<?php

  require_once "JooS/Stream/Storage/List.php";

  class JooS_Stream_Storage_ListTest extends PHPUnit_Framework_TestCase {

    public function testInstance() {
      $list = new JooS_Stream_Storage_List();
      $this->assertEquals(0, $list->count());

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

      $list->{$name1} = $storageFile1;
      $list[$name2]   = $storageFile2;
      $list[$name3]   = $storageFile3;
      $list[$name4]   = $storageFile4;

      $this->assertEquals(4, $list->count());
      $this->assertEquals($storageFile1, $list[$name1]);
      $this->assertEquals($storageFile2, $list->{$name2});

      $this->assertTrue(isset($list->{$name1}));
      $this->assertTrue(isset($list[$name2]));

      unset($list[$name3]);
      unset($list->{$name4});
      $this->assertEquals(2, $list->count());

      $count = 0;
      foreach ($list as $k => $v) {
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

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testError() {
      $list        = new JooS_Stream_Storage_List();
      $list["qqq"] = 1;
    }

  }

  