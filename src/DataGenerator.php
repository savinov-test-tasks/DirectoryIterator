<?php
declare(strict_types=1);

namespace DirectoryIterator;

class DataGenerator
{
    private float $nestingProbability;
    private float $escapeNestingProbability;
    private float $countFileSpawnProbability;

    public function __construct(float $nestingProbability, float $escapeNestingProbability,
                                float $countFileSpawnProbability)
    {
        $this->nestingProbability = $nestingProbability;
        $this->escapeNestingProbability = $escapeNestingProbability;
        $this->countFileSpawnProbability = $countFileSpawnProbability;
    }

    private function createCountFile(string $filePath, float $count): bool {
        $countFilePath = $this->appendFilePaths($filePath, 'count');

        if (file_exists($countFilePath)) {
            return false;
        }

        return file_put_contents($countFilePath, $count) !== false;
    }

    private function generateRandomDirectoryName(): string {
        return join('', [(string) rand(0, 1500), '_', (string) rand(0, 1500)]);
    }

    private function createDirectoryIfNotExists(string $path): void {
        if (!is_dir($path)) {
            mkdir($path);
        }
    }

    private function appendFilePaths(string ...$filePaths): string {
        return join(DIRECTORY_SEPARATOR, $filePaths);
    }

    public function generate(string $pathToGenerate, int $dataAmount): float {
        if (is_file($pathToGenerate)) {
            throw new \UnexpectedValueException('File instead of a directory');
        } else if (!is_dir($pathToGenerate)) {
            $this->createDirectoryIfNotExists($pathToGenerate);
        }

        $currentWorkingDirectory = $pathToGenerate;
        $nestingProbability = $this->nestingProbability;

        $totalCountSum = 0;

        for ($it = 0; $it < $dataAmount; $it++) {
            $coinFlip = rand(0, 100) / 100;

            if ($coinFlip < $this->countFileSpawnProbability) {
                $randomCount = rand(0, 1000);

                if ($this->createCountFile($currentWorkingDirectory, $randomCount)) {
                    $totalCountSum += $randomCount;
                }
            }

            $newDirectoryName = $this->generateRandomDirectoryName();
            $newCurrentWorkingDirectory = $this->appendFilePaths($currentWorkingDirectory, $newDirectoryName);
            $this->createDirectoryIfNotExists($newCurrentWorkingDirectory);

            // Here we do decide if we should go one level deeper or higher in directory hierarchy.
            if ($coinFlip < $this->escapeNestingProbability && $pathToGenerate !== $currentWorkingDirectory) {
                $nestingProbability = $this->nestingProbability;
                $currentWorkingDirectory = realpath($this->appendFilePaths($currentWorkingDirectory, '..'));
            } else if ($coinFlip < $nestingProbability) {
                // Lower probability of nesting
                $nestingProbability *= 0.5;
                $currentWorkingDirectory = $newCurrentWorkingDirectory;
            }
        }

        return $totalCountSum;
    }
}
