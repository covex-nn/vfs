Transactional Virtual File System
=================================

Example
-------

```php
<?php

  Covex\Stream\FileSystem::register("any-vfs-protocol", __DIR__);

  file_put_contents("any-vfs-protocol://" . basename(__FILE__), "hahaha");
  unlink("any-vfs-protocol://" . basename(__FILE__));

  // Covex\Stream\FileSystem::commit("any-vfs-protocol");
  // echo file_exists(__FILE__) ? "yes" : "no";

  Covex\Stream\FileSystem::unregister("any-vfs-protocol");
  
?>
```
