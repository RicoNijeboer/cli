<?php

namespace App\Commands\Base;

use App\Commands\Concerns\ExecutesCliCommands;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use LaravelZero\Framework\Commands\BuildCommand;
use LaravelZero\Framework\Commands\Command;

/**
 * Class Install
 *
 * @package App\Commands\Base
 */
class Install extends Command
{

    use ExecutesCliCommands;

    /**
     * @var string
     */
    protected $signature = 'install {bash source : The file you want the PATH variable to be extended in, most likely ~/.bashrc or ~/.zshrc}';

    /**
     * @var string
     */
    protected $description = 'Installs the CLI tool for you by extending the PATH variable';

    /**
     * @return void
     */
    public function handle(): void
    {
        $bashSource = $this->argument('bash source');

        if ( ! file_exists($bashSource))
        {
            $this->error("The given bash source ({$bashSource}) does not exist.");

            return;
        }

        $path = base_path('bin');

        $content = File::get($bashSource);

        $export = 'export PATH="$PATH:' . $path . '"';

        if (Str::contains($content, $export))
        {
            return;
        }

        File::append(
            $bashSource,
            PHP_EOL . $export
        );
    }
}