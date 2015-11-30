<?php 

namespace ManaPHP\Mvc {

	use ManaPHP\Di;
	use ManaPHP\Mvc\Model\Exception;
	use \ManaPHP\Mvc\Model\ResultInterface;
	use \ManaPHP\Di\InjectionAwareInterface;
	use ManaPHP\Mvc\Model\Resultset;

	/**
	 * ManaPHP\Mvc\Model
	 *
	 * <p>ManaPHP\Mvc\Model connects business objects and database tables to create
	 * a persistable domain model where logic and data are presented in one wrapping.
	 * It‘s an implementation of the object-relational mapping (ORM).</p>
	 *
	 * <p>A model represents the information (data) of the application and the rules to manipulate that data.
	 * Models are primarily used for managing the rules of interaction with a corresponding database table.
	 * In most cases, each table in your database will correspond to one model in your application.
	 * The bulk of your application’s business logic will be concentrated in the models.</p>
	 *
	 * <p>ManaPHP\Mvc\Model is the first ORM written in C-language for PHP, giving to developers high performance
	 * when interacting with databases while is also easy to use.</p>
	 *
	 * <code>
	 *
	 * $robot = new Robots();
	 * $robot->type = 'mechanical';
	 * $robot->name = 'Astro Boy';
	 * $robot->year = 1952;
	 * if ($robot->save() == false) {
	 *  echo "Umh, We can store robots: ";
	 *  foreach ($robot->getMessages() as $message) {
	 *    echo $message;
	 *  }
	 * } else {
	 *  echo "Great, a new robot was saved successfully!";
	 * }
	 * </code>
	 *
	 */
	
	abstract class Model implements ModelInterface, ResultInterface, InjectionAwareInterface, \Serializable {

		const OP_NONE = 0;

		const OP_CREATE = 1;

		const OP_UPDATE = 2;

		const OP_DELETE = 3;

		const DIRTY_STATE_PERSISTENT = 0;

		const DIRTY_STATE_TRANSIENT = 1;

		const DIRTY_STATE_DETACHED = 2;

		protected $_dependencyInjector;

		/**
		 * @var \ManaPHP\Mvc\Model\ManagerInterface
		 */
		protected $_modelsManager;

		protected $_modelsMetaData;

		protected $_errorMessages;

		protected $_operationMade;

		protected $_dirtyState;

		protected $_transaction;

		protected $_uniqueKey;

		protected $_uniqueParams;

		protected $_uniqueTypes;

		protected $_skipped;

		protected $_related;

		protected $_snapshot;

		/**
		 * \ManaPHP\Mvc\Model constructor
		 *
		 * @param \ManaPHP\DiInterface $dependencyInjector
		 * @param \ManaPHP\Mvc\Model\ManagerInterface $modelsManager
		 */
		final public function __construct($dependencyInjector=null, $modelsManager=null){
			$this->_dependencyInjector=$dependencyInjector===null?Di::getDefault():$dependencyInjector;
			$this->_modelsManager=$modelsManager ===null?$this->_dependencyInjector->getShared('modelsManager'):$modelsManager;

			/**
			 * The manager always initializes the object
			 */
			$this->_modelsManager->initialize($this);

			if(method_exists($this,'onConstruct')){
				$this->onConstruct();
			}
		}


		/**
		 * Sets the dependency injection container
		 *
		 * @param \ManaPHP\DiInterface $dependencyInjector
		 * @return \ManaPHP\Mvc\ModelInterface
		 */
		public function setDI($dependencyInjector){
			$this->_dependencyInjector =$dependencyInjector;
			return $this;
		}


		/**
		 * Returns the dependency injection container
		 *
		 * @return \ManaPHP\DiInterface
		 */
		public function getDI(){
			return $this->_dependencyInjector;
		}


		/**
		 * Sets a custom events manager
		 *
		 * @param \ManaPHP\Events\ManagerInterface $eventsManager
		 * @return \ManaPHP\Mvc\ModelInterface
		 */
		protected function setEventsManager($eventsManager){
			$this->_modelsManager->setCustomEventsManager($this,$eventsManager);
			return $this;
		}


		/**
		 * Returns the custom events manager
		 *
		 * @return \ManaPHP\Events\ManagerInterface
		 */
		protected function getEventsManager(){
			return $this->_modelsManager->getCustomEventsManager($this);
		}


		/**
		 * Returns the models meta-data service related to the entity instance
		 *
		 * @return \ManaPHP\Mvc\Model\MetaDataInterface
		 * @throws \ManaPHP\Mvc\Model\Exception
		 */
		public function getModelsMetaData(){
		}


		/**
		 * Returns the models manager related to the entity instance
		 *
		 * @return \ManaPHP\Mvc\Model\ManagerInterface
		 */
		public function getModelsManager(){
			return $this->_modelsManager;
		}


		/**
		 * Sets a transaction related to the Model instance
		 *
		 *<code>
		 *use \ManaPHP\Mvc\Model\Transaction\Manager as TxManager;
		 *use \ManaPHP\Mvc\Model\Transaction\Failed as TxFailed;
		 *
		 *try {
		 *
		 *  $txManager = new TxManager();
		 *
		 *  $transaction = $txManager->get();
		 *
		 *  $robot = new Robots();
		 *  $robot->setTransaction($transaction);
		 *  $robot->name = 'WALL·E';
		 *  $robot->created_at = date('Y-m-d');
		 *  if ($robot->save() == false) {
		 *    $transaction->rollback("Can't save robot");
		 *  }
		 *
		 *  $robotPart = new RobotParts();
		 *  $robotPart->setTransaction($transaction);
		 *  $robotPart->type = 'head';
		 *  if ($robotPart->save() == false) {
		 *    $transaction->rollback("Robot part cannot be saved");
		 *  }
		 *
		 *  $transaction->commit();
		 *
		 *} catch (TxFailed $e) {
		 *  echo 'Failed, reason: ', $e->getMessage();
		 *}
		 *
		 *</code>
		 *
		 * @param \ManaPHP\Mvc\Model\TransactionInterface $transaction
		 * @return \ManaPHP\Mvc\Model
		 */
		public function setTransaction($transaction){ }


		/**
		 * Sets table name which model should be mapped
		 *
		 * @param string $source
		 * @return \ManaPHP\Mvc\Model
		 */
		protected function setSource($source){
		}


		/**
		 * Returns table name mapped in the model
		 *
		 * @return string
		 */
		public function getSource(){ }


		/**
		 * Sets schema name where table mapped is located
		 *
		 * @param string $schema
		 * @return \ManaPHP\Mvc\Model
		 */
		protected function setSchema($schema){ }


		/**
		 * Returns schema name where table mapped is located
		 *
		 * @return string
		 */
		public function getSchema(){ }


		/**
		 * Sets the DependencyInjection connection service name
		 *
		 * @param string $connectionService
		 * @return \ManaPHP\Mvc\ModelInterface
		 */
		public function setConnectionService($connectionService){
			$this->_modelsManager->setConnectionService($this,$connectionService);
			return $this;
		}


		/**
		 * Sets the DependencyInjection connection service name used to read data
		 *
		 * @param string $connectionService
		 * @return \ManaPHP\Mvc\ModelInterface
		 */
		public function setReadConnectionService($connectionService){
			$this->_modelsManager->setReadConnectionService($this,$connectionService);
			return $this;
		}


		/**
		 * Sets the DependencyInjection connection service name used to write data
		 *
		 * @param string $connectionService
		 * @return \ManaPHP\Mvc\ModelInterface
		 */
		public function setWriteConnectionService($connectionService){
			$this->_modelsManager->setWriteConnectionService($this,$connectionService);
			return $this;
		}


		/**
		 * Returns the DependencyInjection connection service name used to read data related the model
		 *
		 * @return string
		 */
		public function getReadConnectionService(){
			return $this->_modelsManager->getReadConnectionService($this);
		}


		/**
		 * Returns the DependencyInjection connection service name used to write data related to the model
		 *
		 * @return string
		 */
		public function getWriteConnectionService(){
			return $this->_modelsManager->getWriteConnectionService($this);
		}


		/**
		 * Sets the dirty state of the object using one of the DIRTY_STATE_* constants
		 *
		 * @param int $dirtyState
		 * @return \ManaPHP\Mvc\ModelInterface
		 */
		public function setDirtyState($dirtyState){
			$this->_dirtyState =$dirtyState;
			return $this;
		}


		/**
		 * Returns one of the DIRTY_STATE_* constants telling if the record exists in the database or not
		 *
		 * @return int
		 */
		public function getDirtyState(){
			return $this->_dirtyState;
		}


		/**
		 * Gets the connection used to read data for the model
		 *
		 * @return \ManaPHP\Db\AdapterInterface
		 */
		public function getReadConnection(){
			return $this->_modelsManager->getReadConnection($this);
		}


		/**
		 * Gets the connection used to write data to the model
		 *
		 * @return \ManaPHP\Db\AdapterInterface
		 */
		public function getWriteConnection(){
			return $this->_modelsManager->getWriteConnection($this);
		}


		/**
		 * Assigns values to a model from an array
		 *
		 *<code>
		 *$robot->assign(array(
		 *  'type' => 'mechanical',
		 *  'name' => 'Astro Boy',
		 *  'year' => 1952
		 *));
		 *</code>
		 *
		 * @param \ManaPHP\Mvc\Model $object
		 * @param array $data
		 * @param array $columnMap
		 * @param array $whiteList
		 * @return \ManaPHP\Mvc\Model
		 */
		public function assign($data, $columnMap=null,$whiteList=null){
			if(is_array($columnMap)){
				$data_mapped=[];
				foreach($data as $k=>$v){
					if(isset($columnMap[$k])){
						$data_mapped[$columnMap[$k]]=$v;
					}
				}
			}else{
				$data_mapped=$data;
			}

			if(count($data_mapped) ===0){
				return $this;
			}

			$metaData =$this->getModelsMetaData();
			foreach($metaData->getAttributes($this) as $attribute){
				$attributeField=$attribute;

				if(isset($data_mapped[$attributeField])){
					if(is_array($whiteList)){
						if(!in_array($attributeField,$whiteList,true)){
							continue;
						}
					}
					$this->$attributeField=$data_mapped[$attributeField];

				}
			}

			return $this;
		}


		/**
		 * Assigns values to a model from an array returning a new model.
		 *
		 *<code>
		 *$robot = \ManaPHP\Mvc\Model::cloneResultMap(new Robots(), array(
		 *  'type' => 'mechanical',
		 *  'name' => 'Astro Boy',
		 *  'year' => 1952
		 *));
		 *</code>
		 *
		 * @param \ManaPHP\Mvc\Model $base
		 * @param array $data
		 * @param array $columnMap
		 * @param int $dirtyState
		 * @param boolean $keepSnapshots
		 * @return \ManaPHP\Mvc\Model
		 * @throws \ManaPHP\Mvc\Model\Exception
		 */
		public static function cloneResultMap($base, $data, $columnMap, $dirtyState=null, $keepSnapshots=null){
			$instance =clone $base;

			$instance->setDirtyState($dirtyState);

			foreach($data as $k=>$v){
				if(is_string($k)){
					if(!is_array($columnMap)){
						$instance->$k=$v;
						continue;
					}

					if(!isset($columnMap[$k])){
						throw new Exception("Column '" . $k . "' doesn't make part of the column map");
					}

					$instance->{$columnMap[$k]}=$v;
				}
			}

			/**
			 * Models that keep snapshots store the original data
			 */
			if($keepSnapshots){
				$instance->setSnapshotData($data,$columnMap);
			}

			/**
			 * Call afterFetch, this allows the developer to execute actions after a record is fetched from the database
			 */
			if(method_exists($instance, 'afterFetch')) {
				$instance->afterFetch();
			}

			return $instance;
		}


		/**
		 * Returns an hydrated result based on the data and the column map
		 *
		 * @param array $data
		 * @param array $columnMap
		 * @param int $hydrationMode
		 * @return mixed
		 * @throws \ManaPHP\Mvc\Model\Exception
		 */
		public static function cloneResultMapHydrate($data, $columnMap, $hydrationMode){
			if(!is_array($columnMap)){
				if($hydrationMode ===Resultset::HYDRATE_ARRAYS){
					return $data;
				}
			}

			$hydrate=new \stdClass();

			foreach($data as $k=>$v){
				if(is_string($k)){
					if(is_array($columnMap)){
						if(!isset($columnMap[$k])){
							throw new Exception("Column '" . $k . "' doesn't make part of the column map")
						}

						$hydrate->{$columnMap[$k]}=$v;
					}
				}
			}

			if($hydrationMode ===Resultset::HYDRATE_ARRAYS){
				return (array)$hydrate;
			}else{
				return $hydrate;
			}
		}


		/**
		 * Assigns values to a model from an array returning a new model
		 *
		 *<code>
		 *$robot = \ManaPHP\Mvc\Model::cloneResult(new Robots(), array(
		 *  'type' => 'mechanical',
		 *  'name' => 'Astro Boy',
		 *  'year' => 1952
		 *));
		 *</code>
		 *
		 * @param \ManaPHP\Mvc\Model $base
		 * @param array $data
		 * @param int $dirtyState
		 * @return \ManaPHP\Mvc\ModelInterface
		 * @throws \ManaPHP\Mvc\Model\Exception
		 */
		public static function cloneResult($base, $data, $dirtyState=null){
			$instance =clone $base;

			$instance->setDirtyState($dirtyState);

			foreach($data as $k=>$v){
				if(!is_string($k)){
					throw new Exception('Invalid key in array data provided to dumpResult()');
				}
				$instance->{$k} =$v;
			}

			/**
			 * Call afterFetch, this allows the developer to execute actions after a record is fetched from the database
			 */
			if(method_exists($instance,'afterFetch')){
				$instance->afterFetch();
			}

			return $instance;
		}


		/**
		 * Allows to query a set of records that match the specified conditions
		 *
		 * <code>
		 *
		 * //How many robots are there?
		 * $robots = Robots::find();
		 * echo "There are ", count($robots), "\n";
		 *
		 * //How many mechanical robots are there?
		 * $robots = Robots::find("type='mechanical'");
		 * echo "There are ", count($robots), "\n";
		 *
		 * //Get and print virtual robots ordered by name
		 * $robots = Robots::find(array("type='virtual'", "order" => "name"));
		 * foreach ($robots as $robot) {
		 *	   echo $robot->name, "\n";
		 * }
		 *
		 * //Get first 100 virtual robots ordered by name
		 * $robots = Robots::find(array("type='virtual'", "order" => "name", "limit" => 100));
		 * foreach ($robots as $robot) {
		 *	   echo $robot->name, "\n";
		 * }
		 * </code>
		 *
		 * @param 	array $parameters
		 * @return  \ManaPHP\Mvc\Model\ResultsetInterface
		 */
		public static function find($parameters=null){
			/**
			 * @var \ManaPHP\Mvc\Model\ManagerInterface $modelsManager
			 */
			$dependencyInjector=Di::getDefault();
			$modelsManager =$dependencyInjector->getShared('modelsManager');

			if(is_array($parameters)){
				$params =$parameters;
			}elseif(is_null($parameters)){
				$params=[];
			}else{
				$params=[];
				$params[]=$parameters;
			}

			$builder =$modelsManager->createBuilder($params);
			$builder->from(get_called_class());
			$query =$builder->getQuery();

			if(isset($params['bind'])){
				if(is_array($params['bind'])){
					$query->
				}
			}
		}


		/**
		 * Allows to query the first record that match the specified conditions
		 *
		 * <code>
		 *
		 * //What's the first robot in robots table?
		 * $robot = Robots::findFirst();
		 * echo "The robot name is ", $robot->name;
		 *
		 * //What's the first mechanical robot in robots table?
		 * $robot = Robots::findFirst("type='mechanical'");
		 * echo "The first mechanical robot name is ", $robot->name;
		 *
		 * //Get first virtual robot ordered by name
		 * $robot = Robots::findFirst(array("type='virtual'", "order" => "name"));
		 * echo "The first virtual robot name is ", $robot->name;
		 *
		 * </code>
		 *
		 * @param array $parameters
		 * @return \ManaPHP\Mvc\Model
		 */
		public static function findFirst($parameters=null){ }


		/**
		 * Create a criteria for a specific model
		 *
		 * @param \ManaPHP\DiInterface $dependencyInjector
		 * @return \ManaPHP\Mvc\Model\Criteria
		 */
		public static function query($dependencyInjector=null){ }


		/**
		 * Checks if the current record already exists or not
		 *
		 * @param \ManaPHP\Mvc\Model\MetadataInterface $metaData
		 * @param \ManaPHP\Db\AdapterInterface $connection
		 * @return boolean
		 */
		protected function _exists(){ }


		/**
		 * Generate a PHQL SELECT statement for an aggregate
		 *
		 * @param string $function
		 * @param string $alias
		 * @param array $parameters
		 * @return \ManaPHP\Mvc\Model\ResultsetInterface
		 */
		protected static function _groupResult(){ }


		/**
		 * Allows to count how many records match the specified conditions
		 *
		 * <code>
		 *
		 * //How many robots are there?
		 * $number = Robots::count();
		 * echo "There are ", $number, "\n";
		 *
		 * //How many mechanical robots are there?
		 * $number = Robots::count("type='mechanical'");
		 * echo "There are ", $number, " mechanical robots\n";
		 *
		 * </code>
		 *
		 * @param array $parameters
		 * @return int
		 */
		public static function count($parameters=null){ }


		/**
		 * Allows to calculate a summatory on a column that match the specified conditions
		 *
		 * <code>
		 *
		 * //How much are all robots?
		 * $sum = Robots::sum(array('column' => 'price'));
		 * echo "The total price of robots is ", $sum, "\n";
		 *
		 * //How much are mechanical robots?
		 * $sum = Robots::sum(array("type='mechanical'", 'column' => 'price'));
		 * echo "The total price of mechanical robots is  ", $sum, "\n";
		 *
		 * </code>
		 *
		 * @param array $parameters
		 * @return double
		 */
		public static function sum($parameters=null){ }


		/**
		 * Allows to get the maximum value of a column that match the specified conditions
		 *
		 * <code>
		 *
		 * //What is the maximum robot id?
		 * $id = Robots::maximum(array('column' => 'id'));
		 * echo "The maximum robot id is: ", $id, "\n";
		 *
		 * //What is the maximum id of mechanical robots?
		 * $sum = Robots::maximum(array("type='mechanical'", 'column' => 'id'));
		 * echo "The maximum robot id of mechanical robots is ", $id, "\n";
		 *
		 * </code>
		 *
		 * @param array $parameters
		 * @return mixed
		 */
		public static function maximum($parameters=null){ }


		/**
		 * Allows to get the minimum value of a column that match the specified conditions
		 *
		 * <code>
		 *
		 * //What is the minimum robot id?
		 * $id = Robots::minimum(array('column' => 'id'));
		 * echo "The minimum robot id is: ", $id;
		 *
		 * //What is the minimum id of mechanical robots?
		 * $sum = Robots::minimum(array("type='mechanical'", 'column' => 'id'));
		 * echo "The minimum robot id of mechanical robots is ", $id;
		 *
		 * </code>
		 *
		 * @param array $parameters
		 * @return mixed
		 */
		public static function minimum($parameters=null){ }


		/**
		 * Allows to calculate the average value on a column matching the specified conditions
		 *
		 * <code>
		 *
		 * //What's the average price of robots?
		 * $average = Robots::average(array('column' => 'price'));
		 * echo "The average price is ", $average, "\n";
		 *
		 * //What's the average price of mechanical robots?
		 * $average = Robots::average(array("type='mechanical'", 'column' => 'price'));
		 * echo "The average price of mechanical robots is ", $average, "\n";
		 *
		 * </code>
		 *
		 * @param array $parameters
		 * @return double
		 */
		public static function average($parameters=null){ }


		/**
		 * Fires an event, implicitly calls behaviors and listeners in the events manager are notified
		 *
		 * @param string $eventName
		 * @return boolean
		 */
		public function fireEvent($eventName){ }


		/**
		 * Fires an event, implicitly calls behaviors and listeners in the events manager are notified
		 * This method stops if one of the callbacks/listeners returns boolean false
		 *
		 * @param string $eventName
		 * @return boolean
		 */
		public function fireEventCancel($eventName){ }


		/**
		 * Cancel the current operation
		 *
		 * @return boolean
		 */
		protected function _cancelOperation(){ }


		/**
		 * Appends a customized message on the validation process
		 *
		 * <code>
		 * use \ManaPHP\Mvc\Model\Message as Message;
		 *
		 * class Robots extends \ManaPHP\Mvc\Model
		 * {
		 *
		 *   public function beforeSave()
		 *   {
		 *     if ($this->name == 'Peter') {
		 *        $message = new Message("Sorry, but a robot cannot be named Peter");
		 *        $this->appendMessage($message);
		 *     }
		 *   }
		 * }
		 * </code>
		 *
		 * @param \ManaPHP\Mvc\Model\MessageInterface $message
		 * @return \ManaPHP\Mvc\Model
		 */
		public function appendMessage($message){ }


		/**
		 * Executes validators on every validation call
		 *
		 *<code>
		 *use \ManaPHP\Mvc\Model\Validator\ExclusionIn as ExclusionIn;
		 *
		 *class Subscriptors extends \ManaPHP\Mvc\Model
		 *{
		 *
		 *	public function validation()
		 *  {
		 * 		$this->validate(new ExclusionIn(array(
		 *			'field' => 'status',
		 *			'domain' => array('A', 'I')
		 *		)));
		 *		if ($this->validationHasFailed() == true) {
		 *			return false;
		 *		}
		 *	}
		 *
		 *}
		 *</code>
		 *
		 * @param \ManaPHP\Mvc\Model\ValidatorInterface $validator
		 * @return \ManaPHP\Mvc\Model
		 */
		protected function validate($validator){ }


		/**
		 * Check whether validation process has generated any messages
		 *
		 *<code>
		 *use \ManaPHP\Mvc\Model\Validator\ExclusionIn as ExclusionIn;
		 *
		 *class Subscriptors extends \ManaPHP\Mvc\Model
		 *{
		 *
		 *	public function validation()
		 *  {
		 * 		$this->validate(new ExclusionIn(array(
		 *			'field' => 'status',
		 *			'domain' => array('A', 'I')
		 *		)));
		 *		if ($this->validationHasFailed() == true) {
		 *			return false;
		 *		}
		 *	}
		 *
		 *}
		 *</code>
		 *
		 * @return boolean
		 */
		public function validationHasFailed(){ }


		/**
		 * Returns all the validation messages
		 *
		 *<code>
		 *	$robot = new Robots();
		 *	$robot->type = 'mechanical';
		 *	$robot->name = 'Astro Boy';
		 *	$robot->year = 1952;
		 *	if ($robot->save() == false) {
		 *  	echo "Umh, We can't store robots right now ";
		 *  	foreach ($robot->getMessages() as $message) {
		 *			echo $message;
		 *		}
		 *	} else {
		 *  	echo "Great, a new robot was saved successfully!";
		 *	}
		 * </code>
		 *
		 * @return \ManaPHP\Mvc\Model\MessageInterface[]
		 */
		public function getMessages($filter=null){ }


		/**
		 * Reads "belongs to" relations and check the virtual foreign keys when inserting or updating records
		 * to verify that inserted/updated values are present in the related entity
		 *
		 * @return boolean
		 */
		protected function _checkForeignKeysRestrict(){ }


		/**
		 * Reads both "hasMany" and "hasOne" relations and checks the virtual foreign keys (restrict) when deleting records
		 *
		 * @return boolean
		 */
		protected function _checkForeignKeysReverseRestrict(){ }


		/**
		 * Reads both "hasMany" and "hasOne" relations and checks the virtual foreign keys (cascade) when deleting records
		 *
		 * @return boolean
		 */
		protected function _checkForeignKeysReverseCascade(){ }


		/**
		 * Executes internal hooks before save a record
		 *
		 * @param \ManaPHP\Mvc\Model\MetadataInterface $metaData
		 * @param boolean $exists
		 * @param string $identityField
		 * @return boolean
		 */
		protected function _preSave(){ }


		/**
		 * Executes internal events after save a record
		 *
		 * @param boolean $success
		 * @param boolean $exists
		 * @return boolean
		 */
		protected function _postSave(){ }


		/**
		 * Sends a pre-build INSERT SQL statement to the relational database system
		 *
		 * @param \ManaPHP\Mvc\Model\MetadataInterface $metaData
		 * @param \ManaPHP\Db\AdapterInterface $connection
		 * @param string $table
		 * @return boolean
		 */
		protected function _doLowInsert(){ }


		/**
		 * Sends a pre-build UPDATE SQL statement to the relational database system
		 *
		 * @param \ManaPHP\Mvc\Model\MetadataInterface $metaData
		 * @param \ManaPHP\Db\AdapterInterface $connection
		 * @param string|array $table
		 * @return boolean
		 */
		protected function _doLowUpdate(){ }


		/**
		 * Saves related records that must be stored prior to save the master record
		 *
		 * @param \ManaPHP\Db\AdapterInterface $connection
		 * @param \ManaPHP\Mvc\ModelInterface[] $related
		 * @return boolean
		 */
		protected function _preSaveRelatedRecords(){ }


		/**
		 * Save the related records assigned in the has-one/has-many relations
		 *
		 * @param \ManaPHP\Db\AdapterInterface $connection
		 * @param \ManaPHP\Mvc\ModelInterface[] $related
		 * @return boolean
		 */
		protected function _postSaveRelatedRecords(){ }


		/**
		 * Inserts or updates a model instance. Returning true on success or false otherwise.
		 *
		 *<code>
		 *	//Creating a new robot
		 *	$robot = new Robots();
		 *	$robot->type = 'mechanical';
		 *	$robot->name = 'Astro Boy';
		 *	$robot->year = 1952;
		 *	$robot->save();
		 *
		 *	//Updating a robot name
		 *	$robot = Robots::findFirst("id=100");
		 *	$robot->name = "Biomass";
		 *	$robot->save();
		 *</code>
		 *
		 * @param array $data
		 * @param array $whiteList
		 * @return boolean
		 */
		public function save($data=null, $whiteList=null){ }


		/**
		 * Inserts a model instance. If the instance already exists in the persistance it will throw an exception
		 * Returning true on success or false otherwise.
		 *
		 *<code>
		 *	//Creating a new robot
		 *	$robot = new Robots();
		 *	$robot->type = 'mechanical';
		 *	$robot->name = 'Astro Boy';
		 *	$robot->year = 1952;
		 *	$robot->create();
		 *
		 *  //Passing an array to create
		 *  $robot = new Robots();
		 *  $robot->create(array(
		 *      'type' => 'mechanical',
		 *      'name' => 'Astroy Boy',
		 *      'year' => 1952
		 *  ));
		 *</code>
		 *
		 * @param array $data
		 * @param array $whiteList
		 * @return boolean
		 */
		public function create($data=null, $whiteList=null){ }


		/**
		 * Updates a model instance. If the instance does n't exist in the persistance it will throw an exception
		 * Returning true on success or false otherwise.
		 *
		 *<code>
		 *	//Updating a robot name
		 *	$robot = Robots::findFirst("id=100");
		 *	$robot->name = "Biomass";
		 *	$robot->update();
		 *</code>
		 *
		 * @param array $data
		 * @param array $whiteList
		 * @return boolean
		 */
		public function update($data=null, $whiteList=null){ }


		/**
		 * Deletes a model instance. Returning true on success or false otherwise.
		 *
		 * <code>
		 *$robot = Robots::findFirst("id=100");
		 *$robot->delete();
		 *
		 *foreach (Robots::find("type = 'mechanical'") as $robot) {
		 *   $robot->delete();
		 *}
		 * </code>
		 *
		 * @return boolean
		 */
		public function delete(){ }


		/**
		 * Returns the type of the latest operation performed by the ORM
		 * Returns one of the OP_* class constants
		 *
		 * @return int
		 */
		public function getOperationMade(){ }


		/**
		 * Refreshes the model attributes re-querying the record from the database
		 */
		public function refresh(){ }


		/**
		 * Skips the current operation forcing a success state
		 *
		 * @param boolean $skip
		 */
		public function skipOperation($skip){ }


		/**
		 * Reads an attribute value by its name
		 *
		 * <code>
		 * echo $robot->readAttribute('name');
		 * </code>
		 *
		 * @param string $attribute
		 * @return mixed
		 */
		public function readAttribute($attribute){ }


		/**
		 * Writes an attribute value by its name
		 *
		 * <code>
		 * 	$robot->writeAttribute('name', 'Rosy');
		 * </code>
		 *
		 * @param string $attribute
		 * @param mixed $value
		 */
		public function writeAttribute($attribute, $value){ }


		/**
		 * Sets a list of attributes that must be skipped from the
		 * generated INSERT/UPDATE statement
		 *
		 *<code>
		 *
		 *class Robots extends \ManaPHP\Mvc\Model
		 *{
		 *
		 *   public function initialize()
		 *   {
		 *       $this->skipAttributes(array('price'));
		 *   }
		 *
		 *}
		 *</code>
		 *
		 * @param array $attributes
		 * @param boolean $replace
		 */
		protected function skipAttributes($attributes, $replace=null){ }


		/**
		 * Sets a list of attributes that must be skipped from the
		 * generated INSERT statement
		 *
		 *<code>
		 *
		 *class Robots extends \ManaPHP\Mvc\Model
		 *{
		 *
		 *   public function initialize()
		 *   {
		 *       $this->skipAttributesOnCreate(array('created_at'));
		 *   }
		 *
		 *}
		 *</code>
		 *
		 * @param array $attributes
		 * @param boolean $replace
		 */
		protected function skipAttributesOnCreate($attributes, $replace=null){ }


		/**
		 * Sets a list of attributes that must be skipped from the
		 * generated UPDATE statement
		 *
		 *<code>
		 *
		 *class Robots extends \ManaPHP\Mvc\Model
		 *{
		 *
		 *   public function initialize()
		 *   {
		 *       $this->skipAttributesOnUpdate(array('modified_in'));
		 *   }
		 *
		 *}
		 *</code>
		 *
		 * @param array $attributes
		 * @param boolean $replace
		 */
		protected function skipAttributesOnUpdate($attributes, $replace=null){ }


		/**
		 * Setup a 1-1 relation between two models
		 *
		 *<code>
		 *
		 *class Robots extends \ManaPHP\Mvc\Model
		 *{
		 *
		 *   public function initialize()
		 *   {
		 *       $this->hasOne('id', 'RobotsDescription', 'robots_id');
		 *   }
		 *
		 *}
		 *</code>
		 *
		 * @param mixed $fields
		 * @param string $referenceModel
		 * @param mixed $referencedFields
		 * @param   array $options
		 * @return  \ManaPHP\Mvc\Model\Relation
		 */
		public function hasOne($fields, $referenceModel, $referencedFields, $options=null){ }


		/**
		 * Setup a relation reverse 1-1  between two models
		 *
		 *<code>
		 *
		 *class RobotsParts extends \ManaPHP\Mvc\Model
		 *{
		 *
		 *   public function initialize()
		 *   {
		 *       $this->belongsTo('robots_id', 'Robots', 'id');
		 *   }
		 *
		 *}
		 *</code>
		 *
		 * @param mixed $fields
		 * @param string $referenceModel
		 * @param mixed $referencedFields
		 * @param   array $options
		 * @return  \ManaPHP\Mvc\Model\Relation
		 */
		public function belongsTo($fields, $referenceModel, $referencedFields, $options=null){ }


		/**
		 * Setup a relation 1-n between two models
		 *
		 *<code>
		 *
		 *class Robots extends \ManaPHP\Mvc\Model
		 *{
		 *
		 *   public function initialize()
		 *   {
		 *       $this->hasMany('id', 'RobotsParts', 'robots_id');
		 *   }
		 *
		 *}
		 *</code>
		 *
		 * @param mixed $fields
		 * @param string $referenceModel
		 * @param mixed $referencedFields
		 * @param   array $options
		 * @return  \ManaPHP\Mvc\Model\Relation
		 */
		public function hasMany($fields, $referenceModel, $referencedFields, $options=null){ }


		/**
		 * Setup a relation n-n between two models through an intermediate relation
		 *
		 *<code>
		 *
		 *class Robots extends \ManaPHP\Mvc\Model
		 *{
		 *
		 *   public function initialize()
		 *   {
		 *       //Setup a many-to-many relation to Parts through RobotsParts
		 *       $this->hasManyToMany(
		 *			'id',
		 *			'RobotsParts',
		 *			'robots_id',
		 *			'parts_id',
		 *			'Parts',
		 *			'id'
		 *		);
		 *   }
		 *
		 *}
		 *</code>
		 *
		 * @param string $fields
		 * @param string $intermediateModel
		 * @param string $intermediateFields
		 * @param string $intermediateReferencedFields
		 * @param string $referencedModel
		 * @param   string $referencedFields
		 * @param   array $options
		 * @return  \ManaPHP\Mvc\Model\Relation
		 */
		public function hasManyToMany($fields, $intermediateModel, $intermediateFields, $intermediateReferencedFields, $referenceModel, $referencedFields, $options=null){ }


		/**
		 * Setups a behavior in a model
		 *
		 *<code>
		 *
		 *use \ManaPHP\Mvc\Model\Behavior\Timestampable;
		 *
		 *class Robots extends \ManaPHP\Mvc\Model
		 *{
		 *
		 *   public function initialize()
		 *   {
		 *		$this->addBehavior(new Timestampable(array(
		 *			'onCreate' => array(
		 *				'field' => 'created_at',
		 *				'format' => 'Y-m-d'
		 *			)
		 *		)));
		 *   }
		 *
		 *}
		 *</code>
		 *
		 * @param \ManaPHP\Mvc\Model\BehaviorInterface $behavior
		 */
		public function addBehavior($behavior){ }


		/**
		 * Sets if the model must keep the original record snapshot in memory
		 *
		 *<code>
		 *
		 *class Robots extends \ManaPHP\Mvc\Model
		 *{
		 *
		 *   public function initialize()
		 *   {
		 *		$this->keepSnapshots(true);
		 *   }
		 *
		 *}
		 *</code>
		 *
		 * @param boolean $keepSnapshots
		 */
		protected function keepSnapshots($keepSnapshots){ }


		/**
		 * Sets the record's snapshot data.
		 * This method is used internally to set snapshot data when the model was set up to keep snapshot data
		 *
		 * @param array $data
		 * @param array $columnMap
		 */
		public function setSnapshotData($data, $columnMap=null){ }


		/**
		 * Checks if the object has internal snapshot data
		 *
		 * @return boolean
		 */
		public function hasSnapshotData(){ }


		/**
		 * Returns the internal snapshot data
		 *
		 * @return array
		 */
		public function getSnapshotData(){ }


		/**
		 * Check if a specific attribute has changed
		 * This only works if the model is keeping data snapshots
		 *
		 * @param boolean $fieldName
		 */
		public function hasChanged($fieldName=null){ }


		/**
		 * Returns a list of changed values
		 *
		 * @return array
		 */
		public function getChangedFields(){ }


		/**
		 * Sets if a model must use dynamic update instead of the all-field update
		 *
		 *<code>
		 *
		 *class Robots extends \ManaPHP\Mvc\Model
		 *{
		 *
		 *   public function initialize()
		 *   {
		 *		$this->useDynamicUpdate(true);
		 *   }
		 *
		 *}
		 *</code>
		 *
		 * @param boolean $dynamicUpdate
		 */
		protected function useDynamicUpdate($dynamicUpdate){ }


		/**
		 * Returns related records based on defined relations
		 *
		 * @param string $alias
		 * @param array $arguments
		 * @return \ManaPHP\Mvc\Model\ResultsetInterface
		 */
		public function getRelated($alias, $arguments=null){ }


		/**
		 * Returns related records defined relations depending on the method name
		 *
		 * @param string $modelName
		 * @param string $method
		 * @param array $arguments
		 * @return mixed
		 */
		protected function _getRelatedRecords(){ }


		/**
		 * Handles method calls when a method is not implemented
		 *
		 * @param string $method
		 * @param array $arguments
		 * @return mixed
		 */
		public function __call($method, $arguments=null){ }


		/**
		 * Handles method calls when a static method is not implemented
		 *
		 * @param string $method
		 * @param array $arguments
		 * @return mixed
		 */
		public static function __callStatic($method, $arguments=null){ }


		/**
		 * Magic method to assign values to the the model
		 *
		 * @param string $property
		 * @param mixed $value
		 */
		public function __set($property, $value){ }


		/**
		 * Magic method to get related records using the relation alias as a property
		 *
		 * @param string $property
		 * @return \ManaPHP\Mvc\Model\Resultset
		 */
		public function __get($property){ }


		/**
		 * Magic method to check if a property is a valid relation
		 *
		 * @param string $property
		 */
		public function __isset($property){ }


		/**
		 * Serializes the object ignoring connections, services, related objects or static properties
		 *
		 * @return string
		 */
		public function serialize(){ }


		/**
		 * Unserializes the object from a serialized string
		 *
		 * @param string $data
		 */
		public function unserialize($data){ }


		/**
		 * Returns a simple representation of the object that can be used with var_dump
		 *
		 *<code>
		 * var_dump($robot->dump());
		 *</code>
		 *
		 * @return array
		 */
		public function dump(){ }


		/**
		 * Returns the instance as an array representation
		 *
		 *<code>
		 * print_r($robot->toArray());
		 *</code>
		 *
		 * @param array $columns
		 * @return array
		 */
		public function toArray($columns=null){ }


		/**
		 * Enables/disables options in the ORM
		 * Available options:
		 * events                — Enables/Disables globally the internal events
		 * virtualForeignKeys    — Enables/Disables virtual foreign keys
		 * columnRenaming        — Enables/Disables column renaming
		 * notNullValidations    — Enables/Disables automatic not null validation
		 * exceptionOnFailedSave — Enables/Disables throws an exception if the saving process fails
		 * phqlLiterals          — Enables/Disables literals in PHQL this improves the security of applications  
		 *
		 * @param array $options
		 */
		public static function setup($options){ }


		/**
		 * Reset the model data
		 *
		 * <code>
		 * $robot = Robots::findFirst();
		 * $robot->reset();
		 * </code>
		 */
		public function reset(){ }

	}
}
