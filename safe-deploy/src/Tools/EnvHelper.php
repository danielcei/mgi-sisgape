<?php

declare(strict_types=1);

namespace SafeDeploy\Tools;

use Illuminate\Support\Arr;

class EnvHelper
{
    const int EXPLODE_LIMIT = 2;

    protected string $envPath;

    /**
     * @var array<string, string>
     */
    protected array $lines;

    public function __construct(?string $envPath = null)
    {
        $this->envPath = $envPath ?? base_path('.env');
        $this->load();
    }

    public function exists(string $key): bool
    {
        return $this->getKey($key) !== null;
    }

    public function getKey(string $key): ?string
    {
        foreach ($this->lines as $line) {
            $parsed = $this->parseLine($line);

            if ($parsed !== null && Arr::get($parsed, 'key') === $key) {
                $value = Arr::get($parsed, 'value');

                return is_string($value) ? $value : null;
            }
        }

        return null;
    }

    /**
     * @return array<string, string>
     */
    public function getKeys(): array
    {
        $result = [];
        foreach ($this->lines as $line) {
            $parsed = $this->parseLine($line);
            if (is_array($parsed)) {
                $result[$parsed['key']] = $parsed['value'];
            }
        }

        return $result;
    }

    public function setKey(string $key, ?string $value): void
    {
        $found = false;
        foreach ($this->lines as $index => $line) {
            $parsed = $this->parseLine($line);
            if ($parsed !== null && Arr::get($parsed, 'key') === $key) {
                $this->lines[$index] = $key.'='.$value;
                $found = true;
            }
        }

        if (! $found) {
            $this->lines[$key] = $key.'='.$value;
        }

        file_put_contents($this->envPath, implode(PHP_EOL, $this->lines));
    }

    protected function load(): void
    {
        if (! file_exists($this->envPath)) {
            $this->lines = [];

            return;
        }

        $content = file_get_contents($this->envPath);
        if ($content === false) {
            $this->lines = [];

            return;
        }

        $lines = preg_split('/\r\n|\r|\n/', $content);

        $this->lines = is_array($lines)
            ? array_combine(array_map('strval', array_keys($lines)), array_map('trim', $lines))
            : [];
    }

    /**
     * @return array{key: string, value: string}|null
     */
    protected function parseLine(string $line): ?array
    {
        $line = trim($line);

        if ($line === '' || str_starts_with($line, '#')) {
            return null;
        }

        $parts = explode('=', $line, self::EXPLODE_LIMIT);
        if (count($parts) !== self::EXPLODE_LIMIT) {
            return null;
        }

        return [
            'key' => trim($parts[0]),
            'value' => trim($parts[1]),
        ];
    }
}
