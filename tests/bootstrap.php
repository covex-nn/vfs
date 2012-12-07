<?php

  $sub_include_path = function() {
    $ext = dirname(__DIR__) . "/external";
    if (file_exists($ext) && is_dir($ext))
    {
      foreach (new DirectoryIterator($ext) as $fileinfo)
      {
        /* @var $fileinfo DirectoryIterator */
        if (!$fileinfo->isDot() && $fileinfo->isDir())
        {
          $bootstrap = $ext . "/" . $fileinfo->getFilename() . "/tests/bootstrap.php";
          if (file_exists($bootstrap))
          {
            include_once($bootstrap);
          }
        }
      }
    }
  };
  $sub_include_path();
  unset($sub_include_path);

  set_include_path(implode(PATH_SEPARATOR, array(
    __DIR__ . "/library",
    dirname(__DIR__) . "/library", 
    get_include_path()
  )));
