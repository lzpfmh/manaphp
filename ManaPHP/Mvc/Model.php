<?php 

namespace ManaPHP\Mvc {

	use ManaPHP\Di;
	use ManaPHP\DiInterface;
	use ManaPHP\Mvc\Model\Criteria;
	use ManaPHP\Mvc\Model\Exception;
	use ManaPHP\Mvc\Model\Message;
	use ManaPHP\Mvc\Model\MetaDataInterface;
	use \ManaPHP\Mvc\Model\ResultInterface;
	use \ManaPHP\Di\InjectionAwareInterface;
	use ManaPHP\Mvc\Model\Resultset;
	use ManaPHP\Mvc\Model\ValidationFailed;

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

		/**
		 * @var \ManaPHP\Mvc\Model\MessageInterface[]
		 */
		protected $_errorMessages;

		protected $_operationMade;

		protected $_dirtyState;

		protected $_uniqueKey;

		protected $_uniqueParams;

		protected $_uniqueTypes;

		protected $_skipped;

		protected $_related;

		/**
		 * @var array
		 */
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
			if(!is_object($this->_modelsMetaData)){
				$this->_modelsMetaData =$this->_dependencyInjector->getShared('modelsMetadata');
			}

			return $this->_modelsMetaData;
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
		 * Sets table name which model should be mapped
		 *
		 * @param string $source
		 * @return \ManaPHP\Mvc\Model
		 */
		protected function setSource($source){
			$this->_modelsManager->setModelSource($this,$source);
		}


		/**
		 * Returns table name mapped in the model
		 *
		 * @return string
		 */
		public function getSource(){
			return $this->_modelsManager->getModelSource($this);
		}


		/**
		 * Sets schema name where table mapped is located
		 *
		 * @param string $schema
		 * @return \ManaPHP\Mvc\Model
		 */
		protected function setSchema($schema){
			$this->_modelsManager->setModelSchema($this,$schema);
		}


		/**
		 * Returns schema name where table mapped is located
		 *
		 * @return string
		 */
		public function getSchema(){
			return $this->_modelsManager->getModelSchema($this);
		}


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
		 * @param array $data
		 * @param array $columnMap
		 * @param array $whiteList
		 * @return \ManaPHP\Mvc\Model
		 * @throws \ManaPHP\Mvc\Model\Exception
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
			foreach($metaData->getAttributes($this) as $attributeField){
				if(isset($data_mapped[$attributeField])){
					if(is_array($whiteList) &&!in_array($attributeField,$whiteList,true)){
						continue;
					}
					$this->{$attributeField}=$data_mapped[$attributeField];
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
		public static function cloneResultMap($base, $data, $columnMap=null, $dirtyState=0, $keepSnapshots=null){
			$instance =clone $base;

			$instance->setDirtyState($dirtyState);

			foreach($data as $k=>$v){
				if(is_string($k)){
					if(!is_array($columnMap)){
						$instance->{$k}=$v;
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
		public static function cloneResult($base, $data, $dirtyState=0){
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
			}elseif($parameters===null){
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
					$query->setBindParams($params['bind'],true);
				}
			}

			if(isset($params['cache'])){
				$query->cache($params['cache']);
			}

			$resultset=$query->execute();

			if(is_object($resultset)){
				if(isset($params['hydration'])){
					$resultset->setHydrateMode($params['hydration']);
				}
			}

			return $resultset;
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
		public static function findFirst($parameters=null){
			/**
			 * @var \ManaPHP\Mvc\Model\ManagerInterface $modelsManager
			 */
			$dependencyInjector=Di::getDefault();
			$modelsManager =$dependencyInjector->getShared('modelsManager');

			if(is_array($parameters)){
				$params =$parameters;
			}elseif($parameters===null){
				$params=[];
			}else{
				$params=[];
				$params[]=$parameters;
			}

			$builder =$modelsManager->createBuilder($params);
			$builder->from(get_called_class());
			$builder->limit(1);

			$query =$builder->getQuery();

			if(isset($params['bind'])){
				if(is_array($params['bind'])){
					$query->setBindParams($params['bind'],true);
				}
			}

			if(isset($params['cache'])){
				$query->cache($params['cache']);
			}

			$query->setUniqueRow(true);

			return $query->execute();
		}


		/**
		 * Create a criteria for a specific model
		 *
		 * @param \ManaPHP\DiInterface $dependencyInjector
		 * @return \ManaPHP\Mvc\Model\Criteria
		 */
		public static function query($dependencyInjector=null){
			if(!is_object($dependencyInjector)){
				$dependencyInjector=Di::getDefault();
			}

			$criteria=$dependencyInjector->get('ManaPHP\Mvc\Model\Criteria');
			$criteria->setModelName(get_called_class());

			return $criteria;
		}


		/**
		 * Checks if the current record already exists or not
		 *
		 * @param \ManaPHP\Mvc\Model\MetadataInterface $metaData
		 * @param \ManaPHP\Db\AdapterInterface $connection
		 * @param string|array table
		 * @return boolean
		 */
		protected function _exists($metaData,$connection,$table=null){

		}


		/**
		 * Generate a PHQL SELECT statement for an aggregate
		 *
		 * @param string $function
		 * @param string $alias
		 * @param array $parameters
		 * @return \ManaPHP\Mvc\Model\ResultsetInterface
		 */
		protected static function _groupResult($function, $alias, $parameters){
			$dependencyInjector =Di::getDefault();
			/**
 			 * @var \ManaPHP\Mvc\Model\ManagerInterface $modelsManager
			 */
			$modelsManager =$dependencyInjector->getShared('modelsManager');

			if(is_array($parameters)){
				$params =$parameters;
			}elseif($parameters===null){
				$params=[];
			}else{
				$params=[];
				$params[]=$parameters;
			}

			if(!isset($params['column'])){
				$params['column']='*';
			}

			if(isset($params['distinct'])){
				$columns =$function .'(DISTINCT '.$params['distinct'].') AS '.$alias;
			}else{
				if(isset($params['group'])){
					$columns =$params['group'].', '.$function.'('.$params['group'].') AS '  .$alias;
				}else{
					$columns =$function.'('.$params['column'].') AS '.$alias;
				}
			}

			$builder =$modelsManager->createBuilder($params);
			$builder->columns($columns);
			$builder->from(get_called_class());

			$query =$builder->getQuery();

			if(isset($params['cache'])){
				$query->cache($params['cache']);
			}

			if(isset($params['bind'])){
				$resultset =$query->execute([$params['bind']]);
			}else{
				$resultset=$query->execute();
			}

			/**
			 * Return the full resultset if the query is grouped
			 */
			if(isset($params['group'])){
				return $resultset;
			}


			/**
			 * Return only the value in the first result
			 */

			$firstRow=$resultset->getFirst();
			return $firstRow->{$alias};
		}


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
		public static function count($parameters=null){
			$result =self::_groupResult('COUNT','rowcount',$parameters);
			if(is_string($result)){
				return (int)$result;
			}else{
				return $result;
			}
		}


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
		public static function sum($parameters=null){
			return self::_groupResult('SUM','sumatory',$parameters);
		}


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
		public static function maximum($parameters=null){
			return self::_groupResult('MAX','maximum',$parameters);
		}


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
		public static function minimum($parameters=null){
			return self::_groupResult('MIN', 'minimum', $parameters);
		}


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
		public static function average($parameters=null){
			return self::_groupResult('AVG','average',$parameters);
		}


		/**
		 * Fires an event, implicitly calls behaviors and listeners in the events manager are notified
		 *
		 * @param string $eventName
		 * @return boolean
		 */
		public function fireEvent($eventName){
			if(method_exists($this,$eventName)){
				$this->{$eventName}();
			}

			return $this->_modelsManager->notifyEvent($eventName,$this);
		}

		/**
		 * Cancel the current operation
		 *
		 * @return boolean
		 */
		protected function _cancelOperation(){
			if($this->_operationMade ===self::OP_DELETE){
				$this->fireEvent('notDeleted');
			}else{
				$this->fireEvent('notSaved');
			}
		}


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
		public function appendMessage($message){
			$this->_errorMessages[]=$message;
			return $this;
		}


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
		protected function validate($validator){
			if($validator->validate($this) ===false){
				$this->_errorMessages =array_merge($this->_errorMessages,$validator->getMessages());
			}
			return $this;
		}


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
		public function validationHasFailed(){
			if(is_array($this->_errorMessages)){
				return count($this->_errorMessages)>0;
			}else{
				return false;
			}
		}


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
		 * @param string $filter
		 * @return \ManaPHP\Mvc\Model\MessageInterface[]
		 */
		public function getMessages($filter=null){
			if(is_string($filter) && $filter !==''){
				$filtered =[];
				foreach($this->_errorMessages as $message){
					if($message->getField() ===$filter){
						$filtered[] =$message;
					}
				}
				return $filtered;
			}

			return $this->_errorMessages;
		}

		/**
		 * Executes internal hooks before save a record
		 *
		 * @param \ManaPHP\Mvc\Model\MetadataInterface $metaData
		 * @param boolean $exists
		 * @param string $identityField
		 * @return boolean
		 */
		protected function _preSave($metaData,$exists,$identityField){
			return true;
		}


		/**
		 * Executes internal events after save a record
		 *
		 * @param boolean $success
		 * @param boolean $exists
		 * @return boolean
		 */
		protected function _postSave($success,$exists){
			if($success ===true){
				if($exists){
					$this->fireEvent('afterUpdate');
				}else{
					$this->fireEvent('afterCreate');
				}
			}

			return $success;
		}


		/**
		 * Sends a pre-build INSERT SQL statement to the relational database system
		 *
		 * @param \ManaPHP\Mvc\Model\MetadataInterface $metaData
		 * @param \ManaPHP\Db\AdapterInterface $connection
		 * @param string $table
		 * @return boolean
		 */
		protected function _doLowInsert($metaData,$connection,$table){ }


		/**
		 * Sends a pre-build UPDATE SQL statement to the relational database system
		 *
		 * @param \ManaPHP\Mvc\Model\MetadataInterface $metaData
		 * @param \ManaPHP\Db\AdapterInterface $connection
		 * @param string|array $table
		 * @return boolean
		 */
		protected function _doLowUpdate($metaData,$connection,$table){}


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
		 * @throws \ManaPHP\Mvc\Model\Exception
		 */
		public function save($data=null, $whiteList=null){
			if(is_array($data) && count($data) >0){
				$this->assign($data,null,$whiteList);
			}

			$metaData =$this->getModelsMetaData();

			$schema=$this->getSchema();
			$source =$this->getSource();
			if($schema){
				$table=[$schema,$source];
			}else{
				$table =$source;
			}

			$exists =$this->_exists($metaData, $this->getReadConnection(),$table);
			if($exists){
				$this->_operationMade =self::OP_UPDATE;
			}else{
				$this->_operationMade=self::OP_CREATE;
			}

			$this->_errorMessages=[];

			/**
			 * Query the identity field
			 */
			$identityField =$metaData->getIdentityField($this);

			if($this->_preSave($metaData,$exists,$identityField) ===false){
				throw new ValidationFailed($this,$this->_errorMessages);
			}

			if($exists){
				$success =$this->_doLowUpdate($metaData,$this->getWriteConnection(),$table);
			}else{
				$success=$this->_doLowInsert($metaData,$this->getWriteConnection(),$identityField);
			}

			if($success){
				$this->_dirtyState =self::DIRTY_STATE_PERSISTENT;
			}

			return $success;
		}


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
		 * @throws \ManaPHP\Mvc\Model\Exception
		 */
		public function create($data=null, $whiteList=null){
			$metaData =$this->getModelsMetaData();

			if($this->_exists($metaData,$this->getReadConnection())){
				$this->_errorMessages=[
					new Message('Record cannot be created because it already exists', null, 'InvalidCreateAttempt')
				];

				return false;
			}

			return $this->save($data,$whiteList);
		}


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
		 * @throws \ManaPHP\Mvc\Model\Exception
		 */
		public function update($data=null, $whiteList=null){
			if($this->_dirtyState){
				$metaData =$this->getModelsMetaData();
				if(!$this->_exists($metaData, $this->getReadConnection())){
					$this->_errorMessages=[new Message('Record cannot be updated because it does not exist', null, 'InvalidUpdateAttempt')];
					return false;
				}
			}

			return $this->save($data,$whiteList);
		}


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
		 * @throws \ManaPHP\Mvc\Model\Exception
		 */
		public function delete(){
			$metaData =$this->getModelsMetaData();
			$writeConnection=$this->getWriteConnection();
			$this->_operationMade =self::OP_DELETE;
			$this->_errorMessages=[];

			$primaryKeys=$metaData->getPrimaryKeyAttributes($this);
			if(count($primaryKeys) ===0){
				throw new Exception('A primary key must be defined in the model in order to perform the operation');
			}

			$values=[];
			$conditions=[];
			foreach($primaryKeys as $primaryKey){
				$attributeField =$primaryKey;

				/**
				 * If the attribute is currently set in the object add it to the conditions
				 */
				if(!isset($this->{$attributeField})){
					throw new Exception("Cannot delete the record because the primary key attribute: '" . $attributeField . "' wasn't set");
				}

				$values[]=$this->{$attributeField};
				$conditions =$writeConnection->escapeIdentifier($attributeField).' = ?';
			}
			$schema =$this->getSchema();
			$source =$this->getSource();
			if(isset($schema)){
				$table=[$schema,$source];
			}else{
				$table=$source;
			}

			$success =$writeConnection->delete($table,implode(' AND ',$conditions),$values);

			/**
			 * Force perform the record existence checking again
			 */
			$this->_dirtyState =self::DIRTY_STATE_DETACHED;

			return $success;
		}


		/**
		 * Returns the type of the latest operation performed by the ORM
		 * Returns one of the OP_* class constants
		 *
		 * @return int
		 */
		public function getOperationMade(){
			return $this->_operationMade;
		}


		/**
		 * Skips the current operation forcing a success state
		 *
		 * @param boolean $skip
		 */
		public function skipOperation($skip){
			$this->_skipped=$skip;
		}


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
		public function readAttribute($attribute){
			return isset($this->{$attribute})?$this->{$attribute}:null;
		}


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
		public function writeAttribute($attribute, $value){
			$this->{$attribute}=$value;
		}


		/**
		 * Check if a specific attribute has changed
		 * This only works if the model is keeping data snapshots
		 *
		 * @param string $fieldName
		 * @return bool
		 * @throws \ManaPHP\Mvc\Model\Exception
		 */
		public function hasChanged($fieldName=null){
			if(!is_array($this->_snapshot)){
				throw new Exception("The record doesn't have a valid data snapshot");
			}

			if($this->_dirtyState !==self::DIRTY_STATE_PERSISTENT){
				throw new Exception('Change checking cannot be performed because the object has not been persisted or is deleted');
			}

			$metaData=$this->getModelsMetaData();
			$allAttributes=$metaData->getDataTypes($this);

			if(is_string($fieldName)){
				if(!isset($allAttributes[$fieldName])){
					throw new Exception("The field '" . $fieldName . "' is not part of the model");
				}

				if(!isset($this->{$fieldName})){
					throw new Exception("The field '" . $fieldName . "' is not defined on the model");
				}

				if(!isset($this->_snapshot[$fieldName])){
					throw new Exception("The field '" . $fieldName . "' was not found in the snapshot");
				}

				return ($this->{$fieldName} !==$this->_snapshot[$fieldName]);
			}

			foreach($allAttributes as $name){
				if(!isset($this->_snapshot[$fieldName])){
					throw new Exception("The field '" . $name . "' was not found in the snapshot");
				}

				if(!isset($this->{$name})){
					return true;
				}

				if($this->{$fieldName} !==$this->_snapshot[$fieldName]){
					return true;
				}
			}

			return false;
		}


		/**
		 * Returns a list of changed values
		 *
		 * @return array
		 * @throws \ManaPHP\Mvc\Model\Exception
		 */
		public function getChangedFields(){
			if(!is_object($this->_snapshot)){
				throw new Exception("The record doesn't have a valid data snapshot");
			}

			if($this->_dirtyState !==self::DIRTY_STATE_PERSISTENT){
				throw new Exception('Change checking cannot be performed because the object has not been persisted or is deleted');
			}

			$metaData=$this->getModelsMetaData();
			$allAttributes=$metaData->getDataTypes($this);

			$changed=[];

			foreach($allAttributes as $k){
				/**
				 * If some attribute is not present in the snapshot, we assume the record as changed
				 */
				if(!isset($this->_snapshot[$k])){
					$changed[]=$k;
					continue;
				}

				if(!isset($this->{$k})){
					$changed[]=$k;
					continue;
				}

				if($this->_snapshot[$k] !==$this->{$k}){
					$changed[]=$k;
					continue;
				}
			}

			return $changed;
		}

		/**
		 * Sets the record's snapshot data.
		 * This method is used internally to set snapshot data when the model was set up to keep snapshot data
		 *
		 * @param array $data
		 * @param array $columnMap
		 */
		public function setSnapshotData($data, $columnMap=null){

		}

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
		protected function useDynamicUpdate($dynamicUpdate){
			$this->_modelsManager->useDynamicUpdate($this,$dynamicUpdate);
		}


		/**
		 * Returns a simple representation of the object that can be used with var_dump
		 *
		 *<code>
		 * var_dump($robot->dump());
		 *</code>
		 *
		 * @return array
		 */
		public function dump(){
			return get_object_vars($this);
		}


		/**
		 * Returns the instance as an array representation
		 *
		 *<code>
		 * print_r($robot->toArray());
		 *</code>
		 *
		 * @param array $columns
		 * @return array
		 * @throws \ManaPHP\Mvc\Model\Exception
		 */
		public function toArray($columns=null){
			$data =[];
			$metaData =$this->getModelsMetaData();

			foreach($metaData->getAttributes($this) as $attributeField){
				if(is_array($columns) && !in_array($attributeField, $columns,true)){
					continue;
				}

				$data[$attributeField]=isset($this->{$attributeField})?$this->{$attributeField}:null;
			}

			return $data;
		}


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
	}
}
