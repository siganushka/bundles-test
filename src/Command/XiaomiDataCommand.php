<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Product;
use App\Entity\ProductOption;
use App\Entity\ProductOptionValue;
use App\Entity\ProductVariant;
use Doctrine\ORM\EntityManagerInterface;
use Siganushka\MediaBundle\Entity\Media;
use Siganushka\MediaBundle\MediaManagerInterface;
use Siganushka\MediaBundle\Utils\FileUtils;
use Siganushka\ProductBundle\Model\ProductVariantChoice;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand('app:xiaomi:data', 'Add xiaomi data.')]
class XiaomiDataCommand extends Command
{
    private readonly HttpClientInterface $httpClient;

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly MediaManagerInterface $mediaManager,
    ) {
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
            ->addOption('keyword', null, InputOption::VALUE_REQUIRED, 'Search keyword.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $keyword = $input->getOption('keyword');
        if (null === $keyword) {
            throw new \InvalidArgumentException('The keyword option cannot be empty.');
        }

        $output->writeln('<info>请求商品列表...</info>');

        $data = $this->handleRequest('/search/index', [
            'query' => $keyword,
            'page_index' => 1,
            'page_size' => 40,
            'callback' => 'foo',
        ]);

        // $total = $data['total'] ?? 0;
        $list = $data['pc_list'] ?? [];
        $total = \count($list);

        $output->writeln(\sprintf('<info>请求商品列表成功，共计 %d 条记录。</info>', $total));

        $current = $successfully = 0;
        foreach ($list as $value) {
            ++$current;
            $message = \sprintf('[%d/%d] #%d', $current, $total, $value['product_id']);

            $output->writeln(\sprintf('<info>%s: 请求详情...</info>', $message));
            $product = $this->handleRequest('/product/view', [
                'product_id' => $value['product_id'],
            ]);

            $output->writeln(\sprintf('<info>%s: 下载主图</info>', $message));
            $media = $this->handleMedia($product['goods_list'][0]['goods_info']['img_url']);

            $entity = new Product();
            $entity->setImg($media);
            $entity->setName(\sprintf('%s (#%s)', trim($product['product_info']['name']), $value['product_id']));

            $buyOption = $product['buy_option'] ?? [];
            foreach ($buyOption as $v1) {
                if (1 === $v1['prop_cfg_id']) {
                    continue;
                }

                $option = new ProductOption();
                $option->setName($v1['name']);

                foreach ($v1['list'] as $v2) {
                    $option->addValue(new ProductOptionValue(\sprintf('prop_value_id_%d', $v2['prop_value_id']), $v2['name']));
                }

                $entity->addOption($option);
            }

            $variants = [];
            if (\count($buyOption) > 0) {
                foreach ($product['goods_list'] as $v1) {
                    $values = [];
                    foreach ($v1['goods_info']['prop_list'] as $v2) {
                        if (1 === $v2['prop_cfg_id']) {
                            continue;
                        }

                        $values[] = new ProductOptionValue(\sprintf('prop_value_id_%d', $v2['prop_value_id']));
                    }

                    $choice = new ProductVariantChoice(...$values);
                    if ($choice->value) {
                        $variants[$choice->value] = $v1['goods_info'];
                    }
                }
            } else {
                $variants[0] = $product['goods_list'][0]['goods_info'];
            }

            foreach ($entity->generateChoices() as $index => $choice) {
                $key = $choice->value ?? 0;
                if (!isset($variants[$key])) {
                    continue;
                }

                $output->writeln(\sprintf('<info>%s: 下载第 %d 张商品图</info>', $message, $index + 1));
                $variantImg = $this->handleMedia($variants[$key]['img_url']);

                $variant = new ProductVariant($choice);
                $variant->setImg($variantImg);
                $variant->setPrice((int) ($variants[$key]['price'] * 100));
                $variant->setInventory($variants[$key]['sku'] ?? null);

                $entity->addVariant($variant);
            }

            $this->entityManager->persist($entity);

            $output->writeln(\sprintf('<info>%s: 完成</info>', $message));
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

        /** @var array */
        $parsedResponse = json_decode($content, true);
        // dd(__METHOD__, $data);

        return $parsedResponse['data'] ?? [];
    }

    private function handleMedia(string $imgUrl): Media
    {
        if (str_starts_with($imgUrl, '//')) {
            $imgUrl = 'https:'.$imgUrl;
        }

        $meida = $this->mediaManager->save('product_img', FileUtils::createFromUrl($imgUrl));

        $this->entityManager->persist($meida);
        $this->entityManager->flush();

        return $meida;
    }
}
