<?php 

namespace ManaPHP\Mvc\Model {
	use \ManaPHP\Di\InjectionAwareInterface;
	/**
	 * ManaPHP\Mvc\Model\MetaData
	 *
	 * <p>Because ManaPHP\Mvc\Model requires meta-data like field names, data types, primary keys, etc.
	 * this component collect them and store for further querying by ManaPHP\Mvc\Model.
	 * ManaPHP\Mvc\Model\MetaData can also use adapters to store temporarily or permanently the meta-data.</p>
	 *
	 * <p>A standard ManaPHP\Mvc\Model\MetaData can be used to query model attributes:</p>
	 *
	 * <code>
	 *	$metaData = new ManaPHP\Mvc\Model\MetaData\Memory();
	 *	$attributes = $metaData->getAttributes(new Robots());
	 *	print_r($attributes);
	 * </code>
	 *
	 */
	
	abstract class MetaData implements InjectionAwareInterface, MetaDataInterface {

		const MODELS_ATTRIBUTES = 0;

		const MODELS_PRIMARY_KEY = 1;

		const MODELS_NON_PRIMARY_KEY = 2;

		const MODELS_NOT_NULL = 3;

		const MODELS_DATA_TYPES = 4;

		const MODELS_DATA_TYPES_NUMERIC = 5;

		const MODELS_DATE_AT = 6;

		const MODELS_DATE_IN = 7;

		const MODELS_IDENTITY_COLUMN = 8;

		const MODELS_DATA_TYPES_BIND = 9;

		const MODELS_AUTOMATIC_DEFAULT_INSERT = 10;

		const MODELS_AUTOMATIC_DEFAULT_UPDATE = 11;

		const MODELS_COLUMN_MAP = 0;

		const MODELS_REVERSE_COLUMN_MAP = 1;

		protected $_dependencyInjector;

		protected $_strategy;

		protected $_metaData;

		protected $_columnMap;


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
		 * @param \ManaPHP\Mvc\ModelInterface $model
		 * @return array
		 * @throws \ManaPHP\Mvc\Model\Exception
		 */
		protected function _getMetaData($model){
			$schema =$model->getSchema();
			$table =$model->getSource();

			if(!$model->getReadConnection()->tableExists($table,$schema)){
				if($schema !==''){
					$complete_table =$schema."'.'".$table;
				}else{
					$complete_table =$table;
				}
				throw new Exception("Table '" . $complete_table . "' doesn't exist in database when dumping meta-data for " . get_called_class(model));
			}



		}


		/**
		 * Reads the complete meta-data for certain model
		 *
		 *<code>
		 *	print_r($metaData->readMetaData(new Robots()));
		 *</code>
		 *
		 * @param \ManaPHP\Mvc\ModelInterface $model
		 * @return array
		 */
		public function readMetaData($model){
			$source =$model->getSource();
			$schema =$model->getSchema();

			$key=strtolower(get_called_class()).'-'.$schema.$source;
			if(!isset($this->_metaData[$key])){
				$this->_metaData[$key]=$this->_getMetaData($model);
			}

			return $this->_metaData[$key];
		}

		/**
		 * Returns table attributes names (fields)
		 *
		 *<code>
		 *	print_r($metaData->getAttributes(new Robots()));
		 *</code>
		 *
		 * @param \ManaPHP\Mvc\ModelInterface $model
		 * @return array
		 * @throws \ManaPHP\Mvc\Model\Exception
		 */
		public function getAttributes($model){
			$data =$this->readMetaData($model)[self::MODELS_ATTRIBUTES];
			if(!is_array($data)){
				throw new Exception('The meta-data is invalid or is corrupt');
			}
			return $data;
		}


		/**
		 * Returns an array of fields which are part of the primary key
		 *
		 *<code>
		 *	print_r($metaData->getPrimaryKeyAttributes(new Robots()));
		 *</code>
		 *
		 * @param \ManaPHP\Mvc\ModelInterface $model
		 * @return array
		 */
		public function getPrimaryKeyAttributes($model){
			return $this->readMetaData($model)[self::MODELS_NON_PRIMARY_KEY];
		}


		/**
		 * Returns attributes and their data types
		 *
		 *<code>
		 *	print_r($metaData->getDataTypes(new Robots()));
		 *</code>
		 *
		 * @param \ManaPHP\Mvc\ModelInterface $model
		 * @return array
		 * @throws \ManaPHP\Mvc\Model\Exception
		 */
		public function getDataTypes($model){
			return $this->readMetaData($model)[self::MODELS_DATA_TYPES];
		}


		/**
		 * Returns attributes and their bind data types
		 *
		 *<code>
		 *	print_r($metaData->getBindTypes(new Robots()));
		 *</code>
		 *
		 * @param \ManaPHP\Mvc\ModelInterface $model
		 * @return array
		 * @throws \ManaPHP\Mvc\Model\Exception
		 */
		public function getBindTypes($model){
			return $this->readMetaData($model)[self::MODELS_DATA_TYPES_BIND];
		}


		/**
		 * Check if a model has certain attribute
		 *
		 *<code>
		 *	var_dump($metaData->hasAttribute(new Robots(), 'name'));
		 *</code>
		 *
		 * @param \ManaPHP\Mvc\ModelInterface $model
		 * @param string $attribute
		 * @return boolean
		 */
		public function hasAttribute($model, $attribute){
			return isset($this->readMetaData($model)[self::MODELS_DATA_TYPES][$attribute]);
		}
	}
}
