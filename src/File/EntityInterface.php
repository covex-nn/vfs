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
 * Interface for all stream entities.
 */
interface EntityInterface
{
    /**
     * Is file exists ?
     */
    public function file_exists(): bool;

    /**
     * Is entity - directory ?
     */
    public function is_dir(): bool;

    /**
     * Is entity - file ?
     */
    public function is_file(): bool;

    /**
     * Is entity - readable ?
     */
    public function is_readable(): bool;

    /**
     * Is entity - writable ?
     */
    public function is_writable(): bool;

    /**
     * Get basename of entity.
     */
    public function basename(): string;

    /**
     * Get path of entity.
     */
    public function path(): string;
}
