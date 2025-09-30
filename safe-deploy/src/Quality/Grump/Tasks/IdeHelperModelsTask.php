<?php

declare(strict_types=1);

namespace SafeDeploy\Quality\Grump\Tasks;

use GrumPHP\Formatter\RawProcessFormatter;
use GrumPHP\Runner\TaskResult;
use GrumPHP\Runner\TaskResultInterface;
use GrumPHP\Task\AbstractExternalTask;
use GrumPHP\Task\Config\ConfigOptionsResolver;
use GrumPHP\Task\Context\ContextInterface;
use GrumPHP\Task\Context\GitPreCommitContext;
use GrumPHP\Task\Context\RunContext;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Collection;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractExternalTask<RawProcessFormatter>
 */
class IdeHelperModelsTask extends AbstractExternalTask
{
    public static function getConfigurableOptions(): ConfigOptionsResolver
    {
        return ConfigOptionsResolver::fromOptionsResolver(new OptionsResolver);
    }

    public function canRunInContext(ContextInterface $context): bool
    {
        return $context instanceof GitPreCommitContext
            || $context instanceof RunContext;
    }

    public function run(ContextInterface $context): TaskResultInterface
    {
        if (! $this->checkDatabaseConnection()) {
            return TaskResult::createFailed(
                $this,
                $context,
                'DB connection is not working. Please check your environment.'
            );
        }

        $fqcns = $this->extractModelClassNames($context);

        if ($fqcns->isEmpty()) {
            return TaskResult::createSkipped($this, $context);
        }

        return $this->callIdeHelper($fqcns, $context);
    }

    /**
     * @param  Collection<int, class-string>  $fqcns
     */
    private function callIdeHelper(Collection $fqcns, ContextInterface $context): TaskResultInterface
    {
        $arguments = $this->processBuilder->createArgumentsForCommand('php');
        $arguments->add('artisan');
        $arguments->add('ide-helper:models');

        $fqcns->each(static fn ($class) => $arguments->add($class));
        $arguments->add('-RW');

        $process = $this->processBuilder->buildProcess($arguments);
        $process->run();

        if ($process->isSuccessful()) {
            return TaskResult::createPassed($this, $context);
        }

        return TaskResult::createFailed(
            $this,
            $context,
            sprintf(
                "Failed to update model doc-blocks for: %s\n%s",
                $fqcns->join(', '),
                $process->getErrorOutput() ?: 'No error output available'
            )
        );
    }

    private function checkDatabaseConnection(): bool
    {
        $arguments = $this->processBuilder->createArgumentsForCommand('php');
        $arguments->add('artisan');
        $arguments->add('db:show');

        $process = $this->processBuilder->buildProcess($arguments);
        $process->run();

        return $process->isSuccessful();
    }

    /**
     * @return Collection<int, class-string>
     */
    private function extractModelClassNames(ContextInterface $context): Collection
    {
        return Collection::make($context->getFiles()->paths([])->toArray())
            ->map(static fn ($file): false|string => $file->getRealPath())
            ->filter(static fn ($file): bool => str_ends_with((string) $file, 'php') && file_exists($file))
            ->map(function ($file): ?string {
                $content = file_get_contents($file);

                if ($content === false) {
                    return null;
                }

                $fileName = pathinfo($file, PATHINFO_FILENAME);

                preg_match('/namespace\s+([^;]+);/', $content, $namespaceMatches);
                $namespace = $namespaceMatches[1] ?? null;

                if ($namespace === null) {
                    return null;
                }

                /** @var class-string $className */
                $className = $namespace.'\\'.$fileName;

                if (
                    ! class_exists($className)
                    || ! $this->isModelOrPivot($className)
                    || ! $this->isInstantiable($className)
                ) {
                    return null;
                }

                return $className;
            })
            ->filter()
            ->values();
    }

    /**
     * @param  class-string  $class
     */
    private function isInstantiable(string $class): bool
    {
        try {
            return new ReflectionClass($class)->isInstantiable();
        } catch (ReflectionException) {
            return false;
        }
    }

    private function isModelOrPivot(string $class): bool
    {
        return is_subclass_of($class, Model::class)
            || is_subclass_of($class, Pivot::class);
    }
}
