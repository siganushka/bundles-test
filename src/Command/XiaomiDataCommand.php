<?php

declare(strict_types=1);

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Siganushka\MediaBundle\ChannelRegistry;
use Siganushka\MediaBundle\Entity\Media;
use Siganushka\MediaBundle\Event\MediaSaveEvent;
use Siganushka\ProductBundle\Entity\Product;
use Siganushka\ProductBundle\Entity\ProductOption;
use Siganushka\ProductBundle\Entity\ProductOptionValue;
use Siganushka\ProductBundle\Entity\ProductVariant;
use Siganushka\ProductBundle\Model\ProductVariantChoice;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class XiaomiDataCommand extends Command
{
    protected static $defaultName = 'app:xiaomi:data';
    protected static $defaultDescription = 'Add xiaomi data.';

    private EntityManagerInterface $entityManager;
    private EventDispatcherInterface $eventDispatcher;
    private ChannelRegistry $channelRegistry;
    private HttpClientInterface $httpClient;

    public function __construct(EntityManagerInterface $entityManager, EventDispatcherInterface $eventDispatcher, ChannelRegistry $channelRegistry)
    {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->channelRegistry = $channelRegistry;

        $this->httpClient = HttpClient::create([
            'base_uri' => 'https://api2.order.mi.com',
            'headers' => [
                'Referer' => 'https://www.mi.com/',
            ],
        ]);

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('keyword', null, InputOption::VALUE_REQUIRED, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $keyword = $input->getOption('keyword');
        if (null === $keyword) {
            throw new \InvalidArgumentException('The keyword option cannot be empty.');
        }

        $output->writeln('<info>获取商品数据...</info>');

        $data = $this->handleRequest('/search/index', [
            'query' => $keyword,
            'page_index' => 1,
            // 小米分页最大只能是 40
            'page_size' => 40,
            'callback' => 'foo',
        ]);

        // $total = $data['total'] ?? 0;
        $list = $data['pc_list'] ?? [];

        $output->writeln(sprintf('<info>获取商品数据成功，共计 %d 条记录。</info>', \count($list)));

        $successfully = 0;
        foreach ($list as $value) {
            $output->writeln(sprintf('<info>第 %d 条数据：#%s 开始...</info>', $successfully, $value['product_id']));

            // if ($value['product_id'] != 16677) {
            //     $output->writeln(sprintf('<info>第 %d 条数据：已跳过。</info>', $successfully));
            //     ++ $successfully;
            //     continue;
            // }

            $output->writeln(sprintf('<info>第 %d 条数据：获取详情...</info>', $successfully));
            $product = $this->handleRequest('/product/view', [
                'product_id' => $value['product_id'],
            ]);

            $output->writeln(sprintf('<info>第 %d 条数据：下载主图...</info>', $successfully));
            $productImg = $this->handleUploadMedia('product_img', $product['goods_list'][0]['goods_info']['img_url']);

            $entity = new Product();
            $entity->setImg($productImg);
            $entity->setName(sprintf('%s (#%s)', $product['product_info']['name'], $value['product_id']));

            // ProductOption
            $buyOption = $product['buy_option'] ?? [];
            foreach ($buyOption as $v1) {
                if (1 === $v1['prop_cfg_id']) {
                    continue;
                }

                $option = new ProductOption($v1['name']);
                foreach ($v1['list'] as $v2) {
                    $option->addValue(new ProductOptionValue(sprintf('prop_value_id_%d', $v2['prop_value_id']), $v2['name']));
                }

                $entity->addOption($option);
            }

            $variantsMapping = [];
            if (\count($buyOption) > 0) {
                foreach ($product['goods_list'] as $v1) {
                    $propCfgIds = [];
                    foreach ($v1['goods_info']['prop_list'] as $v2) {
                        if (1 === $v2['prop_cfg_id']) {
                            continue;
                        }

                        $propCfgIds[] = sprintf('prop_value_id_%d', $v2['prop_value_id']);
                    }

                    $key = ProductVariantChoice::generateValue($propCfgIds);
                    $variantsMapping[$key] = $v1['goods_info'];
                }
            } else {
                $variantsMapping[0] = $product['goods_list'][0]['goods_info'];
            }

            foreach ($entity->getChoices(true) as $index => $choice) {
                $key = $choice->value ?? 0;
                if (!isset($variantsMapping[$key])) {
                    continue;
                }

                $output->writeln(sprintf('<info>第 %d 条数据：下载第 %d 张商品图...</info>', $successfully, $index));
                $variantImg = $this->handleUploadMedia('product_variant_img', $variantsMapping[$key]['img_url']);

                $variant = new ProductVariant($entity, $choice);
                $variant->setPrice((int) ($variantsMapping[$key]['price'] * 100));
                $variant->setImg($variantImg);

                $entity->addVariant($variant);
            }

            $this->entityManager->persist($entity);

            $output->writeln(sprintf('<info>第 %d 条数据：已成功。</info>', $successfully));
            ++$successfully;
        }

        $this->entityManager->flush();
        $this->entityManager->clear();

        return Command::SUCCESS;
    }

    private function handleRequest(string $uri, array $query = []): array
    {
        $response = $this->httpClient->request('GET', $uri, compact('query'));

        // JSONP response
        $content = $response->getContent();
        if (str_starts_with($content, 'foo(')) {
            $content = mb_substr($content, 4);
        }

        if (str_ends_with($content, ');')) {
            $content = mb_substr($content, 0, -2);
        }

        $parsedResponse = json_decode($content, true);
        // dd(__METHOD__, $data);

        return $parsedResponse['data'] ?? [];
    }

    private function handleUploadMedia(string $channelAlias, string $imgUrl): ?Media
    {
        if (str_starts_with($imgUrl, '//')) {
            $imgUrl = 'https:'.$imgUrl;
        }

        $channel = $this->channelRegistry->get($channelAlias);
        $event = MediaSaveEvent::createFromUrl($channel, $imgUrl);

        $this->eventDispatcher->dispatch($event);

        $meida = $event->getMedia();
        if (null === $meida) {
            return null;
        }

        $this->entityManager->persist($meida);
        $this->entityManager->flush();

        return $meida;
    }
}
