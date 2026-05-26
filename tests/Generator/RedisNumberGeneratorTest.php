<?php

declare(strict_types=1);

namespace App\Tests\Generator;

use App\Generator\RedisNumberGenerator;
use PHPUnit\Framework\TestCase;

class RedisNumberGeneratorTest extends TestCase
{
    protected RedisNumberGenerator $numberGenerator;

    protected function setUp(): void
    {
        $this->numberGenerator = new RedisNumberGenerator(redisKey: 'test:number:generator');
    }

    public function testGenerate(): void
    {
        $number = $this->numberGenerator->generate();

        static::assertNotEmpty($number);
        static::assertSame(12, mb_strlen($number));
    }

    // public function testPerformance(): void
    // {
    //     $numbers = [];
    //     $count = 100000;

    //     $preTime = microtime(true);
    //     for ($i = 0; $i < $count; ++$i) {
    //         $number = $this->numberGenerator->generate();
    //         $numbers[$number] = 1;

    //         // if ($i < 10) var_dump($number);
    //     }

    //     $postTime = microtime(true);
    //     $execTime = $postTime - $preTime;

    //     echo \sprintf('共计生成 %d 条记录，重复 %d 条，共耗时 %f'.\PHP_EOL, $count, $count - \count($numbers), $execTime);
    //     // static::assertCount($count, $numbers);
    // }
}
