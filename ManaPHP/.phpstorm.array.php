<?php

namespace ManaPHP\Cache\Adapter\File{
    class ConstructOptions extends \ArrayObject{
        public $cacheDir;
        public $prefix=12;
    }
}

namespace ManaPHP\Cache\Adapter\Redis{
    class ConstructOptionsStub extends \ArrayObject{
        public $host;
        public $port;
        public $db;
        public $persistent;
        public $timeout;
    }
}