<?php 

namespace ManaPHP\Mvc\Model {

	/**
	 * ManaPHP\Mvc\Model\QueryInterface initializer
	 */
	
	interface QueryInterface {

		/**
		 * Parses the intermediate code produced by \ManaPHP\Mvc\Model\Query\Lang generating another
		 * intermediate representation that could be executed by \ManaPHP\Mvc\Model\Query
		 *
		 * @return array
		 */
		public function parse();


		/**
		 * Executes a parsed PHQL statement
		 *
		 * @param array $bindParams
		 * @param array $bindTypes
		 * @return mixed
		 */
		public function execute($bindParams=null, $bindTypes=null);


		/**
		 * Set default bind parameters
		 *
		 * @param array $bindParams
		 * @param boolean $merge
		 * @return \ManaPHP\Mvc\Model\QueryInterface
		 */
		public function setBindParams($bindParams,$merge = false);

		/**
		 * Sets the cache parameters of the query
		 *
		 * @param array $cacheOptions
		 * @return \ManaPHP\Mvc\Model\QueryInterface
		 */
		public function cache($cacheOptions);

		/**
		 * Tells to the query if only the first row in the resultset must be returned
		 *
		 * @param boolean $uniqueRow
		 * @return \ManaPHP\Mvc\Model\QueryInterface
		 */
		public function setUniqueRow($uniqueRow);
	}
}
