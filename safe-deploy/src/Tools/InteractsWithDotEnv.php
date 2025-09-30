<?php

declare(strict_types=1);

namespace SafeDeploy\Tools;

trait InteractsWithDotEnv
{
    /**
     * Copies missing keys from one "dot env" file to another.
     */
    protected function copyMissingKeys(string $to = '.env', string $from = '.env.example', bool $needsConfirmation = false): void
    {
        $this->warn("Checking missing keys from {$from} in {$to}...");
        $toKeys = $this->getDotEnv($to)->getKeys();
        $fromKeys = $this->getDotEnv($from)->getKeys();

        $diffKeys = array_diff(array_keys($fromKeys), array_keys($toKeys));

        if ($diffKeys === []) {
            $this->info('No missing keys.');

            return;
        }

        $missingKeysString = implode(', ', $diffKeys);

        if ($needsConfirmation
            && ! $this->confirm("Do you want to add missing keys [{$missingKeysString}] from {$from} into {$to}?")) {
            return;
        }

        if (! $needsConfirmation) {
            $this->info("Adding missing keys: {$missingKeysString}");
        }

        $missingKeys = collect($fromKeys)->filter(static fn ($value, $key): bool => in_array($key, $diffKeys, true));

        $dotEnv = $this->getDotEnv($to);
        foreach ($missingKeys as $key => $details) {
            $value = $this->ask($key, $details);

            /** @var string $key */
            /** @var string|null $value */
            $dotEnv->setKey($key, $value);
            $this->info("Added: {$key}={$value}");
        }
    }

    /**
     * @param  string|null  $file
     */
    protected function getDotEnv($file = null): EnvHelper
    {
        return new EnvHelper($file);
    }
}
