<?php

declare(strict_types=1);

/*
 * (c) Andrey F. Mindubaev <covex.mobile@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Covex\Stream;

use Covex\Stream\File\EntityInterface;

/**
 * Partition changes, tree data.
 */
class Changes implements \Countable
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
     * Get stream entity.
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
     * Add stream entity to array.
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
     * @param string|array $path
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
     */
    public function exists(string $path): bool
    {
        $name = null;
        $subtree = $this->subtree($path, $name);

        return null !== $subtree && isset($subtree->ownData[$name]);
    }

    public function count(): int
    {
        return count($this->ownData) + count($this->subTrees);
    }

    /**
     * Get subtree's own changes.
     *
     * @param array|string $path
     *
     * @return EntityInterface[]
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
     * Get all children in path/*.*.
     *
     * @param array|string $path Path
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
     * Get subtree by path.
     *
     * @param string|array $path
     */
    public function subtree($path, string &$name = null, bool $create = false): ?self
    {
        $parts = $this->split($path);

        $dirOrFile = array_shift($parts);
        if (!count($parts)) {
            $name = $dirOrFile;
            $subtree = $this;
        } else {
            $exists = isset($this->subTrees[$dirOrFile]);
            if (!$exists && !$create) {
                $subtree = null;
                $name = null;
            } else {
                if (!$exists && $create) {
                    $this->subTrees[$dirOrFile] = new self();
                }
                $subtree = $this->subTrees[$dirOrFile]->subtree($parts, $name, $create);
            }
        }

        return $subtree;
    }

    /**
     * Get a list of own subtrees.
     *
     * @return Changes[]
     */
    public function sublists(): array
    {
        return $this->subTrees;
    }

    /**
     * Split path into dir names.
     *
     * @param string|array $path Path
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
     */
    private function appendChildren(array &$children, string $name, array $new): void
    {
        foreach ($new as $key => $value) {
            $children[$name.'/'.$key] = $value;
        }
    }
}
