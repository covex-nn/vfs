<?php

/**
 * Partition changes, tree data.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Covex\Stream;

use Covex\Stream\File\EntityInterface;

/**
 * @author Andrey F. Mindubaev <covex.mobile@gmail.com>
 */
class Changes
{
    /**
     * @var array
     */
    private $ownData;

    /**
     * @var Changes[]
     */
    private $subTrees;

    public function __construct()
    {
        $this->ownData = [];
        $this->subTrees = [];
    }

    /**
     * Return stream entity.
     *
     * @param string $path Path
     *
     * @return EntityInterface|null
     */
    public function get(string $path): ?EntityInterface
    {
        $entity = null;

        $name = null;
        $subtree = $this->subtree($path, $name);
        if (null !== $subtree && isset($subtree->ownData[$name])) {
            $entity = $subtree->ownData[$name];
        }

        return $entity;
    }

    /**
     * Add stream entity to changes array.
     *
     * @param string          $path   Path
     * @param EntityInterface $entity Stream entity
     *
     * @return bool
     */
    public function add(string $path, EntityInterface $entity): bool
    {
        $result = false;

        if (strlen($path)) {
            $name = null;
            $subtree = $this->subtree($path, $name, true);
            $subtree->ownData[$name] = $entity;

            $result = true;
        }

        return $result;
    }

    /**
     * Delete stream entity from array.
     *
     * @param string|array $path Path
     *
     * @return bool
     */
    public function delete($path): bool
    {
        $result = false;
        $parts = $this->split($path);

        $name = array_shift($parts);
        if (!count($parts)) {
            if (isset($this->ownData[$name])) {
                unset($this->ownData[$name]);
                $result = true;
            }
        } elseif (isset($this->subTrees[$name])) {
            $subtree = $this->subTrees[$name];
            $result = $subtree->delete($parts);
            if ($result && !$subtree->count()) {
                unset($this->subTrees[$name]);
            }
        }

        return $result;
    }

    /**
     * Is $path added to array ?
     *
     * @param string $path Path
     *
     * @return bool
     */
    public function exists(string $path): bool
    {
        $name = null;
        $subtree = $this->subtree($path, $name);

        return null !== $subtree && isset($subtree->ownData[$name]);
    }

    /**
     * Count elements.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->ownData) + count($this->subTrees);
    }

    /**
     * Return subtree's own changes.
     *
     * @param array|string $path Path
     *
     * @return array
     */
    public function own($path = ''): array
    {
        if ($path) {
            $parts = $this->split($path);
            $name = array_shift($parts);

            $own = [];
            if (isset($this->subTrees[$name])) {
                $subtree = $this->subTrees[$name];
                $this->appendChildren(
                    $own, $name, $subtree->own($parts)
                );
            }
        } else {
            $own = $this->ownData;
        }

        return $own;
    }

    /**
     * Return all children in path/*.*.
     *
     * @param array|string $path Path
     *
     * @return array
     */
    public function children($path = ''): array
    {
        if ($path) {
            $parts = $this->split($path);
            $name = array_shift($parts);

            $children = [];
            if (isset($this->subTrees[$name])) {
                $subtree = $this->subTrees[$name];
                $this->appendChildren(
                    $children, $name, $subtree->children($parts)
                );
            }
        } else {
            $children = $this->own();
            foreach ($this->subTrees as $name => $subtree) {
                $this->appendChildren(
                    $children, $name, $subtree->children()
                );
            }
        }

        return $children;
    }

    /**
     * Return subtree by path.
     *
     * @param string|array $path   Path
     * @param string       $name   Name of new element
     * @param bool         $create Auto create subtree ?
     *
     * @return Changes|null
     */
    public function subtree($path, string &$name = null, bool $create = false): ?self
    {
        $parts = $this->split($path);

        $_name = array_shift($parts);
        if (!count($parts)) {
            $name = $_name;
            $subtree = $this;
        } else {
            $exists = isset($this->subTrees[$_name]);
            if (!$exists && !$create) {
                $subtree = null;
                $name = null;
            } else {
                if (!$exists && $create) {
                    $this->subTrees[$_name] = new self();
                }
                $subtree = $this->subTrees[$_name]->subtree($parts, $name, $create);
            }
        }

        return $subtree;
    }

    /**
     * Return a list of own subtrees.
     *
     * @return array
     */
    public function sublists(): array
    {
        return $this->subTrees;
    }

    /**
     * Split path into dir names.
     *
     * @param string|array $path Path
     *
     * @return array
     */
    protected function split($path): array
    {
        if (is_array($path)) {
            $parts = $path;
        } else {
            $parts = explode('/', $path);
        }

        return $parts;
    }

    /**
     * Add new children to array.
     *
     * @param array  $children  Children array
     * @param string $name      Current name
     * @param array  $_children Children of subtrees
     */
    private function appendChildren(array &$children, string $name, array $_children): void
    {
        foreach ($_children as $key => $value) {
            $children[$name.'/'.$key] = $value;
        }
    }
}
