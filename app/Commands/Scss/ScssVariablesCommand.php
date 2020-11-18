<?php

namespace App\Commands\Scss;

use App\Scss\Varializer;
use Illuminate\Support\Facades\File;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\Command;
use ScssPhp\ScssPhp\Compiler;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class ScssColorsCommand
 *
 * @package App\Commands\Scss
 */
class ScssVariablesCommand extends Command
{

    protected $signature = 'scss:variables
                            { --D|dir= : Directory to run, takes the current path as default }
                            { --o|out=_generated-variables.scss : The file to put the generated variables in, OVERWRITES EXISTING FILE! }
                            { --r|rule=* : The CSS rules to convert into variables}';

    public function handle()
    {
        $rules = $this->option('rule');

        if (count($rules) === 0)
        {
            $this->error('Please provide at least one rule which should be converted into variables!');

            return -1;
        }

        if ( ! $this->ensureContinueIsOk())
        {
            return -1;
        }

        $variables = $this->getFiles()
                          ->reduce(function (array $variables, string $path) {
                              $filename = basename($path);
                              $this->info("Soft committing [{$filename}]");

                              return Varializer::from($path, $this->option('rule'))
                                               ->softCommit($variables);
                          }, []);

        file_put_contents($this->getOutputPath(), implode(';' . PHP_EOL, $variables) . ';');

        if ($this->confirm('Check the \'__soft__\' files, do you want to commit to the variable-side?', true))
        {
            collect(File::allFiles($this->getBaseDirectory(), true))
                ->filter(function (SplFileInfo $file) {
                    return Str::contains($file->getFilename(), '__soft__');
                })
                ->each(function (SplFileInfo $file) {
                    $path = $file->getRealPath();

                    File::move($path, str_replace('.__soft__', '', $path));
                });
        }

        $this->info("Make sure to import the '" . basename($this->getOutputPath(), '.scss') . "' file to use the scss variables.");

        return 1;
    }

    private function ensureContinueIsOk()
    {
        $outputFile = $this->getOutputPath();

        if (file_exists($outputFile))
        {
            $confirmed = $this->confirm('Are you sure you want to overwrite that output file?', false);

            if ($confirmed)
            {
                unlink($outputFile);
            }

            return $confirmed;
        }

        return true;
    }

    private function getBaseDirectory()
    {
        return $this->parsePath($this->option('dir'));
    }

    private function parsePath(?string $path = '')
    {
        $pwd = getcwd();

        if (empty($path))
        {
            return $pwd;
        }

        if ( ! Str::startsWith(DIRECTORY_SEPARATOR, $path))
        {
            $path = $pwd . DIRECTORY_SEPARATOR . $path;
        }

        return $path;
    }

    private function getOutputPath(): string
    {
        return $this->parsePath($this->option('out'));
    }

    private function getOutputExtension(): string
    {
        $path = explode('.', $this->option('out'));

        return array_pop($path);
    }

    private function getFiles(): LazyCollection
    {
        return LazyCollection::make(function () {
            $files = array_filter(File::allFiles($this->getBaseDirectory(), true), function (SplFileInfo $file) {
                if (Str::contains($file->getFilename(), '__soft__'))
                {
                    unlink($file->getRealPath());

                    return false;
                }

                return in_array($file->getExtension(), ['vue', 'scss']);
            });

            /** @var SplFileInfo $file */
            foreach ($files as $file)
            {
                yield $file->getRealPath();
            }
        });
    }
}