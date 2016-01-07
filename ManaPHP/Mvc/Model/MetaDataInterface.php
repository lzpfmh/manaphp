<?php

namespace ManaPHP\Mvc\Model {

    /**
     * ManaPHP\Mvc\Model\MetaDataInterface initializer
     */
    interface MetaDataInterface
    {

        /**
         * Returns table attributes names (fields)
         *
         * @param \ManaPHP\Mvc\ModelInterface $model
         * @return    array
         */
        public function getAttributes($model);


        /**
         * Returns an array of fields which are part of the primary key
         *
         * @param \ManaPHP\Mvc\ModelInterface $model
         * @return array
         */
        public function getPrimaryKeyAttributes($model);


        /**
         * Returns an array of fields which are not part of the primary key
         *
         * @param \ManaPHP\Mvc\ModelInterface $model
         * @return    array
         */
        public function getNonPrimaryKeyAttributes($model);

        /**
         * Returns attribute which is auto increment or null
         * @param $model
         * @return string |null
         */
        public function getAutoIncrementAttribute($model);


        /**
         * Returns attributes which types are numerical
         *
         * @param  \ManaPHP\Mvc\ModelInterface $model
         * @return array
         */
        public function getDataTypesNumeric($model);


        /**
         * Check if a model has certain attribute
         *
         * @param \ManaPHP\Mvc\ModelInterface $model
         * @param string $attribute
         * @return boolean
         */
        public function hasAttribute($model, $attribute);


        /**
         * Reads meta-data from the adapter
         *
         * @param string $key
         * @return array
         */
        public function read($key);


        /**
         * Writes meta-data to the adapter
         *
         * @param string $key
         * @param array $data
         */
        public function write($key, $data);

    }
}
