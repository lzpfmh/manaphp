<?php 

namespace ManaPHP\Mvc\Model {
	use \ManaPHP\Di\InjectionAwareInterface;
	use \ManaPHP\Events\EventsAwareInterface;
	/**
	 * ManaPHP\Mvc\Model\Manager
	 *
	 * This components controls the initialization of models, keeping record of relations
	 * between the different models of the application.
	 *
	 * A ModelsManager is injected to a model via a Dependency Injector/Services Container such as ManaPHP\Di.
	 *
	 * <code>
	 * $di = new ManaPHP\Di();
	 *
	 * $di->set('modelsManager', function() {
	 *      return new ManaPHP\Mvc\Model\Manager();
	 * });
	 *
	 * $robot = new Robots($di);
	 * </code>
	 */
	
	class Manager implements ManagerInterface, InjectionAwareInterface, EventsAwareInterface {

		/**
		 * @var \ManaPHP\DiInterface
		 */
		protected $_dependencyInjector;

		protected $_eventsManager;

		protected $_readConnectionServices=[];

		protected $_writeConnectionServices=[];

		protected $_aliases;

		protected $_initialized=[];

		protected $_sources=[];

		protected $_schemas=[];

		protected $_lastQuery;

		protected $_namespaceAliases;

		/**
		 * Sets the DependencyInjector container
		 *
		 * @param \ManaPHP\DiInterface $dependencyInjector
		 */
		public function setDI($dependencyInjector){
			$this->_dependencyInjector =$dependencyInjector;
		}


		/**
		 * Returns the DependencyInjector container
		 *
		 * @return \ManaPHP\DiInterface
		 */
		public function getDI(){
			return $this->_dependencyInjector;
		}


		/**
		 * Sets a global events manager
		 *
		 * @param \ManaPHP\Events\ManagerInterface $eventsManager
		 * @return $this
		 */
		public function setEventsManager($eventsManager){
			$this->_eventsManager=$eventsManager;
			return $this;
		}


		/**
		 * Returns the internal event manager
		 *
		 * @return \ManaPHP\Events\ManagerInterface
		 */
		public function getEventsManager(){
			return $this->_eventsManager;
		}


		/**
		 * Initializes a model in the model manager
		 *
		 * @param \ManaPHP\Mvc\ModelInterface $model
		 * @return boolean
		 */
		public function initialize($model){
			$className =get_class($model);

			/**
			 * Models are just initialized once per request
			 */
			if(isset($this->_initialized[$className])){
				return false;
			}

			/**
			 * Update the model as initialized, this avoid cyclic initializations
			 */
			$this->_initialized[$className]=$model;

			if(method_exists($model,'initialize')){
				$model->initialize();
			}

			return true;
		}


		/**
		 * Loads a model throwing an exception if it does't exist
		 *
		 * @param  string $modelName
		 * @param  boolean $newInstance
		 * @return \ManaPHP\Mvc\ModelInterface
		 * @throws \ManaPHP\Mvc\Model\Exception
		 */
		public function load($modelName, $newInstance){
			if(isset($this->_initialized[$modelName])){
				if($newInstance){
					return new $modelName($this->_dependencyInjector,$this);
				}
				$model =$this->_initialized[$modelName];
				return $model;
			}else{
				if(class_exists($modelName)){
					return new $modelName($this->_dependencyInjector, $this);
				}

				throw new Exception("Model '" . $modelName . "' could not be loaded");
			}
		}


		/**
		 * Sets the mapped source for a model
		 *
		 * @param \ManaPHP\Mvc\Model $model
		 * @param string $source
		 */
		public function setModelSource($model, $source){
			$modelName =get_class($model);
			$this->_sources[$modelName]=$source;
		}


		/**
		 * Returns the mapped source for a model
		 *
		 * @param \ManaPHP\Mvc\Model $model
		 * @return string
		 * @throws \ManaPHP\Mvc\Model\Exception
		 */
		public function getModelSource($model){
			$className =get_class($model);

			if(!isset($this->_sources[$className])){
				$this->_sources[$className]=$model;
			}
			throw new Exception('The source is not provided: '.get_class($model));
		}


		/**
		 * Sets the mapped schema for a model
		 *
		 * @param \ManaPHP\Mvc\Model $model
		 * @param string $schema
		 * @return string
		 */
		public function setModelSchema($model, $schema){
			$className=get_class($model);
			$this->_schemas[$className]=$schema;
		}


		/**
		 * Returns the mapped schema for a model
		 *
		 * @param \ManaPHP\Mvc\Model $model
		 * @return string
		 */
		public function getModelSchema($model){
			$className =get_class($model);
			if(isset($this->_schemas[$className])){
				return $this->_schemas[$className];
			}else{
				return '';
			}
		}


		/**
		 * Sets both write and read connection service for a model
		 *
		 * @param \ManaPHP\Mvc\ModelInterface $model
		 * @param string $connectionService
		 */
		public function setConnectionService($model, $connectionService){
			$className=get_class($model);
			$this->_readConnectionServices[$className]=$connectionService;
			$this->_writeConnectionServices[$className]=$connectionService;
		}


		/**
		 * Sets write connection service for a model
		 *
		 * @param \ManaPHP\Mvc\ModelInterface $model
		 * @param string $connectionService
		 */
		public function setWriteConnectionService($model, $connectionService){
			$className=get_class($model);
			$this->_writeConnectionServices[$className]=$connectionService;
		}


		/**
		 * Sets read connection service for a model
		 *
		 * @param \ManaPHP\Mvc\ModelInterface $model
		 * @param string $connectionService
		 */
		public function setReadConnectionService($model, $connectionService){
			$className=get_class($model);
			$this->_readConnectionServices[$className]=$connectionService;
		}


		/**
		 * Returns the connection to read or write data related to a model depending on the connection services.
		 * @param \ManaPHP\Mvc\ModelInterface $model
		 * @param string[] $connectionServices
		 * @return \ManaPHP\Db\AdapterInterface
		 */
		protected function _getConnection($model, $connectionServices){
			$className =get_class($model);
			$serviceName=isset($connectionServices[$className])?$connectionServices[$className]:'db';
			$connection =$this->_dependencyInjector->getShared($serviceName);

			return $connection;
		}
		/**
		 * Returns the connection to write data related to a model
		 *
		 * @param \ManaPHP\Mvc\ModelInterface $model
		 * @return \ManaPHP\Db\AdapterInterface
		 */
		public function getWriteConnection($model){
			return $this->_getConnection($model,$this->_writeConnectionServices);
		}


		/**
		 * Returns the connection to read data related to a model
		 *
		 * @param \ManaPHP\Mvc\ModelInterface $model
		 * @return \ManaPHP\Db\AdapterInterface
		 */
		public function getReadConnection($model){
			return $this->_getConnection($model,$this->_readConnectionServices);
		}

		/**
		 * Returns the connection service name used to read or write data related to a model depending on the connection services
		 * @param \ManaPHP\Mvc\ModelInterface $model
		 * @param string[] $connectionServices
		 * @return string
		 */
		public function _getConnectionService($model, $connectionServices){
			$className=get_class($model);
			return isset($connectionServices[$className])?$connectionServices[$className]:'db';
		}
		/**
		 * Returns the connection service name used to read data related to a model
		 *
		 * @param \ManaPHP\Mvc\ModelInterface $model
		 * @return string
		 */
		public function getReadConnectionService($model){
			return $this->_getConnectionService($model,$this->_readConnectionServices);
		}


		/**
		 * Returns the connection service name used to write data related to a model
		 *
		 * @param \ManaPHP\Mvc\ModelInterface $model
		 * @return string
		 */
		public function getWriteConnectionService($model){
			return $this->_getConnectionService($model,$this->_writeConnectionServices);
		}


		/**
		 * Creates a \ManaPHP\Mvc\Model\Query without execute it
		 *
		 * @param string $phql
		 * @return \ManaPHP\Mvc\Model\QueryInterface
		 */
		public function createQuery($phql){
			/**
			 * @var $query \ManaPHP\Mvc\Model\Query
			 */
			$query =$this->_dependencyInjector->get('ManaPHP\Mvc\Model\Query',[$phql, $this->_dependencyInjector]);
			$this->_lastQuery =$query;
			return $query;
		}


		/**
		 * Creates a \ManaPHP\Mvc\Model\Query and execute it
		 *
		 * @param string $phql
		 * @param array $placeholders
		 * @param array $bindTypes
		 * @return \ManaPHP\Mvc\Model\QueryInterface
		 * @throws \ManaPHP\Mvc\Model\Exception
		 */
		public function executeQuery($phql, $placeholders=null,$bindTypes=null){
			/**
			 * @var $query \ManaPHP\Mvc\Model\Query
			 */
			$query =$this->_dependencyInjector->get('ManaPHP\Mvc\Model\Query',[$phql, $this->_dependencyInjector]);
			$this->_lastQuery =$query;
			if(is_array($placeholders)){
				$query->setBindParams($placeholders);
			}

			if(is_array($bindTypes)){
				$query->setBindTypes($bindTypes);
			}

			return $query->execute();
		}


		/**
		 * Creates a \ManaPHP\Mvc\Model\Query\Builder
		 *
		 * @param string $params
		 * @return \ManaPHP\Mvc\Model\Query\BuilderInterface
		 */
		public function createBuilder($params=null){
			return $this->_dependencyInjector->get('ManaPHP\Mvc\Model\Query\Builder',[$params, $this->_dependencyInjector]);
		}


		/**
		 * Returns the latest query created or executed in the models manager
		 *
		 * @return \ManaPHP\Mvc\Model\QueryInterface
		 */
		public function getLastQuery(){
			return $this->_lastQuery;
		}


		/**
		 * Registers shorter aliases for namespaces in PHQL statements
		 *
		 * @param string $alias
		 * @param string $namespace
		 */
		public function registerNamespaceAlias($alias, $namespace){
			$this->_namespaceAliases[$alias]=$namespace;
		}


		/**
		 * Returns a real namespace from its alias
		 *
		 * @param string $alias
		 * @return string
		 * @throws \ManaPHP\Mvc\Model\Exception
		 */
		public function getNamespaceAlias($alias){
			if(isset($this->_namespaceAliases[$alias])){
				return $this->_namespaceAliases[$alias];
			}else{
				throw new Exception("Namespace alias '" . $alias . "' is not registered");
			}
		}
	}
}
