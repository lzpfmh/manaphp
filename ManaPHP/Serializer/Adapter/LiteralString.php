<?php
namespace ManaPHP\Serializer\Adapter {

    use ManaPHP\Serializer\AdapterInterface;
    use ManaPHP\Serializer\Exception;

    class LiteralString implements AdapterInterface
    {
        public function serialize($data, $context = null)
        {
            if (is_string($data)) {
                return $data;
            } elseif ($data === false || $data === null) {
                return '';
            } else {
                throw new Exception('is not string');
            }
        }

        public function deserialize($serialized, $content = null)
        {
            return $serialized;
        }
    }
}