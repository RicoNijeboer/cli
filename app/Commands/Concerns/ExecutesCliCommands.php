<?php

namespace App\Commands\Concerns;

/**
 * Class ExecutesCliCommands
 *
 * @package App\Commands\Concerns
 */
trait ExecutesCliCommands
{

    /**
     * @param string $directory
     */
    protected function cd(string $directory): void
    {
        $this->line("<comment> $</comment> cd {$directory}", null);
        chdir($directory);
    }

    /**
     * @param string $command
     *
     * @return void
     */
    private function exec(string $command): void
    {
        $this->line("<comment> $</comment> {$command}", null);
        exec($command);
    }
}