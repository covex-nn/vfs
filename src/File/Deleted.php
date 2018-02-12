<?php

/**
 * Deleted stream entity.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Covex\Stream\File;

/**
 * @author Andrey F. Mindubaev <covex.mobile@gmail.com>
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
     * @return static
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
     * Return saved old entity.
     *
     * @return EntityInterface
     */
    public function getRealEntity(): EntityInterface
    {
        return $this->realEntity;
    }

    /**
     * {@inheritdoc}
     */
    public function file_exists(): bool
    {
        return false;
    }
}
