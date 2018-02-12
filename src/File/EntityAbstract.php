<?php

/**
 * Stream Entity.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Covex\Stream\File;

/**
 * @author Andrey F. Mindubaev <covex.mobile@gmail.com>
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

    /**
     * Protected constructor.
     *
     * @param string $basename Filename
     * @param string $path     Path
     */
    protected function __construct(string $basename, string $path)
    {
        $this->setBasename($basename);
        $this->setPath($path);
    }

    /**
     * {@inheritdoc}
     */
    public function basename(): string
    {
        return $this->basename;
    }

    /**
     * {@inheritdoc}
     */
    public function path(): string
    {
        return $this->path;
    }

    /**
     * {@inheritdoc}
     */
    public function is_writable(): bool
    {
        $path = $this->path();

        return $this->file_exists() && is_writable($path);
    }

    /**
     * {@inheritdoc}
     */
    public function is_readable(): bool
    {
        $path = $this->path();

        return $this->file_exists() && is_readable($path);
    }

    /**
     * {@inheritdoc}
     */
    public function is_dir(): bool
    {
        $path = $this->path();

        return $this->file_exists() && is_dir($path);
    }

    /**
     * {@inheritdoc}
     */
    public function is_file(): bool
    {
        $path = $this->path();

        return $this->file_exists() && is_file($path);
    }

    /**
     * {@inheritdoc}
     */
    public function file_exists(): bool
    {
        $path = $this->path();

        return file_exists($path);
    }

    /**
     * {@inheritdoc}
     */
    protected function setBasename($basename): void
    {
        $this->basename = $basename;
    }

    /**
     * Sets path.
     *
     * @param string $path Path
     */
    protected function setPath($path): void
    {
        $unixPath = str_replace('\\', '/', $path);
        $this->path = $unixPath;
    }
}
