<?php

/**
 * Interface for all stream entities.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Covex\Stream\File;

/**
 * @author Andrey F. Mindubaev <covex.mobile@gmail.com>
 */
interface EntityInterface
{
    /**
     * File exists ?
     *
     * @return bool
     */
    public function file_exists(): bool;

    /**
     * Is entity - directory ?
     *
     * @return bool
     */
    public function is_dir(): bool;

    /**
     * Is entity - file ?
     *
     * @return bool
     */
    public function is_file(): bool;

    /**
     * Is entity - readable ?
     *
     * @return bool
     */
    public function is_readable(): bool;

    /**
     * Is entity - writable ?
     *
     * @return bool
     */
    public function is_writable(): bool;

    /**
     * Returns basename of entity.
     *
     * @return string
     */
    public function basename(): string;

    /**
     * Returns path of entity.
     *
     * @return string
     */
    public function path(): string;
}
