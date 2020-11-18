<?php

namespace App\Models\Css;

use App\Scss\Varializer;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CssGroup
{

    const MAIN_GROUP_CONTENT_REGEX = '/{([\w\s&:\(\)\[\]\{\}="\',.%#;@$~|-]*)}/m';

    /** @var string */
    public $selector;
    /** @var string */
    public $body;
    /** @var int */
    public $startLine;
    /** @var int */
    public $endLine;
    /** @var Collection|CssLine[] */
    public $lines;
    /** @var CssGroup[] */
    public $children = [];
    /** @var string */
    public $originalSelector;
    /** @var string */
    public $block;
    /** @var Varializer */
    private $extractor;
    /** @var bool */
    private $isMediaQueried;
    /** @var string */
    private $mediaQuery;
    /** @var string */
    private $file;

    public function __construct(string $file, string $selector, array $block, Varializer $extractor)
    {
        $this->file = $file;
        $this->block = $block;
        $this->startLine = $block['startLine'];
        $this->endLine = $block['endLine'];
        $this->selector = trim(
            str_replace(PHP_EOL, '', $selector)
        );
        $this->originalSelector = $this->selector;
        $this->extractor = $extractor;
        $this->isMediaQueried = false;

        preg_match(self::MAIN_GROUP_CONTENT_REGEX, $block['content'], $contents);

        $this->body = $contents[1] ?? '';

        $lines = preg_split('/;\s/', preg_replace(Varializer::MAIN_GROUP_REGEX, '', $this->body));

        $lines = array_map(function (string $line, int $index) use ($lines) {
            return trim($index !== count($lines) - 1 ? $line . ';' : $line);
        }, $lines, array_keys($lines));

        $lines = array_filter($lines, function (string $line) {
            return Str::contains($line, ':');
        });

        $this->lines = collect(
            array_map(function (string $line) {
                return new CssLine(substr($line, 0, strlen($line) - 1));
            }, $lines)
        );

        if (Str::startsWith($this->selector, '@media'))
        {
            $this->mediaQuery = trim(
                str_replace('@media', '', $this->selector)
            );
            $this->selector = '';
            $this->isMediaQueried = true;
        }
    }

    public function prefixSelector(string $prefix)
    {
        $prefixHasCommas = Str::contains($prefix, ',');
        $selectorHasCommas = Str::contains($this->selector, ',');

        if ($prefixHasCommas && ! $selectorHasCommas)
        {
            $this->selector = trim(
                collect(explode(',', $prefix))
                    ->map(function ($prefix) {
                        if (Str::contains($this->selector, '&'))
                        {
                            return str_replace('&', $prefix, $this->selector);
                        }

                        return implode(' ', array_filter(
                            [
                                $prefix,
                                $this->selector,
                            ]
                        ));
                    })
                    ->sort()
                    ->implode(',' . PHP_EOL)
            );
        } elseif ($selectorHasCommas && ! $prefixHasCommas)
        {
            $this->selector = trim(
                collect(explode(',', $this->selector))
                    ->map(function ($selector) use ($prefix) {
                        if (Str::contains($this->selector, '&'))
                        {
                            return str_replace('&', $prefix, $this->selector);
                        }

                        return implode(' ', array_filter(
                            [
                                $prefix,
                                $this->selector,
                            ]
                        ));
                    })
                    ->sort()
                    ->implode(',' . PHP_EOL)
            );
        } else
        {
            if (Str::contains($this->selector, '&'))
            {
                $this->selector = str_replace('&', $prefix, $this->selector);
            } else
            {
                $this->selector = implode(' ', array_filter(
                    [
                        $prefix,
                        $this->selector,
                    ]
                ));
            }
        }

        foreach ($this->children as $child)
        {
            $child->prefixSelector($prefix);
        }
    }

    public function softCommitChildren()
    {
        $this->extractor->softCommitGroups($this->body, $this);
    }

    public function softCommit(): void
    {
        $softFileName = $this->getSoftFileName();

        $res = $this->lines->reduce(function (string $content, CssLine $line) {
            if (empty($line->variableName))
            {
                return $content;
            }

            return str_replace(
                $line->original,
                str_replace($line->value, $line->variableName, $line->original),
                $content
            );
        }, $this->block['content']);

        $content = str_replace($this->block['content'], $res, $this->getContent());

        if (file_exists($softFileName))
        {
            unlink($softFileName);
        }
        file_put_contents($softFileName, $content);
    }

    public function getSelectorSlug(): string
    {
        $sluggable = implode(' ', [
            $this->isMediaQueried ? $this->mediaQuery : '',
            $this->selector,
        ]);

        $sluggable = \str_replace('.', ' ', $sluggable);
        $sluggable = preg_replace('/[#:\[\]=\"\']/m', ' ', $sluggable);
        $sluggable = str_replace('*', 'everything', $sluggable);

        return Str::slug($sluggable);
    }

    public function toArray(): array
    {
        $array = [];

        foreach (get_class_vars(self::class) as $key => $_)
        {
            if ($key === 'cssLines')
            {
                $array[$key] = array_map(function (CssLine $line) {
                    return $line->toArray();
                }, $this->{$key});
                continue;
            }

            $array[$key] = $this->{$key};
        }

        return $array;
    }

    private function getSoftFileName(): string
    {
        $softFileName = explode('.', $this->file);
        $extension = array_pop($softFileName);
        $softFileName[] = '__soft__';
        $softFileName[] = $extension;
        $softFileName = implode('.', $softFileName);

        return $softFileName;
    }

    private function getContent(): string
    {
        $softFileName = $this->getSoftFileName();

        if (file_exists($softFileName))
        {
            return file_get_contents($softFileName);
        }

        return file_get_contents($this->file);
    }
}