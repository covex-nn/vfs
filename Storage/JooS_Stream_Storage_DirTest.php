<?php

require_once "JooS/Stream/Storage/Dir.php";

class JooS_Stream_Storage_DirTest extends PHPUnit_Framework_TestCase
{

  public function testInstance()
  {
    require_once "JooS/Stream/Entity/PHPUnit/TestingDir.php";

    $dirContent = JooS_Stream_Entity_PHPUnit_TestingDir::newInstance("qqq");
    
    require_once "JooS/Stream/Storage.php";

    $dirStorage = JooS_Stream_Storage::newInstance($dirContent);
    
    $this->assertEquals(0, $dirStorage->count());

    require_once "JooS/Stream/Entity/PHPUnit/TestingFile.php";

    require_once "JooS/Stream/Storage.php";

    $name1 = "qqq";
    $name2 = "eee.rrr";
    $name3 = "ttt";
    $name4 = "yyy";

    $contentFile1 = JooS_Stream_Entity_PHPUnit_TestingFile::newInstance($name1);
    $storageFile1 = JooS_Stream_Storage::newInstance($contentFile1);

    $contentFile2 = JooS_Stream_Entity_PHPUnit_TestingFile::newInstance($name2);
    $storageFile2 = JooS_Stream_Storage::newInstance($contentFile2);

    $contentFile3 = JooS_Stream_Entity_PHPUnit_TestingFile::newInstance($name3);
    $storageFile3 = JooS_Stream_Storage::newInstance($contentFile3);

    $contentFile4 = JooS_Stream_Entity_PHPUnit_TestingFile::newInstance($name4);
    $storageFile4 = JooS_Stream_Storage::newInstance($contentFile4);

    $dirStorage->add($storageFile1);
    $dirStorage->add($storageFile2);
    $dirStorage->add($storageFile3);
    $dirStorage->add($storageFile4);

    $this->assertEquals(4, $dirStorage->count());
    $this->assertEquals($storageFile1, $dirStorage->{$name1});
    $this->assertEquals($storageFile2, $dirStorage->{$name2});

    $this->assertTrue(isset($dirStorage->{$name1}));
    $this->assertTrue(isset($dirStorage->{$name2}));

    unset($dirStorage->{$name3});
    unset($dirStorage->{$name4});
    $this->assertEquals(2, $dirStorage->count());

    $count = 0;
    foreach ($dirStorage as $k => $v) {
      switch ($k) {
        case $name1:
          $count += 1;
          break;
        case $name2:
          $count += 10;
          break;
        default:
          $count += 0.12872845218461294123;
      }
    }
    $this->assertEquals(11, $count);
  }

}
