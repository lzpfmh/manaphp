<?php
namespace ManaPHP\Logger\Adapter {

    use ManaPHP\Logger\AdapterInterface;
    use ManaPHP\Logger\Exception;

    class File implements AdapterInterface
    {

        /**
         * @var string
         */
        protected $_file;

        /**
         * @var resource
         */
        protected $_fileHandle;

        /**
         * \ManaPHP\Logger\Adapter\File constructor.
         *
         * @param string $file
         */
        public function __construct($file)
        {
            $this->_file = $file;
        }

        /**
         * @param string $level
         * @param string $message
         * @param array $context
         * @throws \ManaPHP\Logger\Exception
         */
        public function log($level, $message, $context = null)
        {
            if ($this->_fileHandle === null) {
                $dir = dirname($this->_file);
                if (!file_exists($dir)) {
                    mkdir($dir, 0755, true);
                }

                $this->_fileHandle = fopen($this->_file, 'a');
                if ($this->_fileHandle === false) {
                    throw new Exception('Can\'t open log file: ' . $this->_file);
                }
            }

            if ($this->_fileHandle !== false) {
                if (fwrite($this->_fileHandle, $message) === false) {
                    throw new Exception('Write the log to file failed: ' . $this->_fileHandle);
                }
            }
        }
    }
}