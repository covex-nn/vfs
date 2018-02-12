<?php

/**
 * Virtual stream entity.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Covex\Stream\File;

/**
 * @author Andrey F. Mindubaev <covex.mobile@gmail.com>
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
     * @param EntityInterface $entity Real stream entity
     * @param string          $path   Tmp path
     * @param string          $name   Optional basename
     *
     * @return static
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

    /**
     * {@inheritdoc}
     */
    public function getRealEntity(): EntityInterface
    {
        return $this->realEntity;
    }
}
