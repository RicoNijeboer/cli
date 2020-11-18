<?php

namespace App\Scss;

use App\Models\Css\CssGroup;
use App\Models\Css\CssLine;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Class CreateAndAddVariables
 *
 * @package App\Scss
 */
class Varializer
{

    const MAIN_GROUP_REGEX = '/{[\'\w\s\.&:\~\(\)\[\]\{\}=",|#;%@$-]*}/m';
    /** @var string */
    protected $filePath;
    /** @var string */
    protected $extension;
    /** @var string */
    protected $originalContent;
    /** @var string */
    protected $variables = [];
    /** @var string[] */
    private $rules;

    public function __construct(string $filePath, array $rules)
    {
        $this->rules = $rules;
        $this->filePath = $filePath;

        $path = explode('.', $filePath);

        $this->extension = array_pop($path);

        unset($path);
    }

    public static function from(string $filePath, array $rules): self
    {
        return new static($filePath, $rules);
    }

    public function softCommit(array $variables = []): array
    {
        $this->variables = $variables;

        $content = file_get_contents($this->filePath);

        if ($this->extension === 'vue')
        {
            $content = $this->extractStylesheetFromHTML($content);
        }

        $this->originalContent = $content;

        $this->softCommitGroups($content);

        return $this->variables;
    }

    public function softCommitGroups(string $content, CssGroup $parent = null): void
    {
        $blocks = $this->readBlocks($content);

        foreach ($blocks as $k => $block)
        {
            if (empty($block['content']))
            {
                continue;
            }

            $selector = preg_replace(self::MAIN_GROUP_REGEX, '', $block['content']);

            $group = new CssGroup($this->filePath, $selector, $block, $this);

            if ( ! empty($parent))
            {
                $group->prefixSelector($parent->selector);
                $group->parent = $parent;
                $parent->children[] = $group;
            }

            if ($group->getSelectorSlug() === 'form-check-position-absolute-top-2px-left-0-display-block-border-1px-solid-aaaaaa-border-radius-10px-width-20px-height-20px-webkit-transition-border-25s-linear-transition-border-25s-linear-container-form-container-before-content-position-absolute-top-6px-left-6px-display-block-margin-auto-border-radius-4px-width-8px-height-8px-webkit-transition-background-0-25s-linear-transition-background-0-25s-linear')
            {
                file_put_contents(__DIR__ . '/__.scss', $group->block['content']);
                file_put_contents(__DIR__ . '/__2.scss', $parent->parent->block['content']);
                dd('', '', $this->filePath);
            }

            $group->lines->each(function (CssLine $line) use (&$group) {
                if ( ! in_array($line->rule, $this->rules))
                {
                    return;
                }

                $variableName = $this->getVariableName($group, $line);
                $variable = $variableName . ' : ' . $line->value;
                $i = 1;

                while (Arr::has($this->variables, $variableName)
                    && Arr::get($this->variables, $variableName) === $variable)
                {
                    $variableName = $this->getVariableName($group, $line, $i);
                    $variable = $variableName . ' : ' . $line->value;
                    $i++;
                }

                $line->setVariableName($variableName);

                $this->variables[$variableName] = $variable;
            });
            $group->softCommit();

            $group->softCommitChildren();
        }
    }

    protected function readBlocks(string $content, int $startingIndent = 0): array
    {
        $lines = explode(PHP_EOL, $content);
        $groups = [];
        $currentGroup = 0;
        $currentIndent = $startingIndent;
        $selectorEnded = true;
        $lineNr = 0;

        foreach ($lines as $line)
        {
            $lineNr++;

            if (empty(trim($line)))
            {
                if (isset($groups[$currentGroup]))
                {
                    $groups[$currentGroup]['content'] .= PHP_EOL . PHP_EOL . $line;
                }
                continue;
            }

            if ($currentIndent === 0 && Str::contains($line, ';'))
            {
                continue;
            } elseif ($currentIndent === 0)
            {
                $groups[$currentGroup] = [
                    'content'   => ($groups[$currentGroup] ?? ''),
                    'startLine' => $lineNr,
                ];
                $groups[$currentGroup]['content'] .= PHP_EOL . $line;
                $selectorEnded = Str::contains($line, '{');
                $currentIndent++;
                continue;
            }

            if ( ! $selectorEnded && Str::contains($line, '{'))
            {
                $selectorEnded = true;
                $groups[$currentGroup]['content'] .= PHP_EOL . $line;
                continue;
            } elseif (Str::contains($line, '{'))
            {
                $currentIndent++;
            }

            if (Str::contains($line, '}'))
            {
                $currentIndent--;
            }

            $groups[$currentGroup]['content'] .= PHP_EOL . $line;

            if ($currentIndent === 0)
            {
                $groups[$currentGroup]['endLine'] = $lineNr;
                $currentGroup++;
            }
        }

        $groups[$currentGroup]['endLine'] = $lineNr;

        return array_map(function (array $block) {
            $block['content'] = trim(
                str_replace(PHP_EOL . PHP_EOL, PHP_EOL, $block['content'] ?? '')
            );

            return $block;
        }, $groups);
    }

    /**
     * @param CssGroup $group
     * @param CssLine  $line
     * @param int|null $i
     *
     * @return string
     */
    private function getVariableName(CssGroup $group, CssLine $line, int $i = null): string
    {
        return implode('-', array_filter(
            [
                '$' . $group->getSelectorSlug(),
                $line->rule,
                $i,
            ]
        ));
    }

    private function extractStylesheetFromHTML(string $html)
    {
        $styleHtml = substr($html, strpos($html, '<style'));
        $stylesheet = str_replace('</style>', '', $styleHtml);
        $stylesheet = preg_replace('/<style[\w\s="\']*>/m', '', $stylesheet);

        return $stylesheet;
    }
}