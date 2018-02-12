<?php

/**
 * Stream entity (real file/directory).
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Covex\Stream\File;

/**
 * @author Andrey F. Mindubaev <covex.mobile@gmail.com>
 */
class Entity extends EntityAbstract
{
    /**
     * Create new entity instance.
     *
     * @param string $path Path to file
     *
     * @return static
     */
    public static function newInstance(string $path): self
    {
        $basename = basename($path);

        return new static($basename, $path);
    }

    /**
     * Fix slashes and backslashes in path.
     *
     * @param string $path Path
     *
     * @return string
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
