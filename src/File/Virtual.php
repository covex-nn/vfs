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
 * Virtual stream entity.
 */
class Virtual extends EntityAbstract implements VirtualInterface
{
    /**
     * @var EntityInterface
     */
    private $realEntity;

    /**
     * Create new virtual stream entity.
     *
     * @return $this
     */
    public static function newInstance(EntityInterface $entity, string $path, string $name = null): self
    {
        if (null === $name) {
            $name = $entity->basename();
        }
        $instance = new static($name, $path);
        $instance->realEntity = $entity;

        return $instance;
    }

    public function getRealEntity(): EntityInterface
    {
        return $this->realEntity;
    }
}
