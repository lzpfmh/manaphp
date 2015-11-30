<?php 

namespace ManaPHP\Mvc\Model {

	/**
	 * ManaPHP\Mvc\Model\ResultInterface initializer
	 */
	
	interface ResultInterface {

		/**
		 * Sets the object's state
		 *
		 * @param boolean $dirtyState
		 */
		public function setDirtyState($dirtyState);

	}
}
