<?php

namespace App\Js\Vue;

use Illuminate\Support\Arr;

/**
 * Class ParsesComputedValues
 *
 * @package App\Js\Vue
 */
trait ParsesComputedValues
{

    protected function isMethodComputed(string $computed): bool
    {
        return preg_match($this->getComputedRegex('name.method'), $computed);
    }

    protected function getComputedName(string $computedString): string
    {
        $regex = $this->getComputedRegex(
            $this->isMethodComputed($computedString)
                ? 'name.method'
                : 'name.computed'
        );

        preg_match($regex, $computedString, $matches);

        return $matches[1];
    }

    protected function extractGetterFromMethodComputed(string $computedString)
    {
        preg_match(
            $this->getComputedRegex('getter.method'),
            $computedString,
            $matches
        );

        return $matches[1];
    }

    protected function extractGetterFromComputed(string $computedString)
    {
        preg_match(
            $this->getComputedRegex('getter.computed'),
            $computedString,
            $matches
        );

        $content = $matches[1];

        return $this->extractGetterFromMethodComputed($content);
    }

    protected function parseComputed(string $computedString): array
    {
        $computed = [
            'name' => $this->getComputedName($computedString),
        ];

        if ($this->isMethodComputed($computedString))
        {
            $computed['getter'] = <<<JS
get {$computed['name']}() {
    {$this->extractGetterFromMethodComputed($computedString)}
}
JS;
        } else
        {
            $computed['getter'] = <<<JS
get {$computed['name']}() {
    {$this->extractGetterFromComputed($computedString)}
}
JS;
        }

        return $computed;
    }

    private final function getComputedRegex(string $regex): string
    {
        return Arr::get(
            [
                'name'   => [
                    'method'   => '/^([\w]*)\(\)/m',
                    'computed' => '/^([\w]*):/m',
                ],
                'getter' => [
                    'method' => '/{([\w\s.;{}\[\]:,\'"]*)}/m',
                    'computed' => '/{get\(\)\s?([\w\s.;{}\[\]:,\'"\(\)]*)}/m',
                ],
                'setter' => '/{set\(\)\s?([\w\s.;{}\[\]:,\'"]*)}/m',
            ],
            $regex
        );
    }
}