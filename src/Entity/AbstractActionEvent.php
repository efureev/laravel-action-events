<?php

declare(strict_types=1);

namespace Fureev\ActionEvents\Entity;

use Fureev\ActionEvents\Contracts\ActionEventable;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Str;

abstract class AbstractActionEvent implements ActionEventable
{
    protected ?Authenticatable $user = null;

    /** @var array|null Changed data */
    protected ?array $changedData = null;

    /** @var array|null Original data */
    protected ?array $originalData = null;

    public function getUserId(): mixed
    {
        return $this->user?->getAuthIdentifier();
    }

    public function getChangedData(): ?array
    {
        return $this->changedData;
    }

    public function setChangedData(?array $data): static
    {
        $this->changedData = $data;

        return $this;
    }

    public function getOriginalData(): ?array
    {
        return $this->originalData;
    }

    public function setOriginalData(?array $data): static
    {
        $this->originalData = $data;

        return $this;
    }


    public function setUser(?Authenticatable $user = null): static
    {
        $this->user = $user;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'name'      => $this->getName(),
            'user_id'   => $this->getUserId(),
            'type'      => $this->getType(),
            'status'    => $this->getStatus(),
            'thread_id' => $this->buildTread(),
            'changes'   => $this->getChangedData(),
            'original'  => $this->getOriginalData(),
        ];
    }

    protected function buildTread(): string
    {
        return (string)Str::orderedUuid();
    }
}
