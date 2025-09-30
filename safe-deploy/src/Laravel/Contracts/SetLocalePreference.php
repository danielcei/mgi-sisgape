<?php

declare(strict_types=1);

namespace SafeDeploy\Laravel\Contracts;

interface SetLocalePreference
{
    public function setLocalePreference(string $locale): void;
}
