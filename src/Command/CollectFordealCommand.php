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
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[AsCommand('app:collect:fordeal', 'Collect fordeal data.')]
class CollectFordealCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly MediaManagerInterface $mediaManager,
        private readonly HttpClientInterface $httpClient,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('id', null, InputOption::VALUE_REQUIRED, 'Product ID.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $id = $input->getOption('id');
        if (!is_numeric($id)) {
            throw new \InvalidArgumentException('The id option cannot be empty.');
        }

        $options = [
            'headers' => ['Accept-Language' => 'en,zh;q=0.9,en;q=0.8'],
        ];

        $response = $this->httpClient->request('GET', \sprintf('https://www.fordeal.com/detail/%s', $id), $options);
        $content = $response->getContent();

        $crawler = new Crawler($content);
        $scriptTag = $crawler->filter('script#__F_STATE__');

        /** @var array */
        $data = json_decode($scriptTag->text(), true);

        $entity = new Product();
        $entity->setName($data['detail']['itemDetail']['realTitle']);
        $entity->setSummary($data['detail']['itemDetail']['title']);
        $entity->setImg($this->handleMedia($data['detail']['itemDetail']['displayImage']));

        foreach ($data['sku']['skuAttrs'] as $key => $value) {
            $multipleValues = \count($value['nValue']) > 1;
            $productOption = new ProductOption($value['title']);
            foreach ($value['nValue'] as ['key' => $code, 'value' => $text]) {
                if ($multipleValues && 0 === $key) {
                    $imgUrl = array_find($data['sku']['skus'], static fn (array $item) => \in_array($code, $item['attr']))['image'] ?? null;
                    $img = $imgUrl ? $this->handleMedia($imgUrl) : null;
                } else {
                    $img = null;
                }

                $productOption->addValue(new ProductOptionValue($code, $text, $img));
            }

            $entity->addOption($productOption);
        }

        foreach ($entity->generateChoices() as $choice) {
            $variant = new ProductVariant($choice);

            $result = array_find($data['sku']['skus'], static function (array $item) use ($choice): bool {
                sort($item['attr']);

                return $choice->code === implode('-', $item['attr']);
            });

            if ($result) {
                $variant->setPrice((int) ($result['calcPrice'] * 100));
                $variant->setEnabled(true);
            } else {
                $variant->setEnabled(false);
            }

            $entity->addVariant($variant);
        }

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return Command::SUCCESS;
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
