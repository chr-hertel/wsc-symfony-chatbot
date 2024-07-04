<?php

declare(strict_types=1);

namespace App\WscProgram;

use Symfony\Component\Filesystem\Filesystem;

final class Dumper
{
    public function __construct(
        private readonly Loader $loader,
        private readonly Filesystem $filesystem,
        private readonly string $programFile,
    ) {
    }

    public function dumpProgram(): void
    {
        $text = 'Web Summer Camp Program 2024:'.PHP_EOL;

        foreach ($this->loader->loadProgram()->sessions as $session) {
            $text .= $session->toString().PHP_EOL;
        }

        $this->filesystem->dumpFile($this->programFile, $text);
    }
}
