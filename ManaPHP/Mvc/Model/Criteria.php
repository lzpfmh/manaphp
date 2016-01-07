<?php

namespace ManaPHP\Mvc\Model {

    use \ManaPHP\Di\InjectionAwareInterface;

    /**
     * ManaPHP\Mvc\Model\Criteria
     *
     * This class allows to build the array parameter required by ManaPHP\Mvc\Model::find
     * and ManaPHP\Mvc\Model::findFirst using an object-oriented interface
     *
     *<code>
     *$robots = Robots::query()
     *    ->where("type = :type:")
     *    ->andWhere("year < 2000")
     *    ->bind(array("type" => "mechanical"))
     *    ->order("name")
     *    ->execute();
     *</code>
     */
    class Criteria implements CriteriaInterface, InjectionAwareInterface
    {

        protected $_model;

        protected $_params;

        protected $_hiddenParamNumber;

        /**
         * Sets the DependencyInjector container
         *
         * @param \ManaPHP\DiInterface $dependencyInjector
         */
        public function setDI($dependencyInjector)
        {
            $this->_params['di'] = $dependencyInjector;
        }


        /**
         * Returns the DependencyInjector container
         *
         * @return \ManaPHP\DiInterface
         */
        public function getDI()
        {
            return isset($this->_params['di']) ? $this->_params['di'] : null;
        }


        /**
         * Set a model on which the query will be executed
         *
         * @param string $modelName
         * @return static
         */
        public function setModelName($modelName)
        {
            $this->_model = $modelName;
            return $this;
        }


        /**
         * Returns an internal model name on which the criteria will be applied
         *
         * @return string
         */
        public function getModelName()
        {
            return $this->_model;
        }


        /**
         * Sets the bound parameters in the criteria
         * This method replaces all previously set bound parameters
         *
         * @param string $bindParams
         * @return static
         */
        public function bind($bindParams)
        {
            $this->_params['bind'] = $bindParams;
            return $this;
        }


        /**
         * Sets the bind types in the criteria
         * This method replaces all previously set bound parameters
         *
         * @param string $bindTypes
         * @return static
         */
        public function bindTypes($bindTypes)
        {
            $this->_params['bindTypes'] = $$bindTypes;
            return $this;
        }

        /**
         * Sets SELECT DISTINCT / SELECT ALL flag
         *
         * @param mixed $distinct
         * @return static
         */
        public function distinct($distinct)
        {
            $this->_params['distinct'] = $distinct;
            return $this;
        }


        /**
         * Sets the columns to be queried
         *
         *<code>
         *    $criteria->columns(array('id', 'name'));
         *</code>
         *
         * @param string|array $columns
         * @return static
         */
        public function columns($columns)
        {
            $this->_params['columns'] = $columns;
            return $this;
        }


        /**
         * Adds a join to the query
         *
         *<code>
         *    $criteria->join('Robots');
         *    $criteria->join('Robots', 'r.id = RobotsParts.robots_id');
         *    $criteria->join('Robots', 'r.id = RobotsParts.robots_id', 'r');
         *    $criteria->join('Robots', 'r.id = RobotsParts.robots_id', 'r', 'LEFT');
         *</code>
         *
         * @param string $model
         * @param string $conditions
         * @param string $alias
         * @param string $type
         * @return static
         */
        public function join($model, $conditions = null, $alias = null, $type = null)
        {
            if (!isset($this->_params['joins'])) {
                $this->_params = [];
            }
            $this->_params[] = [$model, $conditions, $alias, $type];
            return $this;
        }


        /**
         * Adds a INNER join to the query
         *
         *<code>
         *    $criteria->innerJoin('Robots');
         *    $criteria->innerJoin('Robots', 'r.id = RobotsParts.robots_id');
         *    $criteria->innerJoin('Robots', 'r.id = RobotsParts.robots_id', 'r');
         *    $criteria->innerJoin('Robots', 'r.id = RobotsParts.robots_id', 'r', 'LEFT');
         *</code>
         *
         * @param string $model
         * @param string $conditions
         * @param string $alias
         * @return static
         */
        public function innerJoin($model, $conditions = null, $alias = null)
        {
            return $this->join($model, $conditions, $alias, 'INNER');
        }


        /**
         * Adds a LEFT join to the query
         *
         *<code>
         *    $criteria->leftJoin('Robots', 'r.id = RobotsParts.robots_id', 'r');
         *</code>
         *
         * @param string $model
         * @param string $conditions
         * @param string $alias
         * @return static
         */
        public function leftJoin($model, $conditions = null, $alias = null)
        {
            return $this->join($model, $conditions, $alias, 'LEFT');
        }


        /**
         * Adds a RIGHT join to the query
         *
         *<code>
         *    $criteria->rightJoin('Robots', 'r.id = RobotsParts.robots_id', 'r');
         *</code>
         *
         * @param string $model
         * @param string $conditions
         * @param string $alias
         * @return static
         */
        public function rightJoin($model, $conditions = null, $alias = null)
        {
            return $this->join($model, $conditions, $alias, 'RIGHT');
        }


        /**
         * Sets the conditions parameter in the criteria
         *
         * @param string $conditions
         * @param array $bindParams
         * @param array $bindTypes
         * @return static
         * @throws \ManaPHP\Mvc\Model\Exception
         */
        public function where($conditions, $bindParams = null, $bindTypes = null)
        {
            $this->_params['conditions'] = $conditions;

            if (is_array($bindParams)) {
                if (!isset($this->_params['bind'])) {
                    $this->_params['bind'] = [];
                }
                $this->_params['bind'] = array_merge($this->_params['bind'], $bindParams);
            }
            if ($bindTypes !== null) {
                throw new Exception('bindTypes not support.');
            }

            return $this;
        }


        /**
         * Appends a condition to the current conditions using an AND operator
         *
         * @param string $conditions
         * @param array $bindParams
         * @param array $bindTypes
         * @return static
         * @throws \ManaPHP\Mvc\Model\Exception
         */
        public function andWhere($conditions, $bindParams = null, $bindTypes = null)
        {
            if (isset($this->_params['conditions'])) {
                $this->_params['conditions'] = '(' . $this->_params['conditions'] . ') AND (' . $conditions . ')';
            } else {
                $this->_params['conditions'] = $conditions;
            }

            if (is_array($bindParams)) {
                if (!isset($this->_params['bind'])) {
                    $this->_params['bind'] = [];
                }
                $this->_params['bind'] = array_merge($this->_params['bind'], $bindParams);
            }

            if ($bindTypes !== null) {
                throw new Exception('bindTypes not support.');
            }

            return $this;
        }


        /**
         * Appends a condition to the current conditions using an OR operator
         *
         * @param string $conditions
         * @param array $bindParams
         * @param array $bindTypes
         * @return static
         * @throws \ManaPHP\Mvc\Model\Exception
         */
        public function orWhere($conditions, $bindParams = null, $bindTypes = null)
        {
            if (isset($this->_params['conditions'])) {
                $this->_params['conditions'] = '(' . $this->_params['conditions'] . ') OR (' . $conditions . ')';
            } else {
                $this->_params['conditions'] = $conditions;
            }

            if (is_array($bindParams)) {
                if (!isset($this->_params['bind'])) {
                    $this->_params['bind'] = [];
                }
                $this->_params['bind'] = array_merge($this->_params['bind'], $bindParams);
            }

            if ($bindTypes !== null) {
                throw new Exception('bindTypes not support.');
            }

            return $this;
        }


        /**
         * Appends a BETWEEN condition to the current conditions
         *
         *<code>
         *    $criteria->betweenWhere('price', 100.25, 200.50);
         *</code>
         *
         * @param string $expr
         * @param mixed $minimum
         * @param mixed $maximum
         * @return static
         */
        public function betweenWhere($expr, $minimum, $maximum)
        {
            $min_key = 'ABP' . $this->_hiddenParamNumber++;
            $max_key = 'ABP' . $this->_hiddenParamNumber++;

            /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
            $this->andWhere($expr . 'BETWEEN :' . $min_key . ': AND :' . $max_key . ':', [$min_key => $minimum, $max_key => $maximum]);

            return $this;
        }


        /**
         * Appends a NOT BETWEEN condition to the current conditions
         *
         *<code>
         *    $criteria->notBetweenWhere('price', 100.25, 200.50);
         *</code>
         *
         * @param string $expr
         * @param mixed $minimum
         * @param mixed $maximum
         * @return static
         */
        public function notBetweenWhere($expr, $minimum, $maximum)
        {
            $min_key = 'ABP' . $this->_hiddenParamNumber++;
            $max_key = 'ABP' . $this->_hiddenParamNumber++;

            /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
            $this->andWhere($expr . ' NOT BETWEEN :' . $min_key . ': AND :' . $max_key . ':', [$min_key => $minimum, $max_key => $maximum]);
            return $this;
        }


        /**
         * Appends an IN condition to the current conditions
         *
         *<code>
         *    $criteria->inWhere('id', [1, 2, 3]);
         *</code>
         *
         * @param string $expr
         * @param array $values
         * @return static
         */
        public function inWhere($expr, $values)
        {
            $bind_params = [];
            $bind_keys = [];

            foreach ($values as $value) {
                $key = 'ABP' . $this->_hiddenParamNumber++;
                $bind_keys[] = ':' . $key . ':';
                $bind_params[$key] = $value;
            }

            /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
            $this->andWhere($expr, ' IN (' . implode(',', $bind_keys) . ')', $bind_params);
            return $this;
        }


        /**
         * Appends a NOT IN condition to the current conditions
         *
         *<code>
         *    $criteria->notInWhere('id', [1, 2, 3]);
         *</code>
         *
         * @param string $expr
         * @param array $values
         * @return static
         */
        public function notInWhere($expr, $values)
        {
            $bind_params = [];
            $bind_keys = [];

            foreach ($values as $value) {
                $key = 'ABP' . $this->_hiddenParamNumber++;
                $bind_keys[] = ':' . $key . ':';
                $bind_params[$key] = $value;
            }

            /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
            $this->andWhere($expr, ' NOT IN (' . implode(',', $bind_keys) . ')', $bind_params);
            return $this;
        }


        /**
         * Adds the conditions parameter to the criteria
         *
         * @param string $conditions
         * @return static
         */
        public function conditions($conditions)
        {
            $this->_params['conditions'] = $conditions;
            return $this;
        }

        /**
         * Adds the order-by parameter to the criteria
         *
         * @param string $orderColumns
         * @return static
         */
        public function orderBy($orderColumns)
        {
            $this->_params['order'] = $orderColumns;
            return $this;
        }


        /**
         * Adds the limit parameter to the criteria
         *
         * @param int $limit
         * @param int $offset
         * @return static
         */
        public function limit($limit, $offset = null)
        {
            if ($offset === null) {
                $this->_params['limit'] = $limit;
            } else {
                $this->_params['limit'] = ['number' => $limit, 'offset' => $offset];
            }

            return $this;
        }


        /**
         * Adds the "for_update" parameter to the criteria
         *
         * @param boolean $forUpdate
         * @return static
         */
        public function forUpdate($forUpdate = null)
        {
            $this->_params['for_update'] = $forUpdate;
            return $this;
        }


        /**
         * Adds the "shared_lock" parameter to the criteria
         *
         * @param boolean $sharedLock
         * @return static
         */
        public function sharedLock($sharedLock = null)
        {
            $this->_params['shared_lock'] = $sharedLock;
            return $this;
        }


        /**
         * Returns the conditions parameter in the criteria
         *
         * @return string
         */
        public function getWhere()
        {
            return isset($this->_params['conditions']) ? $this->_params['conditions'] : null;
        }


        /**
         * Return the columns to be queried
         *
         * @return string|array
         */
        public function getColumns()
        {
            return isset($this->_params['columns']) ? $this->_params['columns'] : null;
        }


        /**
         * Returns the conditions parameter in the criteria
         *
         * @return string
         */
        public function getConditions()
        {
            return isset($this->_params['conditions']) ? $this->_params['conditions'] : null;
        }


        /**
         * Returns the limit parameter in the criteria
         *
         * @return string
         */
        public function getLimit()
        {
            return isset($this->_params['limit']) ? $this->_params['limit'] : null;
        }


        /**
         * Returns the order parameter in the criteria
         *
         * @return string
         */
        public function getOrder()
        {
            return isset($this->_params['order']) ? $this->_params['order'] : null;
        }


        /**
         * Returns all the parameters defined in the criteria
         *
         * @return array
         */
        public function getParams()
        {
            return $this->_params;
        }


        /**
         * Builds a \ManaPHP\Mvc\Model\Criteria based on an input array like $_POST
         *
         * @param \ManaPHP\DiInterface $dependencyInjector
         * @param string $modelName
         * @param array $data
         * @return static
         */
        public static function fromInput($dependencyInjector, $modelName, $data)
        {
        }


        /**
         * Executes a find using the parameters built with the criteria
         *
         * @return \ManaPHP\Mvc\Model\ResultsetInterface
         */
        public function execute()
        {
            /**
             * @var \ManaPHP\Mvc\ModelInterface $model
             */
            $model = $this->_model;

            return $model::find($this->getParams());
        }


        /**
         * Sets the cache options in the criteria
         * This method replaces all previously set cache options
         *
         * @param array $options
         * @return static
         */
        public function cache($options)
        {
            $this->_params['cache'] = $options;
            return $this;
        }

    }
}
