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
         * @param \ManaPHP\Mvc\ModelInterface $model
         * @return array
         * @throws \ManaPHP\Mvc\Model\Exception
         */
        protected function _fetchMetaDataFromRDBMS($model){
            $schema=$model->getSchema();
            $table=$model->getSource();
            if($schema){
                $completeTable=$schema."''".$table;
            }else{
                $completeTable=$table;
            }
            $readConnection=$model->getReadConnection();
            $columns=$readConnection->fetchAll('DESCRIBE '.$readConnection->escapeIdentifier($table),\PDO::FETCH_NUM);
            if(count($columns) ===0){
                throw new Exception("Cannot obtain table columns for the mapped source '" . $completeTable . "' used in model " . get_class($model));
            }

            $attributes=[];
            $primaryKeys=[];
            $nonPrimaryKeys=[];
            $numericTyped=[];
            $autoIncrementAttribute=null;
            foreach($columns as $column){
                $columnName =$column[0];

                $attributes[]=$columnName;

                if($column[3]==='PRI'){
                    $primaryKeys[]=$columnName;
                }else{
                    $nonPrimaryKeys=$columnName;
                }

                $columnType=$column[1];
                if(strpos($columnType,'int') !==false
                ||strpos($columnType,'decimal') !==false
                ||strpos($columnType,'double') !==false
                ||strpos($columnType,'float') !==false) {
                    $numericTyped[]=$columnName;
                }

                if($column[5] ==='auto_increment'){
                    $autoIncrementAttribute=$columnName;
                }
            }

            return [self::MODELS_ATTRIBUTES=>$attributes,
                    self::MODELS_PRIMARY_KEY=>$primaryKeys,
                    self::MODELS_NON_PRIMARY_KEY=>$nonPrimaryKeys,
                    self::MODELS_DATA_TYPES_NUMERIC=>$numericTyped,
                    self::MODELS_IDENTITY_COLUMN=>$autoIncrementAttribute];
        }
        /**
         * @param \ManaPHP\Mvc\ModelInterface $model
         * @return array
         */
        protected function _initialize($model){
            $className =get_class($model);
            if(!isset($this->_metaData[$className])){
                $prefixKey='meta-'.$className;
                $data =$this->read($prefixKey);
                if($data !==null){
                    return $data;
                }else{
                    $data =$this->_fetchMetaDataFromRDBMS($model);
                    $this->write($prefixKey,$data);
                    return $data;
                }
            }
        }

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
		 * Reads the complete meta-data for certain model
		 *
		 *<code>
		 *	print_r($metaData->readMetaData(new Robots()));
		 *</code>
		 *
		 * @param \ManaPHP\Mvc\ModelInterface $model
		 * @return array
		 */
		protected function _readMetaData($model){
			$source =$model->getSource();
			$schema =$model->getSchema();

			$key=get_class($model).'-'.$schema.$source;
			if(!isset($this->_metaData[$key])){
				$this->_metaData[$key]=$this->_initialize($model);
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
			return $this->_readMetaData($model)[self::MODELS_ATTRIBUTES];
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
			return $this->_readMetaData($model)[self::MODELS_PRIMARY_KEY];
		}

        /**
         * Returns attribute which is auto increment or null
         * @param $model
         * @return string |null
         */
        public function getAutoIncrementAttribute($model){
            return $this->_readMetaData($model)[self::MODELS_IDENTITY_COLUMN];
        }

        /**
         * Returns an array of fields which are not part of the primary key
         *
         * @param \ManaPHP\Mvc\ModelInterface $model
         * @return 	array
         */
        public function getNonPrimaryKeyAttributes($model){
            return $this->_readMetaData($model)[self::MODELS_NON_PRIMARY_KEY];
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
			return isset($this->_readMetaData($model)[self::MODELS_ATTRIBUTES][$attribute]);
		}

        /**
         * Returns attributes which types are numerical
         *
         * @param  \ManaPHP\Mvc\ModelInterface $model
         * @return array
         */
        public function getDataTypesNumeric($model){
            return null;
        }
	}
}
