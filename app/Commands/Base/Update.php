<?php

namespace App\Commands\Base;

use App\Commands\Concerns\ExecutesCliCommands;
use LaravelZero\Framework\Commands\Command;

/**
 * Class Update
 *
 * @package App\Commands\Base
 */
class Update extends Command
{

    use ExecutesCliCommands;

    /**
     * @var string
     */
    protected $signature = 'update';

    /**
     * @var string
     */
    protected $description = 'Updates the CLI tool for you';

    /**
     * @return void
     */
    public function handle(): void
    {
        $original = getcwd();
        $path = base_path();

        $this->exec("cd {$path} && git pull && composer install --no-dev");
        $this->exec("cd {$original}");
    }
}