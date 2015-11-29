<?php 

namespace ManaPHP\Http\Response {

	/**
	 * ManaPHP\Http\Response\Headers
	 *
	 * This class is a bag to manage the response headers
	 */
	
	class Headers implements \ManaPHP\Http\Response\HeadersInterface {

		protected $_headers;

		/**
		 * Sets a header to be sent at the end of the request
		 *
		 * @param string $name
		 * @param string $value
		 */
		public function set($name, $value){ }


		/**
		 * Gets a header value from the internal bag
		 *
		 * @param string $name
		 * @return string
		 */
		public function get($name){ }


		/**
		 * Sets a raw header to be sent at the end of the request
		 *
		 * @param string $header
		 */
		public function setRaw($header){ }


		/**
		 * Removes a header to be sent at the end of the request
		 *
		 * @param string $header Header name
		 */
		public function remove($header_index){ }


		/**
		 * Sends the headers to the client
		 *
		 * @return boolean
		 */
		public function send(){ }


		/**
		 * Reset set headers
		 *
		 */
		public function reset(){ }


		/**
		 * Returns the current headers as an array
		 *
		 * @return array
		 */
		public function toArray(){ }


		/**
		 * Restore a \ManaPHP\Http\Response\Headers object
		 *
		 * @param array $data
		 * @return \ManaPHP\Http\Response\Headers
		 */
		public static function __set_state($data){ }

	}
}
