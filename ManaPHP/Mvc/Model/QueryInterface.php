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
		 * @return \ManaPHP\Mvc\Model\Query
		 */
		public function setBindParams($bindParams){ }
	}
}
