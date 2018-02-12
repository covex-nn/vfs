<?php

/**
 * Interface for virtual stream entities.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Covex\Stream\File;

/**
 * @author Andrey F. Mindubaev <covex.mobile@gmail.com>
 */
interface VirtualInterface
{
    /**
     * Return saved old entity.
     *
     * @return EntityInterface
     */
    public function getRealEntity(): EntityInterface;
}
