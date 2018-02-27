<?php

declare(strict_types=1);

/*
 * (c) Andrey F. Mindubaev <covex.mobile@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Covex\Stream\File;

/**
 * Stream Entity.
 */
abstract class EntityAbstract implements EntityInterface
{
    /**
     * @var string
     */
    protected $basename;

    /**
     * @var string
     */
    protected $path;

    protected function __construct(string $basename, string $path)
    {
        $this->setBasename($basename);
        $this->setPath($path);
    }

    public function basename(): string
    {
        return $this->basename;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function is_writable(): bool
    {
        $path = $this->path();

        return $this->file_exists() && is_writable($path);
    }

    public function is_readable(): bool
    {
        $path = $this->path();

        return $this->file_exists() && is_readable($path);
    }

    public function is_dir(): bool
    {
        $path = $this->path();

        return $this->file_exists() && is_dir($path);
    }

    public function is_file(): bool
    {
        $path = $this->path();

        return $this->file_exists() && is_file($path);
    }

    public function file_exists(): bool
    {
        $path = $this->path();

        return file_exists($path);
    }

    protected function setBasename(string $basename): void
    {
        $this->basename = $basename;
    }

    protected function setPath(string $path): void
    {
        $unixPath = str_replace('\\', '/', $path);
        $this->path = $unixPath;
    }
}
