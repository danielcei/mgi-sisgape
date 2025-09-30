<?php

declare(strict_types=1);

namespace SafeDeploy\Laravel\Contracts;

interface Userstampable
{
    public function setCreatedBy(null|int|string $userId): self;

    public function setDeletedBy(null|int|string $userId): self;

    public function setUpdatedBy(null|int|string $userId): self;
}
