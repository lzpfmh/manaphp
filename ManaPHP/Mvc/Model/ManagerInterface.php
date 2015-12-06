<?php 

namespace ManaPHP\Mvc\Model {

	/**
	 * ManaPHP\Mvc\Model\ManagerInterface initializer
	 */
	
	interface ManagerInterface {

		/**
		 * Initializes a model in the model manager
		 *
		 * @param \ManaPHP\Mvc\ModelInterface $model
		 */
		public function initialize($model);


		/**
		 * Sets the mapped source for a model
		 *
		 * @param \ManaPHP\Mvc\Model $model
		 * @param string $source
		 */
		public function setModelSource($model, $source);


		/**
		 * Returns the mapped source for a model
		 *
		 * @param \ManaPHP\Mvc\Model $model
		 * @return string
		 */
		public function getModelSource($model);


		/**
		 * Sets the mapped schema for a model
		 *
		 * @param \ManaPHP\Mvc\Model $model
		 * @param string $schema
		 * @return string
		 */
		public function setModelSchema($model, $schema);


		/**
		 * Returns the mapped schema for a model
		 *
		 * @param \ManaPHP\Mvc\Model $model
		 * @return string
		 */
		public function getModelSchema($model);


		/**
		 * Loads a model throwing an exception if it does't exist
		 *
		 * @param string $modelName
		 * @param boolean $newInstance
		 * @return \ManaPHP\Mvc\ModelInterface
		 */
		public function load($modelName, $newInstance);

		/**
		 * Sets both write and read connection service for a model
		 *
		 * @param \ManaPHP\Mvc\ModelInterface $model
		 * @param string $connectionService
		 */
		public function setConnectionService($model, $connectionService);


		/**
		 * Sets write connection service for a model
		 *
		 * @param \ManaPHP\Mvc\ModelInterface $model
		 * @param string $connectionService
		 */
		public function setWriteConnectionService($model, $connectionService);


		/**
		 * Sets read connection service for a model
		 *
		 * @param \ManaPHP\Mvc\ModelInterface $model
		 * @param string $connectionService
		 */
		public function setReadConnectionService($model, $connectionService);


		/**
		 * Returns the connection to write data related to a model
		 *
		 * @param \ManaPHP\Mvc\ModelInterface $model
		 * @return \ManaPHP\Db\AdapterInterface
		 */
		public function getWriteConnection($model);


		/**
		 * Returns the connection to read data related to a model
		 *
		 * @param \ManaPHP\Mvc\ModelInterface $model
		 * @return \ManaPHP\Db\AdapterInterface
		 */
		public function getReadConnection($model);


		/**
		 * Returns the connection service name used to read data related to a model
		 *
		 * @param \ManaPHP\Mvc\ModelInterface $model
		 * @return string
		 */
		public function getReadConnectionService($model);


		/**
		 * Returns the connection service name used to write data related to a model
		 *
		 * @param \ManaPHP\Mvc\ModelInterface $model
		 * @return string
		 */
		public function getWriteConnectionService($model);


		/**
		 * Creates a \ManaPHP\Mvc\Model\Query without execute it
		 *
		 * @param string $phql
		 * @return \ManaPHP\Mvc\Model\QueryInterface
		 */
		public function createQuery($phql);


		/**
		 * Creates a \ManaPHP\Mvc\Model\Query and execute it
		 *
		 * @param string $phql
		 * @param array $placeholders
		 * @param array $bindTypes
		 * @return \ManaPHP\Mvc\Model\QueryInterface
		 */
		public function executeQuery($phql, $placeholders=null,$bindTypes=null);


		/**
		 * Creates a \ManaPHP\Mvc\Model\Query\Builder
		 *
		 * @param string $params
		 * @return \ManaPHP\Mvc\Model\Query\BuilderInterface
		 */
		public function createBuilder($params=null);


		/**
		 * Returns the last query created or executed in the
		 *
		 * @return \ManaPHP\Mvc\Model\QueryInterface
		 */
		public function getLastQuery();
	}
}
