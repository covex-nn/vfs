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
 * Interface for virtual stream entities.
 */
interface VirtualInterface
{
    /**
     * Get saved real entity.
     */
    public function getRealEntity(): EntityInterface;
}
