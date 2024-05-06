<?php

declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('app:test:openai', description: 'Test if OpenAI API Key looks good')]
final class OpenAiTestCommand extends Command
{
    public function __construct(private ?string $openAiApiKey)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if (empty($this->openAiApiKey)) {
            $io->error('No secret OPENAI_API_KEY found.');

            return Command::FAILURE;
        }

        if (!str_starts_with($this->openAiApiKey, 'sk-') || 56 !== strlen($this->openAiApiKey)) {
            $io->error('OpenAI API Key seems to be invalid.');

            return Command::FAILURE;
        }

        $io->success('OpenAI API Key looks good!');

        return Command::SUCCESS;
    }
}
