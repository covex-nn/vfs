<?php

  require_once "JooS/Stream/Storage.php";

  class JooS_Stream_StorageTest extends PHPUnit_Framework_TestCase {

    public function testInstance() {
      require_once "JooS/Stream/Entity/PHPUnit/TestingDir.php";

      require_once "JooS/Stream/Entity/PHPUnit/TestingFile.php";

      $nameDir  = "qqq";
      $nameFile = "eee.rrr";

      $contentDir = JooS_Stream_Entity_PHPUnit_TestingDir::newInstance($nameDir);
      /* @var $storageDir JooS_Stream_Storage_Dir */
      $storageDir = JooS_Stream_Storage::newInstance($contentDir, null);

      $contentFile = JooS_Stream_Entity_PHPUnit_TestingFile::newInstance("www/" . $nameFile);
      /* @var $storageFile JooS_Stream_Storage_File */
      $storageFile = JooS_Stream_Storage::newInstance($contentFile, $storageDir);

      $this->assertEquals($nameDir, $storageDir->name());
      $this->assertEquals($nameFile, $storageFile->name());

      $this->assertEquals($contentDir, $storageDir->content());
      $this->assertEquals($contentFile, $storageFile->content());

      $this->assertEquals(null, $storageDir->storage());
      $this->assertEquals($storageDir, $storageFile->storage());

      $this->assertEquals($nameDir, $storageDir->path());
      $this->assertEquals($nameDir . "/" . $nameFile, $storageFile->path());

      $this->assertEquals(1, $storageDir->count());
      /* @var $files JooS_Stream_Storage_List */
      $files = $storageDir->files();
      $this->assertEquals(1, $files->count());
      $this->assertEquals($storageFile, $files[$nameFile]);
    }

  }

  