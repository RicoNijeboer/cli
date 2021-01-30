<?php

namespace App\Js;

use App\Files\FileReader;
use Illuminate\Support\Str;

/**
 * Class Reader
 *
 * @package App\Js
 */
class Reader
{

    /** @var VueComponent */
    protected $component;

    /** @var FileReader */
    protected $reader;

    protected $componentStarted = false;
    protected $indent = 0;
    /**
     * @var array
     */
    private $extraLifecycleMethods;

    public function __construct(string $path, array $extraLifecycleMethods = [])
    {
        $this->reader = new FileReader($path);
        $this->component = new VueComponent();
        $this->extraLifecycleMethods = $extraLifecycleMethods;
    }

    public function parseComponent(): VueComponent
    {
        $this->reader->open();

        while ($this->reader->hasNextLine())
        {
            $this->readLine();
        }

        $this->reader->close();

        return $this->component;
    }

    protected function readLine(): void
    {
        $line = $this->reader->getNextLine();

        if ($this->shouldSkipLine($line))
        {
            return;
        }

        if ($this->isStartComponentLine($line))
        {
            $this->componentStarted = true;

            return;
        }

        $this->setIndent($line);

        $this->readImport($line);
        $this->readComponentName($line);
        $this->readComponents($line);
        $this->readData($line);
        $this->readMethods($line);
        $this->readComputedAttributes($line);
        $this->readWatchers($line);
        $this->readLifecycleMethods($line);
    }

    protected function shouldSkipLine(string $line): bool
    {
        return empty($line);
    }

    protected function isImportLine(string $line): bool
    {
        return Str::contains($line, 'import')
            && Str::contains($line, 'from');
    }

    protected function isStartComponentLine(string $line): bool
    {
        return Str::contains($line, 'export default');
    }

    protected function setIndent(string $line)
    {
        preg_match_all('/( ){4}/m', $line, $matches);

        $this->indent = count($matches[0] ?? []);
    }

    protected function readComponentName(string $line)
    {
        $trimmed = trim($line);

        if ($this->indent !== 1 || ! preg_match('/^name:/m', $trimmed))
        {
            return;
        }

        $jsonLine = '{ ' . str_replace(['name', ','], ['"name"', ''], $trimmed) . ' }';

        $this->component->name = json_decode(
            $jsonLine,
            false
        )->name;
    }

    protected function readComponents(string $line)
    {
        $trimmed = trim($line);

        if ($this->indent !== 1 || ! Str::startsWith($trimmed, 'components:'))
        {
            return;
        }

        $trimmed = $this->finishObjectOrArray($trimmed);

        $componentString = explode('}', explode('{', $trimmed, 2)[1], 2)[0];

        $components = $this->trimParts(explode(',', $componentString));

        $this->component->components = array_merge($this->component->components, $components);
    }

    protected function readData(string $line)
    {
        $trimmed = trim($line);

        if ($this->indent !== 1 || ! Str::startsWith($trimmed, 'data()'))
        {
            return;
        }

        $trimmed = $this->finishObjectOrArray($trimmed);

        preg_match_all('/return\s?{([\w:\s,\[\]{}\'"]*)}/m', $trimmed, $matches);

        $dataParts = $this->explodeAndKeep($matches[1][0], ',');
        $dataParts = array_filter(
            $this->flatten(
                array_map(
                    function (string $part) {
                        return explode(PHP_EOL, $part);
                    },
                    $dataParts
                )
            )
        );

        while (count($dataParts) > 0)
        {
            $part = array_shift($dataParts);

            $parts = $this->trimParts(explode(':', $part, 2));

            if (count($parts) !== 2 && count($dataParts) === 0)
            {
                continue;
            } else
            {
                if (count($parts) !== 2)
                {
                    dd($parts, $dataParts, $this->component);
                } else
                {
                    [$name, $value] = $parts;
                    $forceValue = false;

                    if (Str::contains($value, ['[', ']', '{', '}']))
                    {
                        $value = $this->finishDataObjectFromParts($value, $dataParts);
                        $forceValue = true;
                    } else
                    {
                        $value = Str::endsWith($value, ',') ? substr($value, 0, strlen($value) - 1) : $value;

                        eval("\$value = {$value};");
                    }
                }
            }

            $this->component->addData($name, $value, $forceValue);
        }
    }

    protected function readMethods(string $line)
    {
        $trimmed = trim($line);

        if ($this->indent !== 1 || ! Str::startsWith($trimmed, 'methods:'))
        {
            return;
        }

        $trimmed = $this->finishObjectOrArray($trimmed, false, false);

        preg_match_all('/^(methods:\s?{)([\w\(\)\s\{\}\[\].+\'",:;$=>-]*)(},?)$/m', $trimmed, $matches);

        $methodParts = $this->explodeAndKeep($matches[2][0], ',');

        while (count($methodParts) > 0)
        {
            $method = array_shift($methodParts);

            if (Str::contains($method, ['[', ']', '{', '}']))
            {
                $method = $this->finishDataObjectFromParts($method, $methodParts);
            }

            $this->component->addMethod($method);
        }
    }

    protected function readLifecycleMethods(string $line)
    {
        $this->readLifecycleMethod($line, 'beforeCreate');
        $this->readLifecycleMethod($line, 'created');
        $this->readLifecycleMethod($line, 'beforeMount');
        $this->readLifecycleMethod($line, 'mounted');
        $this->readLifecycleMethod($line, 'beforeUpdate');
        $this->readLifecycleMethod($line, 'updated');
        $this->readLifecycleMethod($line, 'beforeDestroy');
        $this->readLifecycleMethod($line, 'destroyed');

        foreach ($this->extraLifecycleMethods as $methodName)
        {
            $this->readLifecycleMethod($line, $methodName, true);
        }
    }

    protected function readLifecycleMethod(string $line, string $methodName, bool $asExtraMethod = false)
    {
        $method = trim($line);

        if ($this->indent !== 1 || ! Str::startsWith($method, "{$methodName}()"))
        {
            return;
        }

        if (Str::contains($method, ['[', ']', '{', '}']))
        {
            $method = $this->finishObjectOrArray($method);
        }

        $method = str_replace(PHP_EOL, '', $method);

        $addMethod = $asExtraMethod ? 'addExtraLifecycleMethod' : 'addLifecycleMethod';
        $this->component->{$addMethod}($method);
    }

    protected function readComputedAttributes(string $line)
    {
        $trimmed = trim($line);

        if ($this->indent !== 1 || ! Str::startsWith($trimmed, 'computed:'))
        {
            return;
        }

        $trimmed = $this->finishObjectOrArray($trimmed, false, false);

        preg_match_all('/^(computed:\s?{)([\w\(\)\s\{\}\[\].+\'",:;$=>-]*)(},?)$/m', $trimmed, $matches);

        $computedParts = $this->explodeAndKeep($matches[2][0], ',');

        while (count($computedParts) > 0)
        {
            $computed = array_shift($computedParts);

            if (Str::contains($computed, ['[', ']', '{', '}']))
            {
                $computed = $this->finishDataObjectFromParts($computed, $computedParts);
            }

            if ( ! empty($computed))
            {
                $this->component->addComputed($computed);
            }
        }
    }

    protected function readWatchers(string $line)
    {
        $trimmed = trim($line);

        if ($this->indent !== 1 || ! Str::startsWith($trimmed, 'watch:'))
        {
            return;
        }

        $trimmed = $this->finishObjectOrArray($trimmed, false, false);

        preg_match_all('/^(watch:\s?{)([\w\(\)\s\{\}\[\].+\'",:;$=>-]*)(},?)$/m', $trimmed, $matches);

        $watchParts = $this->explodeAndKeep($matches[2][0], ',');

        while (count($watchParts) > 0)
        {
            $watch = array_shift($watchParts);

            if (Str::contains($watch, ['[', ']', '{', '}']))
            {
                $watch = $this->finishDataObjectFromParts($watch, $watchParts);
            }

            if ( ! empty($watch))
            {
                $this->component->addWatcher($watch);
            }
        }
    }

    protected function readImport(string $line)
    {
        $trimmed = trim($line);

        if ( ! (Str::startsWith($trimmed, 'import') && Str::contains($trimmed, 'from')))
        {
            return;
        }

        $trimmed = str_replace(['import', ';'], '', $trimmed);

        [$import, $from] = $this->trimParts(explode('from', $trimmed, 2));

        $this->component->addImport($import, $from);
    }

    protected function finishDataObjectFromParts(string $line, array &$parts): string
    {
        $trimmed = trim($line);
        $equalCurlies = substr_count($trimmed, '{') === substr_count($trimmed, '}');
        $equalBlockies = substr_count($trimmed, '[') === substr_count($trimmed, ']');

        while ( ! ($equalCurlies && $equalBlockies))
        {
            $trimmed .= trim(array_shift($parts));

            $equalCurlies = substr_count($trimmed, '{') === substr_count($trimmed, '}');
            $equalBlockies = substr_count($trimmed, '[') === substr_count($trimmed, ']');
        }

        return $trimmed;
    }

    protected function finishObjectOrArray(string $line, bool $array = false, bool $doEOLs = true): string
    {
        $trimmed = trim($line);
        $initialIndent = $this->indent;

        while ( ! (Str::contains($trimmed, $array ? ']' : '}') && $this->indent === $initialIndent))
        {
            $nextLine = $this->reader->getNextLine();

            $this->setIndent($nextLine);

            if ($doEOLs)
            {
                $trimmed .= PHP_EOL;
            }

            $trimmed .= trim($nextLine);
        }

        return $trimmed;
    }

    protected function trimParts(array $parts): array
    {
        return array_map(
            function (string $component): string {
                return trim($component);
            },
            $parts
        );
    }

    protected function flatten(array $multi): array
    {
        $flat = [];

        array_walk_recursive($multi, function ($item) use (&$flat) {
            $flat[] = $item;
        });

        return $flat;
    }

    protected function explodeAndKeep(string $matches, string $separator): array
    {
        $dataParts = explode($separator, $matches);

        return array_map(function (string $part, int $index) use (&$dataParts) {
            if ($index === (count($dataParts) - 1))
            {
                return $part;
            }

            return $part . ',';
        }, $dataParts, array_keys($dataParts));
    }
}