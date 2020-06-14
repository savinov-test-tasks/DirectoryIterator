<?php
declare(strict_types=1);

namespace DirectoryIterator;


class CountFinder
{
    public static function find(string $path): float {
        $result = 0;

        $directoryIterator = new \RecursiveDirectoryIterator($path);
        $recursiveIterator = new \RecursiveIteratorIterator($directoryIterator);

        /** @var \RecursiveDirectoryIterator $iterator */
        foreach ($recursiveIterator as $iterator) {
            if ($iterator->isFile() && $iterator->getFilename() === 'count') {
                $result += (float) (file_get_contents($iterator->getRealPath()));
            }
        }

        return $result;
    }
}
