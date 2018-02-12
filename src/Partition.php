<?php

/**
 * Filesystem tree.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Covex\Stream;

use Covex\Stream\File\Deleted;
use Covex\Stream\File\DeletedInterface;
use Covex\Stream\File\Entity;
use Covex\Stream\File\EntityAbstract;
use Covex\Stream\File\EntityInterface;
use Covex\Stream\File\Virtual;
use Covex\Stream\File\VirtualInterface;

/**
 * Filesystem tree.
 *
 * @author Andrey F. Mindubaev <covex.mobile@gmail.com>
 *
 * @todo нужно проверять на is_writable:
 *        1) изменение/удаление реальных файлов
 *        2) создание файлов/каталогов в реальных каталогов
 */
class Partition
{
    /**
     * @var Entity
     */
    private $root = null;

    /**
     * @var Changes
     */
    private $changes = null;

    /**
     * @var Files
     */
    private $files;

    /**
     * @param EntityInterface $content Folder
     */
    public function __construct(EntityInterface $content = null)
    {
        $this->files = new Files();

        if (null === $content) {
            $folder = $this->files->mkdir(0777);

            $content = Entity::newInstance($folder);
        }
        $this->setRoot($content);
    }

    /**
     * Return root of filesystem.
     *
     * @return EntityInterface
     */
    public function getRoot(): EntityInterface
    {
        return $this->root;
    }

    /**
     * Return file/directory entity.
     *
     * @param string $filename Path to file/directory
     *
     * @return EntityInterface|null
     */
    public function getEntity(string $filename): ?EntityInterface
    {
        $changes = $this->getChanges();

        $filename = Entity::fixPath($filename);
        $parts = explode('/', $filename);
        $basename = array_pop($parts);

        $filepath = '';
        $partiallyFilepath = '';
        $changesStage = false;
        $directory = $this->getRoot();

        foreach ($parts as $name) {
            $filepath .= ($filepath ? '/' : '').$name;

            if ($partiallyFilepath) {
                $partiallyFilepath = $partiallyFilepath.'/'.$name;
            } else {
                $partiallyFilepath = $name;
            }

            $changesExists = $changes->exists($filepath);
            if ($changesExists || $changesStage) {
                $changesStage = true;
                if ($changesExists) {
                    $directory = $changes->get($filepath);
                    $partiallyFilepath = '';
                    if (!$directory->file_exists() || !$directory->is_dir()) {
                        return null;
                    }
                } else {
                    return null;
                }
            } else {
                $path = $directory->path().'/'.$filepath;
                if (!file_exists($path) || !is_dir($path)) {
                    return null;
                }
            }
        }

        if ($changes->exists($filename)) {
            $entity = $changes->get($filename);
        } else {
            $entity = Entity::newInstance(
                $directory->path().
                ($partiallyFilepath ? '/'.$partiallyFilepath : '').
                '/'.$basename
            );
        }

        return $entity;
    }

    /**
     * Return list of files inside directory path.
     *
     * @param string $path Path
     *
     * @return array|null
     */
    public function getList(string $path)
    {
        $entity = $this->getEntity($path);

        if (null !== $entity && $entity->file_exists() && $entity->is_dir()) {
            $files = [];
            $changes = $this->getChanges();
            $own = $changes->own($path);

            if (!($entity instanceof VirtualInterface)) {
                $directory = $this->getRoot();
                $directoryPath = $directory->path().'/'.$path;

                $dirHandler = opendir($directoryPath);
                if ($dirHandler) {
                    while (true) {
                        $file = readdir($dirHandler);
                        if (false === $file) {
                            break;
                        } elseif ('.' == $file || '..' == $file) {
                            continue;
                        }

                        $changesKey = ($path ? $path.'/' : '').$file;
                        if (isset($own[$changesKey])) {
                            continue;
                        }
                        $files[$changesKey] = Entity::newInstance($changesKey);
                    }
                    closedir($dirHandler);
                }
            }

            foreach ($own as $changesKey => $file) {
                /* @var $file EntityInterface */
                if ($file instanceof DeletedInterface) {
                    unset($own[$changesKey]);
                }
            }

            $mergedFiles = array_merge($files, $own);
            ksort($mergedFiles);

            $result = array_values($mergedFiles);
        } else {
            /**
             * @todo !!! возвращать пустой массив!
             */
            $result = null;
        }

        return $result;
    }

    /**
     * Retrieve information about a file.
     *
     * @param string $path  Path to file
     * @param int    $flags Flags
     *
     * @return array|bool
     */
    public function getStat(string $path, int $flags)
    {
        $entity = $this->getEntity($path);

        if (null === $entity) {
            $path = null;
        } elseif ($entity instanceof DeletedInterface) {
            $path = null;
        } elseif (!$entity->file_exists()) {
            $path = null;
        } else {
            $path = $entity->path();
        }

        if (null === $path) {
            $stat = false;
        } elseif ($flags & STREAM_URL_STAT_QUIET) {
            $stat = @stat($path);
        } else {
            $stat = stat($path);
        }
        /*
         * @todo всегда возвращать массив
         */
        return $stat;
    }

    /**
     * Create a directory.
     *
     * @param string $path    Path
     * @param int    $mode    Mode
     * @param int    $options Options
     *
     * @return EntityInterface|null
     */
    public function makeDirectory($path, $mode, $options)
    {
        $result = null;

        $entity = $this->getEntity($path);
        if (null !== $entity) {
            if (!$entity->file_exists()) {
                $tmpPath = $this->files->mkdir($mode);

                $result = Virtual::newInstance($entity, $tmpPath);

                $changes = $this->getChanges();
                $changes->add($path, $result);
            }
        }
        /* @todo STREAM_MKDIR_RECURSIVE support */

        if (null === $result && ($options & STREAM_REPORT_ERRORS)) {
            trigger_error(
                "Could not create directory '$path'", E_USER_WARNING
            );
        }

        return $result;
    }

    /**
     * Remove directory.
     *
     * @param string $path    Path to directory
     * @param int    $options Stream options
     *
     * @return Deleted|null
     */
    public function removeDirectory(string $path, int $options)
    {
        $list = $this->getList($path);
        if (null === $list) {
            $result = null;
        } elseif (count($list)) {
            $result = null;
        } else {
            $entity = $this->getEntity($path);

            $result = Deleted::newInstance($entity);

            $changes = $this->getChanges();
            $changes->add($path, $result);
        }

        if (null === $result && ($options & STREAM_REPORT_ERRORS)) {
            trigger_error(
                "Could not remove directory '$path'", E_USER_WARNING
            );
        }

        return $result;
    }

    /**
     * Delete a file.
     *
     * @param string $path Path
     *
     * @return Deleted|null
     */
    public function deleteFile(string $path)
    {
        $entity = $this->getEntity($path);

        if (null === $entity) {
            $result = null;
        } elseif (!$entity->file_exists() || !$entity->is_file()) {
            $result = null;
        } else {
            $result = Deleted::newInstance($entity);

            $changes = $this->getChanges();
            $changes->add($path, $result);
        }

        return $result;
    }

    /**
     * Renames a file or directory.
     *
     * @param string $srcPath Source path
     * @param string $dstPath Destination path
     *
     * @return Virtual|null
     */
    public function rename(string $srcPath, string $dstPath)
    {
        $changes = $this->getChanges();

        $srcEntity = $this->getEntity($srcPath);
        $dstEntity = $this->getEntity($dstPath);

        if (null === $srcEntity || !$srcEntity->file_exists()) {
            $result = null;
        } elseif (null === $dstEntity || $dstEntity->file_exists()) {
            $result = null;
        } else {
            if ($srcEntity->is_dir()) {
                $dirStat = $this->getStat($srcPath, 0);
                $dstEntity = $this->makeDirectory($dstPath, $dirStat['mode'], 0);

                $list = $this->getList($srcPath);
                if (count($list)) {
                    foreach ($list as $file) {
                        /* @var $file EntityInterface */
                        $filename = $file->basename();
                        $this->rename(
                            $srcPath.'/'.$filename,
                            $dstPath.'/'.$filename
                        );
                    }
                }
            } else {
                $tmpPath = $this->files->tempnam();
                copy($srcEntity->path(), $tmpPath);

                $dstEntity = Virtual::newInstance(
                    $dstEntity, $tmpPath, $dstEntity->basename()
                );
                $changes->add($dstPath, $dstEntity);
            }

            $srcEntity = Deleted::newInstance($srcEntity);
            $changes->add($srcPath, $srcEntity);

            $result = $dstEntity;
        }

        return $result;
    }

    /**
     * Opens file or URL.
     *
     * @param string $path    Path
     * @param string $mode    Mode
     * @param int    $options Options
     *
     * @return resource
     *
     * @see http://php.net/manual/en/function.fopen.php
     */
    public function fileOpen(string $path, string $mode, int $options)
    {
        $entity = $this->getEntity($path);

        $filePointer = null;
        if (null !== $entity) {
            $exists = $entity->file_exists();

            if ($exists && !$entity->is_file()) {
                $filePointer = null;
            } else {
                $fopenWillFail = false;
                $mode = strtolower($mode);
                if ('r' != $mode) {
                    $isVirtual = ($entity instanceof VirtualInterface);
                    if (!$exists || !$isVirtual) {
                        /* @var $entity Entity */
                        $tmpPath = $this->files->tempnam();
                        $basename = basename($path);

                        if ($exists) {
                            if ('x' == $mode || 'x+' == $mode) {
                                $fopenWillFail = true;
                            } else {
                                copy($entity->path(), $tmpPath);
                            }
                        }
                        if (!$fopenWillFail) {
                            $entity = Virtual::newInstance(
                                $entity, $tmpPath, $basename
                            );

                            $changes = $this->getChanges();
                            $changes->add($path, $entity);
                        }
                    }
                }

                if ($fopenWillFail) {
                    $entity = null;
                } else {
                    if ($options & STREAM_REPORT_ERRORS) {
                        $filePointer = fopen($entity->path(), $mode);
                    } else {
                        $filePointer = @fopen($entity->path(), $mode);
                    }
                }
            }
        }

        return $filePointer;
    }

    /**
     * Commit changes to real FS.
     */
    public function commit(): void
    {
        if (null !== $this->changes) {
            $this->commitInternal($this->changes);

            $this->changes = null;
        }
    }

    /**
     * Init root.
     *
     * @param EntityInterface $entity Folder
     *
     * @throws Exception
     */
    protected function setRoot(EntityInterface $entity): void
    {
        if (!$entity->file_exists() || !$entity->is_dir()) {
            throw new Exception('Root folder is not valid');
        }

        $this->root = $entity;
    }

    /**
     * Return FS-changes object.
     *
     * @return Changes
     */
    protected function getChanges(): Changes
    {
        if (null === $this->changes) {
            $this->changes = new Changes();
        }

        return $this->changes;
    }

    /**
     * Commit changes into real FS.
     *
     * Если удалено и не существовало с самого начала
     * - ничего не делаем
     * Иначе если когда-то не существовало
     * - удаляем всё RДерево
     * - Если сейчас существует, то
     *   - переносим всё VДерево
     * Иначе если это файл, то
     * - удаляем RФайл
     * - копируем VФайл на место RФайла
     *
     * По всем subtree
     * - сделать тоже самое, если не было собственных изменений
     *
     * @param Changes $changes Changes in FS
     * @param string  $path    Path to commit
     */
    private function commitInternal(Changes $changes, string $path = ''): void
    {
        $root = $this->getRoot();
        $rootpath = $root->path();

        if ($path) {
            $path .= '/';
        }

        $own = $changes->own();
        foreach ($own as $filename => $vEntity) {
            $filepath = $path.$filename;
            $rPath = $rootpath.'/'.$filepath;

            /* @var $vEntity EntityAbstract|VirtualInterface */
            $vExists = $vEntity->file_exists();
            $rDeleted = false;

            $rEntity = $vEntity;
            do {
                $rEntity = $rEntity->getRealEntity();
                $rDeleted = $rDeleted || !$rEntity->file_exists();
            } while ($rEntity instanceof VirtualInterface);
            /* @var $rEntity Entity */
            $rExists = $rEntity->file_exists();
            if ($rExists && !$vExists) {
                $rDeleted = true;
            }

            if (!$vExists && !$rExists) {
                continue;
            } elseif ($rDeleted) {
                if ($rExists) {
                    $this->files->delete($rPath);
                }
                if ($vExists) {
                    $this->copyChanges($filepath);
                }
            } else {
                /* @var $vEntity Virtual */
                if ($vEntity->is_file()) {
                    unlink($rPath);
                    copy($vEntity->path(), $rPath);
                }
            }
        }

        $subtrees = $changes->sublists();
        foreach ($subtrees as $filename => $tree) {
            /* @var $tree Changes */
            if (!isset($own[$filename])) {
                $this->commitInternal($tree, $path.$filename);
            }
        }
    }

    /**
     * Copy new virtual files to real fs.
     *
     * @param string $path Path
     */
    private function copyChanges(string $path): void
    {
        $entity = $this->getEntity($path);
        $source = $entity->path();

        $root = $this->getRoot()->path();
        $destination = $root.'/'.$path;

        if ($entity->is_file()) {
            copy($source, $destination);
        } else {
            $mode = fileperms($source);
            mkdir($destination, $mode);

            $list = $this->getList($path);
            foreach ($list as $file) {
                /* @var $file EntityInterface */
                $this->copyChanges($path.'/'.$file->basename());
            }
        }
    }
}
