<?php

namespace App\Js\Vue;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Class ParsesWatchers
 *
 * @package App\Js\Vue
 */
trait ParsesWatchers
{

    protected function parseWatcher(string $watcherString): array
    {
        $watcher = [
            'name' => $this->getWatchedAttribute($watcherString),
        ];

        $handlerMethod = Str::camel('on_' . str_replace('.', '_', $watcher['name']) . '_changed');

        if ($this->isMethodWatcher($watcherString))
        {
            $watcher['handler'] = $this->getHandlerFromMethodWatcher($watcherString);
        } else
        {
            $configurationLines = $this->getConfiguration($watcherString);

            while (count($configurationLines) > 0)
            {
                $line = array_shift($configurationLines);

                $this->readWatchHandler($watcher, $line, $configurationLines);
            }
        }

        $watcher['handler'] = <<<JS
@Watch('{$watcher['name']}')
{$handlerMethod}{$watcher['handler']}
JS;

        return $watcher;
    }

    protected function getWatchedAttribute(string $watcherString): string
    {
        if ($this->isMethodWatcher($watcherString))
        {
            $matches = $this->pregMatch($watcherString, 'name.method');
        } else
        {
            $matches = $this->pregMatch($watcherString, 'name.configured');
        }

        return $matches[1];
    }

    protected function isMethodWatcher(string $watcherString): bool
    {
        return preg_match($this->getWatcherRegex('name.method'), $watcherString);
    }

    protected function getHandlerFromMethodWatcher(string $watcherString): string
    {
        $matches = $this->pregMatch($watcherString, 'method.handler');

        return $matches[0];
    }

    protected function readWatchHandler(array &$watcher, string $line, array &$parts): void
    {
        if ( ! Str::startsWith($line, 'handler'))
        {
            return;
        }

        $watcher['handler'] = $line;

        $equalCurlies = substr_count($watcher['handler'], '{') === substr_count($watcher['handler'], '}');
        $equalBlockies = substr_count($watcher['handler'], '[') === substr_count($watcher['handler'], ']');

        while ( ! ($equalCurlies && $equalBlockies))
        {
            $watcher['handler'] .= trim(array_shift($parts));

            $equalCurlies = substr_count($watcher['handler'], '{') === substr_count($watcher['handler'], '}');
            $equalBlockies = substr_count($watcher['handler'], '[') === substr_count($watcher['handler'], ']');
        }

        $watcher['handler'] = substr($watcher['handler'], strlen('handler'));
    }

    protected function getConfiguration(string $watcherString): array
    {
        $matches = $this->pregMatch($watcherString, 'configured.configuration');

        $configuration = explode(',', $matches[1]);

        return array_filter($configuration);
    }

    private function pregMatch(string $subject, string $regex)
    {
        preg_match(
            $this->getWatcherRegex($regex),
            $subject,
            $matches
        );

        return $matches;
    }

    private function getWatcherRegex(string $regex): string
    {
        return Arr::get(
            [
                'name'       => [
                    'method'     => '/^\'?([\w.]*)\'?\([\w,{}\[\]:.\s]*\)/m',
                    'configured' => '/^\'?([\w.]*)\'?:/m',
                ],
                'configured' => [
                    'configuration' => '/{([\w\s.;{}\[\]:,\'"\(\)]*)}/m',
                ],
                'method'     => [
                    'handler' => '/\([\w,{}\[\]:.\s]*\)\s?{([\w\s.;{}\[\]:,\'"\(\)]*)}/m',
                ],
            ],
            $regex
        );
    }
}