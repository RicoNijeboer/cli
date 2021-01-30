<?php

namespace App\Files;

/**
 * Class FileReader
 *
 * @package App\Files
 */
class FileReader
{

    /** @var string */
    private $path;

    /** @var resource|null */
    private $pointer = null;


    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function open(): self
    {
        $this->pointer = fopen($this->path, 'r');

        return $this;
    }

    public function close(): self
    {
        fclose($this->pointer);
        $this->pointer = null;

        return $this;
    }

    public function getNextLine(): string
    {
        $line = fgets($this->pointer);

        return rtrim($line ?? '');
    }

    public function hasNextLine(): bool
    {
        return !feof($this->pointer);
    }

    /**
     * @return bool
     */
    protected function isOpened(): bool
    {
        return empty($this->pointer);
    }

    protected function ensureOpened(): void
    {
        if ( ! $this->isOpened())
        {
            $this->open();
        }
    }
}