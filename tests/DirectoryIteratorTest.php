<?php
declare(strict_types=1);

namespace DirectoryIterator\Tests\GeneratorTest;

use DirectoryIterator\CountFinder;
use PHPUnit\Framework\TestCase;

class DirectoryIteratorTest extends TestCase
{
    public function testShouldFindRightCount()
    {
        $path = join('/', [getcwd(), 'tmp']);
        $dataGenerator = new \DirectoryIterator\DataGenerator(0.25, 0.2, 0.3);

        $totalCount = $dataGenerator->generate($path, 5000);

        $totalCountFinder = CountFinder::find($path);

        $this->assertEquals($totalCount, $totalCountFinder);

        if (PHP_OS === 'Linux') {
            shell_exec('rm -fr ' . $path);
        }
    }
}
