JooS_Stream
===========

This repository is a part of [PHPackager](http://github.com/covex-nn/PHPackager "PHPackager")

Example
-------

```php
<?php

  JooS\Stream\Wrapper_FS::register("any-vfs-protocol", __DIR__);

  file_put_contents("any-vfs-protocol://" . basename(__FILE__), "hahaha");
  unlink("any-vfs-protocol://" . basename(__FILE__));

  // JooS\Stream\Wrapper_FS::commit("any-vfs-protocol");
  // echo file_exists(__FILE__) ? "yes" : "no";

  JooS\Stream\Wrapper_FS::unregister("any-vfs-protocol");

?>
```


## Read more

Please visit [wiki](http://github.com/covex-nn/JooS_Stream/wiki/).
