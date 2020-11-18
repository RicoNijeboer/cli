<?php

namespace App\Commands\Base;

use App\Commands\Concerns\ExecutesCliCommands;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
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

        if ( ! is_dir($path))
        {
            File::makeDirectory($path, 0755, true);
        }

        if ( ! file_exists($path . DIRECTORY_SEPARATOR . 'rico'))
        {
            File::link(base_path('rico'), $path . DIRECTORY_SEPARATOR . 'rico');
        }

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