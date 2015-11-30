<?php 

namespace ManaPHP\Mvc\Model {

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
	
	class Criteria implements \ManaPHP\Mvc\Model\CriteriaInterface, \ManaPHP\Di\InjectionAwareInterface {

		protected $_model;

		protected $_params;

		protected $_hiddenParamNumber;

		/**
		 * Sets the DependencyInjector container
		 *
		 * @param \ManaPHP\DiInterface $dependencyInjector
		 */
		public function setDI($dependencyInjector){ }


		/**
		 * Returns the DependencyInjector container
		 *
		 * @return \ManaPHP\DiInterface
		 */
		public function getDI(){ }


		/**
		 * Set a model on which the query will be executed
		 *
		 * @param string $modelName
		 * @return \ManaPHP\Mvc\Model\CriteriaInterface
		 */
		public function setModelName($modelName){ }


		/**
		 * Returns an internal model name on which the criteria will be applied
		 *
		 * @return string
		 */
		public function getModelName(){ }


		/**
		 * Sets the bound parameters in the criteria
		 * This method replaces all previously set bound parameters
		 *
		 * @param string $bindParams
		 * @return \ManaPHP\Mvc\Model\CriteriaInterface
		 */
		public function bind($bindParams){ }


		/**
		 * Sets the bind types in the criteria
		 * This method replaces all previously set bound parameters
		 *
		 * @param string $bindTypes
		 * @return \ManaPHP\Mvc\Model\CriteriaInterface
		 */
		public function bindTypes($bindTypes){ }


		/**
		 * Sets the columns to be queried
		 *
		 *<code>
		 *	$criteria->columns(array('id', 'name'));
		 *</code>
		 *
		 * @param string|array $columns
		 * @return \ManaPHP\Mvc\Model\CriteriaInterface
		 */
		public function columns($columns){ }


		/**
		 * Adds a join to the query
		 *
		 *<code>
		 *	$criteria->join('Robots');
		 *	$criteria->join('Robots', 'r.id = RobotsParts.robots_id');
		 *	$criteria->join('Robots', 'r.id = RobotsParts.robots_id', 'r');
		 *	$criteria->join('Robots', 'r.id = RobotsParts.robots_id', 'r', 'LEFT');
		 *</code>
		 *
		 * @param string $model
		 * @param string $conditions
		 * @param string $alias
		 * @param string $type
		 * @return \ManaPHP\Mvc\Model\CriteriaInterface
		 */
		public function join($model, $conditions=null, $alias=null, $type=null){ }


		/**
		 * Adds a INNER join to the query
		 *
		 *<code>
		 *	$criteria->innerJoin('Robots');
		 *	$criteria->innerJoin('Robots', 'r.id = RobotsParts.robots_id');
		 *	$criteria->innerJoin('Robots', 'r.id = RobotsParts.robots_id', 'r');
		 *	$criteria->innerJoin('Robots', 'r.id = RobotsParts.robots_id', 'r', 'LEFT');
		 *</code>
		 *
		 * @param string $model
		 * @param string $conditions
		 * @param string $alias
		 * @return \ManaPHP\Mvc\Model\CriteriaInterface
		 */
		public function innerJoin($model, $conditions=null, $alias=null){ }


		/**
		 * Adds a LEFT join to the query
		 *
		 *<code>
		 *	$criteria->leftJoin('Robots', 'r.id = RobotsParts.robots_id', 'r');
		 *</code>
		 *
		 * @param string $model
		 * @param string $conditions
		 * @param string $alias
		 * @return \ManaPHP\Mvc\Model\CriteriaInterface
		 */
		public function leftJoin($model, $conditions=null, $alias=null){ }


		/**
		 * Adds a RIGHT join to the query
		 *
		 *<code>
		 *	$criteria->rightJoin('Robots', 'r.id = RobotsParts.robots_id', 'r');
		 *</code>
		 *
		 * @param string $model
		 * @param string $conditions
		 * @param string $alias
		 * @return \ManaPHP\Mvc\Model\CriteriaInterface
		 */
		public function rightJoin($model, $conditions=null, $alias=null){ }


		/**
		 * Sets the conditions parameter in the criteria
		 *
		 * @param string $conditions
		 * @param array $bindParams
		 * @param array $bindTypes
		 * @return \ManaPHP\Mvc\Model\CriteriaInterface
		 */
		public function where($conditions,$bindParams,$bindTypes){ }


		/**
		 * Appends a condition to the current conditions using an AND operator (deprecated)
		 *
		 * @param string $conditions
		 * @param array $bindParams
		 * @param array $bindTypes
		 * @return \ManaPHP\Mvc\Model\CriteriaInterface
		 */
		public function addWhere($conditions, $bindParams=null, $bindTypes=null){ }


		/**
		 * Appends a condition to the current conditions using an AND operator
		 *
		 * @param string $conditions
		 * @param array $bindParams
		 * @param array $bindTypes
		 * @return \ManaPHP\Mvc\Model\CriteriaInterface
		 */
		public function andWhere($conditions, $bindParams=null, $bindTypes=null){ }


		/**
		 * Appends a condition to the current conditions using an OR operator
		 *
		 * @param string $conditions
		 * @param array $bindParams
		 * @param array $bindTypes
		 * @return \ManaPHP\Mvc\Model\CriteriaInterface
		 */
		public function orWhere($conditions, $bindParams=null, $bindTypes=null){ }


		/**
		 * Appends a BETWEEN condition to the current conditions
		 *
		 *<code>
		 *	$criteria->betweenWhere('price', 100.25, 200.50);
		 *</code>
		 *
		 * @param string $expr
		 * @param mixed $minimum
		 * @param mixed $maximum
		 * @return \ManaPHP\Mvc\Model\CriteriaInterface
		 */
		public function betweenWhere($expr, $minimum, $maximum){ }


		/**
		 * Appends a NOT BETWEEN condition to the current conditions
		 *
		 *<code>
		 *	$criteria->notBetweenWhere('price', 100.25, 200.50);
		 *</code>
		 *
		 * @param string $expr
		 * @param mixed $minimum
		 * @param mixed $maximum
		 * @return \ManaPHP\Mvc\Model\CriteriaInterface
		 */
		public function notBetweenWhere($expr, $minimum, $maximum){ }


		/**
		 * Appends an IN condition to the current conditions
		 *
		 *<code>
		 *	$criteria->inWhere('id', [1, 2, 3]);
		 *</code>
		 *
		 * @param string $expr
		 * @param array $values
		 * @return \ManaPHP\Mvc\Model\CriteriaInterface
		 */
		public function inWhere($expr, $values){ }


		/**
		 * Appends a NOT IN condition to the current conditions
		 *
		 *<code>
		 *	$criteria->notInWhere('id', [1, 2, 3]);
		 *</code>
		 *
		 * @param string $expr
		 * @param array $values
		 * @return \ManaPHP\Mvc\Model\CriteriaInterface
		 */
		public function notInWhere($expr, $values){ }


		/**
		 * Adds the conditions parameter to the criteria
		 *
		 * @param string $conditions
		 * @return \ManaPHP\Mvc\Model\CriteriaInterface
		 */
		public function conditions($conditions){ }


		/**
		 * Adds the order-by parameter to the criteria (deprecated)
		 *
		 * @param string $orderColumns
		 * @return \ManaPHP\Mvc\Model\CriteriaInterface
		 */
		public function order($orderColumns){ }


		/**
		 * Adds the order-by parameter to the criteria
		 *
		 * @param string $orderColumns
		 * @return \ManaPHP\Mvc\Model\CriteriaInterface
		 */
		public function orderBy($orderColumns){ }


		/**
		 * Adds the limit parameter to the criteria
		 *
		 * @param int $limit
		 * @param int $offset
		 * @return \ManaPHP\Mvc\Model\CriteriaInterface
		 */
		public function limit($limit, $offset=null){ }


		/**
		 * Adds the "for_update" parameter to the criteria
		 *
		 * @param boolean $forUpdate
		 * @return \ManaPHP\Mvc\Model\CriteriaInterface
		 */
		public function forUpdate($forUpdate=null){ }


		/**
		 * Adds the "shared_lock" parameter to the criteria
		 *
		 * @param boolean $sharedLock
		 * @return \ManaPHP\Mvc\Model\CriteriaInterface
		 */
		public function sharedLock($sharedLock=null){ }


		/**
		 * Returns the conditions parameter in the criteria
		 *
		 * @return string
		 */
		public function getWhere(){ }


		/**
		 * Return the columns to be queried
		 *
		 * @return string|array
		 */
		public function getColumns(){ }


		/**
		 * Returns the conditions parameter in the criteria
		 *
		 * @return string
		 */
		public function getConditions(){ }


		/**
		 * Returns the limit parameter in the criteria
		 *
		 * @return string
		 */
		public function getLimit(){ }


		/**
		 * Returns the order parameter in the criteria
		 *
		 * @return string
		 */
		public function getOrder(){ }


		/**
		 * Returns all the parameters defined in the criteria
		 *
		 * @return array
		 */
		public function getParams(){ }


		/**
		 * Builds a \ManaPHP\Mvc\Model\Criteria based on an input array like $_POST
		 *
		 * @param \ManaPHP\DiInterface $dependencyInjector
		 * @param string $modelName
		 * @param array $data
		 * @return \ManaPHP\Mvc\Model\Criteria
		 */
		public static function fromInput($dependencyInjector, $modelName, $data){ }


		/**
		 * Executes a find using the parameters built with the criteria
		 *
		 * @return \ManaPHP\Mvc\Model\ResultsetInterface
		 */
		public function execute(){ }


		/**
		 * Sets the cache options in the criteria
		 * This method replaces all previously set cache options
		 *
		 * @param array $options
		 * @return \ManaPHP\Mvc\Model\CriteriaInterface
		 */
		public function cache($options){ }

	}
}
