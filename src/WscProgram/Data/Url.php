<?php

declare(strict_types=1);

namespace App\WscProgram\Data;

use Symfony\Component\Serializer\Attribute\SerializedName;

final class Url
{
    #[SerializedName('loc')]
    private string $location;

    #[SerializedName('lastmod')]
    private \DateTimeImmutable $lastModified;

    public function getLocation(): string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getLastModified(): \DateTimeImmutable
    {
        return $this->lastModified;
    }

    public function setLastModified(\DateTimeImmutable $lastModified): self
    {
        $this->lastModified = $lastModified;

        return $this;
    }

    public function isProgramUrl(): bool
    {
        return !str_contains($this->location, '/2023/')
            && (str_contains($this->location, '/talk/') || str_contains($this->location, '/workshop/'));
    }
}
