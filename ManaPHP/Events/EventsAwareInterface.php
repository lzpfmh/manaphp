<?php 

namespace ManaPHP\Events {

	/**
	 * ManaPHP\Events\EventsAwareInterface initializer
	 */
	
	interface EventsAwareInterface {

		/**
		 * Sets the events manager
		 *
		 * @param \ManaPHP\Events\ManagerInterface $eventsManager
		 */
		public function setEventsManager($eventsManager);


		/**
		 * Returns the internal event manager
		 *
		 * @return \ManaPHP\Events\ManagerInterface
		 */
		public function getEventsManager();
	}
}
