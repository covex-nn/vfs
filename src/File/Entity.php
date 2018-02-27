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
 * Stream entity (real file/directory).
 */
class Entity extends EntityAbstract
{
    /**
     * @return $this
     */
    public static function newInstance(string $path): self
    {
        $basename = basename($path);

        return new static($basename, $path);
    }

    /**
     * Fix slashes and backslashes in path.
     */
    public static function fixPath(string $path): string
    {
        if (false !== strpos($path, '\\')) {
            $path = str_replace('\\', '/', $path);
        }
        while (false !== strpos($path, '//')) {
            $path = str_replace('//', '/', $path);
        }
        if (strlen($path)) {
            if ('/' === substr($path, 0, 1)) {
                $path = ltrim($path, '/');
            }
            if ('/' === substr($path, -1, 1)) {
                $path = rtrim($path, '/');
            }
        }

        return $path;
    }
}
