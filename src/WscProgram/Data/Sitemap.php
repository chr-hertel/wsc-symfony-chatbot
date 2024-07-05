<?php

declare(strict_types=1);

namespace App\WscProgram\Data;

final class Sitemap
{
    /**
     * @var list<Url>
     */
    private array $urls = [];

    /**
     * @return list<Url>
     */
    public function getUrls(): array
    {
        return $this->urls;
    }

    /**
     * @param list<Url> $urls
     */
    public function setUrl(array $urls): void
    {
        $this->urls = $urls;
    }
}
