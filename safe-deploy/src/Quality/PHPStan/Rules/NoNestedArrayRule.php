<?php

declare(strict_types=1);

namespace SafeDeploy\Quality\PHPStan\Rules;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Scalar\String_;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;
use PHPStan\ShouldNotHappenException;

/**
 * @implements Rule<ArrayDimFetch>
 */
class NoNestedArrayRule implements Rule
{
    public function getNodeType(): string
    {
        return ArrayDimFetch::class;
    }

    /**
     * @param  ArrayDimFetch  $node
     * @return list<RuleError>
     *
     * @throws ShouldNotHappenException
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if ($node->var instanceof ArrayDimFetch) {
            $keys = $this->extractKeys($node);
            $keyPath = implode('.', $keys);

            return [
                RuleErrorBuilder::message(
                    sprintf(
                        'Nested arrays are not allowed. Use Arr::get($array, "%s") instead of $array%s.',
                        $keyPath,
                        $this->formatArrayAccess($keys)
                    )
                )
                    ->identifier('noNestedArrays.prohibited')
                    ->build(),
            ];
        }

        return [];
    }

    /**
     * @return array<int, string>
     */
    private function extractKeys(ArrayDimFetch $node): array
    {
        /** @var array<int, string> $keys */
        $keys = [];
        $currentNode = $node;

        while ($currentNode instanceof ArrayDimFetch) {
            if ($currentNode->dim instanceof String_) {
                $keys[] = $currentNode->dim->value;
                $currentNode = $currentNode->var;

                continue;
            }

            if ($currentNode->dim instanceof Expr) {
                $keys[] = '?';
            }

            $currentNode = $currentNode->var;
        }

        return array_reverse($keys);
    }

    /**
     * @param  array<int, string>  $keys
     */
    private function formatArrayAccess(array $keys): string
    {
        $result = '';
        foreach ($keys as $key) {
            $result .= "['{$key}']";
        }

        return $result;
    }
}
