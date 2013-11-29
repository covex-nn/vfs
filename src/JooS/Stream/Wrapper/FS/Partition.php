<?php

/**
 * Filesystem tree
 *
 * @author  Andrey F. Mindubaev <covex.mobile@gmail.com>
 * @license http://opensource.org/licenses/MIT  MIT License
 */
namespace JooS\Stream;

/**
 * Filesystem tree
 *
 * @todo нужно проверять на is_writable:
 *        1) изменение/удаление реальных файлов
 *        2) создание файлов/каталогов в реальных каталогов
 */
class Wrapper_FS_Partition
{

  /**
   * @var Entity
   */
  private $_root = null;

  /**
   * @var Wrapper_FS_Changes
   */
  private $_changes = null;

  /**
   * @var Files
   */
  private $_files;

  /**
   * Constructor
   *
   * @param Entity_Interface $content Folder
   */
  public function __construct(Entity_Interface $content = null)
  {
    $this->_files = new Files();

    if (is_null($content)) {
      $folder = $this->_files->mkdir(0777);

      $content = Entity::newInstance($folder);
    }
    $this->setRoot($content);
  }

  /**
   * Return root of filesystem
   *
   * @return Entity
   */
  public function getRoot()
  {
    return $this->_root;
  }

  /**
   * Init root
   *
   * @param Entity_Interface $entity Folder
   *
   * @return null
   * @throws Wrapper_FS_Exception
   */
  protected function setRoot(Entity_Interface $entity)
  {
    if (!$entity->file_exists() || !$entity->is_dir()) {
      throw new Wrapper_FS_Exception(
        "Root folder is not valid"
      );
    }

    $this->_root = $entity;
  }

  /**
   * Return FS-changes object
   *
   * @return Wrapper_FS_Changes
   */
  protected function getChanges()
  {
    if (is_null($this->_changes)) {
      $this->_changes = new Wrapper_FS_Changes();
    }
    return $this->_changes;
  }

  /**
   * Return file/directory entity
   *
   * @param string $filename Path to file/directory
   *
   * @return Entity_Interface
   */
  public function getEntity($filename)
  {
    $changes = $this->getChanges();

    $filename = Entity::fixPath($filename);
    $parts = explode("/", $filename);
    $basename = array_pop($parts);

    $filepath = "";
    $partiallyFilepath = "";
    $changesStage = false;
    $directory = $this->getRoot();

    foreach ($parts as $name) {
      $filepath .= ($filepath ? "/" : "") . $name;

      if ($partiallyFilepath) {
        $partiallyFilepath = $partiallyFilepath . "/" . $name;
      } else {
        $partiallyFilepath = $name;
      }

      $changesExists = $changes->exists($filepath);
      if ($changesExists || $changesStage) {
        $changesStage = true;
        if ($changesExists) {
          $directory = $changes->get($filepath);
          $partiallyFilepath = "";
          if (!$directory->file_exists() || !$directory->is_dir()) {
            return null;
          }
        } else {
          return null;
        }
      } else {
        $path = $directory->path() . "/" . $filepath;
        if (!file_exists($path) || !is_dir($path)) {
          return null;
        }
      }
    }

    if ($changes->exists($filename)) {
      $entity = $changes->get($filename);
    } else {
      $entity = Entity::newInstance(
        $directory->path() .
        ($partiallyFilepath ? "/" . $partiallyFilepath : "") .
        "/" . $basename
      );
    }
    return $entity;
  }

  /**
   * Return list of files inside directory path
   *
   * @param string $path Path
   *
   * @return array
   */
  public function getList($path)
  {
    $entity = $this->getEntity($path);

    if (!is_null($entity) && $entity->file_exists() && $entity->is_dir()) {
      $files = array();
      $changes = $this->getChanges();
      $own = $changes->own($path);

      if (!($entity instanceof Entity_Virtual_Interface)) {
        $directory = $this->getRoot();
        $directoryPath = $directory->path() . "/" . $path;

        $dirHandler = opendir($directoryPath);
        if ($dirHandler) {
          while (true) {
            $file = readdir($dirHandler);
            if ($file === false) {
              break;
            } elseif ($file == "." || $file == "..") {
              continue;
            }

            $changesKey = ($path ? $path . "/" : "") . $file;
            if (isset($own[$changesKey])) {
              continue;
            } else {
              $files[$changesKey] = Entity::newInstance($changesKey);
            }
          }
          closedir($dirHandler);
        }
      }

      foreach ($own as $changesKey => $file) {
        /* @var $file Entity_Interface */
        if ($file instanceof Entity_Deleted_Interface) {
          unset($own[$changesKey]);
        }
      }

      $mergedFiles = array_merge($files, $own);
      ksort($mergedFiles);

      $result = array_values($mergedFiles);
    } else {
      $result = null;
    }

    return $result;
  }

  /**
   * Retrieve information about a file
   *
   * @param string $path  Path to file
   * @param int    $flags Flags
   *
   * @return array
   */
  public function getStat($path, $flags)
  {
    $entity = $this->getEntity($path);

    if (is_null($entity)) {
      $path = null;
    } elseif ($entity instanceof Entity_Deleted_Interface) {
      $path = null;
    } elseif (!$entity->file_exists()) {
      $path = null;
    } else {
      $path = $entity->path();
    }

    if (is_null($path)) {
      $stat = false;
    } elseif ($flags & STREAM_URL_STAT_QUIET) {
      $stat = @stat($path);
    } else {
      $stat = stat($path);
    }

    return $stat;
  }

  /**
   * Create a directory
   *
   * @param string $path    Path
   * @param int    $mode    Mode
   * @param int    $options Options
   *
   * @return Entity_Interface
   */
  public function makeDirectory($path, $mode, $options)
  {
    $result = null;

    $entity = $this->getEntity($path);
    if (!is_null($entity)) {
      if (!$entity->file_exists()) {
        $tmpPath = $this->_files->mkdir($mode);

        $result = Entity_Virtual::newInstance($entity, $tmpPath);

        $changes = $this->getChanges();
        $changes->add($path, $result);
      }
    } else {
      /* @todo сделать поддержку STREAM_MKDIR_RECURSIVE */
    }

    if (is_null($result) && ($options & STREAM_REPORT_ERRORS)) {
      trigger_error(
        "Could not create directory '$path'", E_USER_WARNING
      );
    }

    return $result;
  }

  /**
   * Remove directory
   *
   * @param string $path    Path to directory
   * @param int    $options Stream options
   *
   * @return Entity_Deleted
   */
  public function removeDirectory($path, $options)
  {
    $list = $this->getList($path);
    if (is_null($list)) {
      $result = null;
    } elseif (sizeof($list)) {
      $result = null;
    } else {
      $entity = $this->getEntity($path);

      $result = Entity_Deleted::newInstance($entity);

      $changes = $this->getChanges();
      $changes->add($path, $result);
    }

    if (is_null($result) && ($options & STREAM_REPORT_ERRORS)) {
      trigger_error(
        "Could not remove directory '$path'", E_USER_WARNING
      );
    }

    return $result;
  }

  /**
   * Delete a file
   *
   * @param string $path Path
   *
   * @return Entity_Deleted
   */
  public function deleteFile($path)
  {
    $entity = $this->getEntity($path);

    if (is_null($entity)) {
      $result = null;
    } elseif (!$entity->file_exists() || !$entity->is_file()) {
      $result = null;
    } else {
      $result = Entity_Deleted::newInstance($entity);

      $changes = $this->getChanges();
      $changes->add($path, $result);
    }

    return $result;
  }

  /**
   * Renames a file or directory
   *
   * @param string $srcPath Source path
   * @param string $dstPath Destination path
   *
   * @return Entity_Virtual
   */
  public function rename($srcPath, $dstPath)
  {
    $changes = $this->getChanges();

    $srcEntity = $this->getEntity($srcPath);
    $dstEntity = $this->getEntity($dstPath);

    if (is_null($srcEntity) || !$srcEntity->file_exists()) {
      $result = null;
    } elseif (is_null($dstEntity) || $dstEntity->file_exists()) {
      $result = null;
    } else {
      if ($srcEntity->is_dir()) {
        $dirStat = $this->getStat($srcPath, 0);
        $dstEntity = $this->makeDirectory($dstPath, $dirStat["mode"], 0);

        $list = $this->getList($srcPath);
        if (sizeof($list)) {
          foreach ($list as $file) {
            /* @var $file Entity_Interface */
            $filename = $file->basename();
            $this->rename(
              $srcPath . "/" . $filename,
              $dstPath . "/" . $filename
            );
          }
        }
      } else {
        $tmpPath = $this->_files->tempnam();
        copy($srcEntity->path(), $tmpPath);

        $dstEntity = Entity_Virtual::newInstance(
          $dstEntity, $tmpPath, $dstEntity->basename()
        );
        $changes->add($dstPath, $dstEntity);
      }

      $srcEntity = Entity_Deleted::newInstance($srcEntity);
      $changes->add($srcPath, $srcEntity);

      $result = $dstEntity;
    }
    return $result;
  }

  /**
   * Opens file or URL
   *
   * @param string           $path    Path
   * @param string           $mode    Mode
   * @param int              $options Options
   * @param Entity_Interface &$entity Opened entity
   *
   * @return resource
   * @link http://php.net/manual/en/function.fopen.php
   */
  public function fileOpen($path, $mode, $options, &$entity)
  {
    $entity = $this->getEntity($path);

    $filePointer = null;
    if (!is_null($entity)) {
      $exists = $entity->file_exists();

      if ($exists && !$entity->is_file()) {
        $filePointer = null;
      } else {
        $fopenWillFail = false;
        $mode = strtolower($mode);
        if ($mode != "r") {
          $isVirtual = ($entity instanceof Entity_Virtual_Interface);
          if (!$exists || !$isVirtual) {
            /* @var $entity Entity */
            $tmpPath = $this->_files->tempnam();
            $basename = basename($path);

            if ($exists) {
              if ($mode == "x" || $mode == "x+") {
                $fopenWillFail = true;
              } else {
                copy($entity->path(), $tmpPath);
              }
            }
            if (!$fopenWillFail) {
              $entity = Entity_Virtual::newInstance(
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
   * Commit changes to real FS
   *
   * @return null
   */
  public function commit()
  {
    if (!is_null($this->_changes)) {
      $this->_commit($this->_changes);

      $this->_changes = null;
    }
  }

  /**
   * Commit changes into real FS
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
   * @param Wrapper_FS_Changes $changes Changes in FS
   * @param string             $path    Path to commit
   *
   * @return null
   */
  private function _commit(Wrapper_FS_Changes $changes, $path = "")
  {
    $root = $this->getRoot();
    $rootpath = $root->path();

    if ($path) {
      $path .= "/";
    }

    $own = $changes->own();
    foreach ($own as $filename => $vEntity) {
      $filepath = $path . $filename;
      $rPath = $rootpath . "/" . $filepath;

      /* @var $vEntity Entity_Abstract|Entity_Virtual_Interface */
      $vExists = $vEntity->file_exists();
      $rDeleted = false;

      $rEntity = $vEntity;
      do {
        $rEntity = $rEntity->getRealEntity();
        $rDeleted = $rDeleted || !$rEntity->file_exists();
      } while ($rEntity instanceof Entity_Virtual_Interface);
      /* @var $rEntity Entity */
      $rExists = $rEntity->file_exists();
      if ($rExists && !$vExists) {
        $rDeleted = true;
      }

      if (!$vExists && !$rExists) {
        continue;
      } elseif ($rDeleted) {
        if ($rExists) {
          $this->_files->delete($rPath);
        }
        if ($vExists) {
          $this->_copyChanges($filepath);
        }
      } else {
        /* @var $vEntity Entity_Virtual */
        if ($vEntity->is_file()) {
          unlink($rPath);
          copy($vEntity->path(), $rPath);
        }
      }
    }

    $subtrees = $changes->sublists();
    foreach ($subtrees as $filename => $tree) {
      /* @var $tree Wrapper_FS_Changes */
      if (!isset($own[$filename])) {
        $this->_commit($tree, $path . $filename);
      }
    }

  }

  /**
   * Copy new virtual files to real fs
   *
   * @param string $path Path
   *
   * @return null
   */
  private function _copyChanges($path)
  {
    $entity = $this->getEntity($path);
    $source = $entity->path();

    $root = $this->getRoot()->path();
    $destination = $root . "/" . $path;

    if ($entity->is_file()) {
      copy($source, $destination);
    } else {
      $mode = fileperms($source);
      mkdir($destination, $mode);

      $list = $this->getList($path);
      foreach ($list as $file) {
        /* @var $file Entity_Interface */
        $this->_copyChanges($path . "/" . $file->basename());
      }
    }
  }

}
