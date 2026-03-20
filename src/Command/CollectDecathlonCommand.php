<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Product;
use App\Entity\ProductOption;
use App\Entity\ProductOptionValue;
use App\Entity\ProductVariant;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\CacheItemPoolInterface;
use Siganushka\MediaBundle\Entity\Media;
use Siganushka\MediaBundle\MediaManagerInterface;
use Siganushka\MediaBundle\Utils\FileUtils;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand('app:collect:decathlon', 'Collect decathlon data.')]
class CollectDecathlonCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly MediaManagerInterface $mediaManager,
        private readonly HttpClientInterface $httpClient,
        private readonly CacheItemPoolInterface $cachePool,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('dsm_code', null, InputOption::VALUE_REQUIRED, 'DSM code.')
            ->addOption('category_id', null, InputOption::VALUE_REQUIRED, 'Category id.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $dsmCode = $input->getOption('dsm_code');
        $categoryId = $input->getOption('category_id');
        if (\is_string($dsmCode)) {
            $this->handleProduct($output, $dsmCode);
        } elseif (\is_string($categoryId)) {
            $this->handleCategory($output, $categoryId);
        } else {
            throw new \RuntimeException('Nothing todo.');
        }

        $this->entityManager->flush();

        return Command::SUCCESS;
    }

    private function handleCategory(OutputInterface $output, string $categoryId): void
    {
        $options = [
            'headers' => ['Authorization' => \sprintf('Bearer %s', $this->getToken())],
        ];

        $response = $this->httpClient->request('GET', \sprintf('https://mpm-store.decathlon.com.cn/wcc_bff/api/v1/easymerch/plp/product_lists?category_id=%s&page_size=100', $categoryId), $options);
        $data = $response->toArray(false)['data']['records']['products'] ?? [];

        $total = \count($data);
        $output->writeln(\sprintf('<info>请求商品列表成功，共计 %d 条记录。</info>', $total));

        $current = $successfully = 0;
        foreach ($data as $value) {
            ++$current;
            $message = \sprintf('[%d/%d] #%d', $current, $total, $value['dsm_code']);

            try {
                $this->handleProduct($output, $value['dsm_code']);
            } catch (\Throwable $th) {
                $output->writeln(\sprintf('<info>%s: 出错（%s）</info>', $message, $th->getMessage()));
                continue;
            }

            $output->writeln(\sprintf('<info>%s: 完成</info>', $message));
            ++$successfully;
        }
    }

    private function handleProduct(OutputInterface $output, string $dsmCode): void
    {
        $options = [
            'headers' => ['Authorization' => \sprintf('Bearer %s', $this->getToken())],
        ];

        $response = $this->httpClient->request('GET', \sprintf('https://mpm-store.decathlon.com.cn/wcc_bff/api/v1/product/data?dsm_code=%s', $dsmCode), $options);
        $data = $response->toArray(false)['data']['dsm'] ?? null;

        if (!$data) {
            throw new \RuntimeException('Data not found.');
        }

        $models = $data['models'] ?? [];
        $items = $models[0]['items'] ?? [];

        $entity = new Product();
        $entity->setImg($this->handleMedia($models[0]['media']['images'][0]['image_url']));
        $entity->setName(array_first(explode(' ', $models[0]['web_label'])));
        $entity->setSummary($data['designed_for']);

        if (\count($models) > 1) {
            $option = new ProductOption('颜色');
            foreach ($models as $model) {
                $label = trim(str_replace('/-', ' ', $model['color_label'] ?? ''));
                if (empty($label)) {
                    $label = '-';
                }

                $option->addValue(new ProductOptionValue($model['model_code'], $label, $this->handleMedia($model['media']['images'][0]['image_url'])));
            }

            $entity->addOption($option);
        }

        if (\count($items) > 1) {
            $option = new ProductOption('尺码');
            foreach ($items as $item) {
                $option->addValue(new ProductOptionValue($item['item_code'], $item['size_label']));
            }

            $entity->addOption($option);
        }

        foreach ($entity->generateChoices() as $choice) {
            $variant = new ProductVariant($choice);
            $variant->setPrice((int) ($items[0]['price']['active_price'] * 100));
            $variant->setEnabled(true);

            $entity->addVariant($variant);
        }

        $this->entityManager->persist($entity);
    }

    private function getToken(): string
    {
        $item = $this->cachePool->getItem(__METHOD__);
        if ($item->isHit() && \is_string($data = $item->get())) {
            return $data;
        }

        $response = $this->httpClient->request('GET', 'https://www.decathlon.com.cn/product-detail?dsm_code=1');
        foreach ($response->getHeaders(false)['set-cookie'] ?? [] as $cookie) {
            if (str_starts_with($cookie, 'token=')) {
                preg_match('/token=([^;]+)/', $cookie, $matches);
                if ($token = $matches[1] ?? null) {
                    $item->set($token);
                    $item->expiresAfter(900);
                    $this->cachePool->save($item);

                    return $token;
                }
            }
        }

        throw new \RuntimeException('Token not found.');
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
