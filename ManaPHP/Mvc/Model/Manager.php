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

		protected $_customEventsManager;

		protected $_readConnectionServices;

		protected $_writeConnectionServices;

		protected $_aliases;

		protected $_hasMany;

		protected $_hasManySingle;

		protected $_hasOne;

		protected $_hasOneSingle;

		protected $_belongsTo;

		protected $_belongsToSingle;

		protected $_hasManyToMany;

		protected $_hasManyToManySingle;

		protected $_initialized;

		protected $_sources;

		protected $_schemas;

		protected $_behaviors;

		protected $_lastQuery;

		protected $_reusable;

		protected $_keepSnapshots;

		protected $_dynamicUpdate;

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
		 * Sets a custom events manager for a specific model
		 *
		 * @param \ManaPHP\Mvc\ModelInterface $model
		 * @param \ManaPHP\Events\ManagerInterface $eventsManager
		 */
		public function setCustomEventsManager($model, $eventsManager){
			$this->_customEventsManager[strtolower(get_class($model))]=$eventsManager;
		}


		/**
		 * Returns a custom events manager related to a model
		 *
		 * @param \ManaPHP\Mvc\ModelInterface $model
		 * @return \ManaPHP\Events\ManagerInterface
		 */
		public function getCustomEventsManager($model){
			$className =strtolower(get_class($model));
			if(is_array($this->_customEventsManager)){
				if(isset($this->_customEventsManager[$className])){
					return $this->_customEventsManager[$className];
				}
			}

			return false;
		}


		/**
		 * Initializes a model in the model manager
		 *
		 * @param \ManaPHP\Mvc\ModelInterface $model
		 * @return boolean
		 */
		public function initialize($model){
			$className =strtolower(get_class($model));
			if(isset($this->_initialized[$className])){
				return false;
			}

			$this->_initialized[$className]=$model;

			if(method_exists($model,'initialize')){
				$model->initialize();
			}

			return true;
		}


		/**
		 * Check whether a model is already initialized
		 *
		 * @param string $modelName
		 * @return bool
		 */
		public function isInitialized($modelName){
			return isset($this->_initialized[strtolower($modelName)]);
		}

		/**
		 * Loads a model throwing an exception if it does't exist
		 *
		 * @param  string $modelName
		 * @param  boolean $newInstance
		 * @return \ManaPHP\Mvc\ModelInterface
		 */
		public function load($modelName, $newInstance){

		}


		/**
		 * Sets the mapped source for a model
		 *
		 * @param \ManaPHP\Mvc\Model $model
		 * @param string $source
		 */
		public function setModelSource($model, $source){
			$this->_sources[strtolower(get_class($model))]=$source;
		}


		/**
		 * Returns the mapped source for a model
		 *
		 * @param \ManaPHP\Mvc\Model $model
		 * @return string
		 */
		public function getModelSource($model){
			$className =strtolower(get_class($model));
			if(is_array($this->_sources)){
				if(isset($this->_sources[$className])){
					return $this->_sources[$className];
				}
			}
			$this->_sources[$className]=s;
		}


		/**
		 * Sets the mapped schema for a model
		 *
		 * @param \ManaPHP\Mvc\Model $model
		 * @param string $schema
		 * @return string
		 */
		public function setModelSchema($model, $schema){
			$this->_schemas[get_class($model)]=$schema;
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
			if(is_array($connectionServices) &&isset($connectionServices[$className])){
				$serviceName =$connectionServices[$className];
			}else{
				$serviceName ='db';
			}

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
			if(is_array($connectionServices)){
				if(isset($connectionServices[$className])){
					return $connectionServices[$className];
				}
			}

			return 'db';
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
		 * Receives events generated in the models and dispatches them to a events-manager if available
		 * Notify the behaviors that are listening in the model
		 *
		 * @param string $eventName
		 * @param \ManaPHP\Mvc\ModelInterface $model
		 */
		public function notifyEvent($eventName, $model){ }


		/**
		 * Dispatch a event to the listeners and behaviors
		 * This method expects that the endpoint listeners/behaviors returns true
		 * meaning that a least one is implemented
		 *
		 * @param \ManaPHP\Mvc\ModelInterface $model
		 * @param string $eventName
		 * @param array $data
		 * @return boolean
		 */
		public function missingMethod($model, $eventName, $data){ }



		/**
		 * Returns a reusable object from the internal list
		 *
		 * @param string $modelName
		 * @param string $key
		 * @return object
		 */
		public function getReusableRecords($modelName, $key){ }


		/**
		 * Stores a reusable record in the internal list
		 *
		 * @param string $modelName
		 * @param string $key
		 * @param mixed $records
		 */
		public function setReusableRecords($modelName, $key, $records){ }


		/**
		 * Clears the internal reusable list
		 *
		 * @param
		 */
		public function clearReusableObjects(){ }


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
		public function getLastQuery(){ }


		/**
		 * Registers shorter aliases for namespaces in PHQL statements
		 *
		 * @param string $alias
		 * @param string $namespace
		 */
		public function registerNamespaceAlias($alias, $namespace){ }


		/**
		 * Returns a real namespace from its alias
		 *
		 * @param string $alias
		 * @return string
		 */
		public function getNamespaceAlias($alias){ }


		/**
		 * Returns all the registered namespace aliases
		 *
		 * @return array
		 */
		public function getNamespaceAliases(){ }


		/**
		 * Destroys the PHQL cache
		 */
		public function __destruct(){ }

	}
}
