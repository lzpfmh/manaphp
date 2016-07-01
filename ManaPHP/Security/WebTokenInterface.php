<?php
namespace ManaPHP\Security {

    interface WebTokenInterface
    {
        /**
         * @param int $ttl
         * @return string
         */
        public function create($ttl);
    }
}