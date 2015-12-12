<?php 

namespace ManaPHP\Mvc\Model {

	/**
	 * ManaPHP\Mvc\Model\ValidationFailed
	 *
	 * This exception is generated when a model fails to save a record
	 * ManaPHP\Mvc\Model must be set up to have this behavior
	 */
	
	class ValidationFailed extends \ManaPHP\Mvc\Model\Exception {

		protected $_model;

		protected $_messages;

		/**
		 * \ManaPHP\Mvc\Model\ValidationFailed constructor
		 *
		 * @param \ManaPHP\Mvc\Model $model
		 * @param \ManaPHP\Mvc\Model\Message[] $validationMessages
		 */
		public function __construct($model, $validationMessages){ }


		/**
		 * Returns the complete group of messages produced in the validation
		 *
		 * @return \ManaPHP\Mvc\Model\Message[]
		 */
		public function getMessages(){ }


		/**
		 * Returns the model that generated the messages
		 *
		 * @return \ManaPHP\Mvc\Model
		 */
		public function getModel(){ }

	}
}
