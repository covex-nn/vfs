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
 * Deleted stream entity.
 */
class Deleted extends EntityAbstract implements DeletedInterface
{
    /**
     * @var EntityInterface
     */
    private $realEntity;

    /**
     * Create new virtual stream entity.
     *
     * @param EntityInterface $realEntity Real stream entity
     *
     * @return $this
     */
    public static function newInstance(EntityInterface $realEntity): self
    {
        $basename = $realEntity->basename();
        $path = $realEntity->path();

        $instance = new static($basename, $path);
        $instance->realEntity = $realEntity;

        return $instance;
    }

    /**
     * Get saved old entity.
     */
    public function getRealEntity(): EntityInterface
    {
        return $this->realEntity;
    }

    public function file_exists(): bool
    {
        return false;
    }
}
