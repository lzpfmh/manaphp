<?php 

namespace ManaPHP\Http {

	/**
	 * ManaPHP\Http\ResponseInterface initializer
	 */
	
	interface ResponseInterface {

		/**
		 * Sets the HTTP response code
		 *
		 * @param int $code
		 * @param string $message
		 * @return \ManaPHP\Http\ResponseInterface
		 */
		public function setStatusCode($code, $message);


		/**
		 * Overwrites a header in the response
		 *
		 * @param string $name
		 * @param string $value
		 * @return \ManaPHP\Http\ResponseInterface
		 */
		public function setHeader($name, $value);


		/**
		 * Send a raw header to the response
		 *
		 * @param string $header
		 * @return \ManaPHP\Http\ResponseInterface
		 */
		public function setRawHeader($header);


		/**
		 * Sets output expire time header
		 *
		 * @param \DateTime $datetime
		 * @return \ManaPHP\Http\ResponseInterface
		 */
		public function setExpires($datetime);


		/**
		 * Sends a Not-Modified response
		 *
		 * @return \ManaPHP\Http\ResponseInterface
		 */
		public function setNotModified();


		/**
		 * Sets the response content-type mime, optionally the charset
		 *
		 * @param string $contentType
		 * @param string $charset
		 * @return \ManaPHP\Http\ResponseInterface
		 */
		public function setContentType($contentType, $charset=null);


		/**
		 * Redirect by HTTP to another action or URL
		 *
		 * @param string $location
		 * @param boolean $externalRedirect
		 * @param int $statusCode
		 * @return \ManaPHP\Http\ResponseInterface
		 */
		public function redirect($location, $externalRedirect=null, $statusCode=null);


		/**
		 * Sets HTTP response body
		 *
		 * @param string $content
		 * @return \ManaPHP\Http\ResponseInterface
		 */
		public function setContent($content);


		/**
		 * Sets HTTP response body. The parameter is automatically converted to JSON
		 *
		 *<code>
		 *	$response->setJsonContent(array("status" => "OK"));
		 *</code>
		 *
		 * @param string $content
		 * @param int $jsonOptions
		 * @return \ManaPHP\Http\ResponseInterface
		 */
		public function setJsonContent($content, $jsonOptions=null);


		/**
		 * Appends a string to the HTTP response body
		 *
		 * @param string $content
		 * @return \ManaPHP\Http\ResponseInterface
		 */
		public function appendContent($content);


		/**
		 * Gets the HTTP response body
		 *
		 * @return string
		 */
		public function getContent();


		/**
		 * Sends headers to the client
		 *
		 * @return \ManaPHP\Http\ResponseInterface
		 */
		public function sendHeaders();

		/**
		 * Sets a cookies bag for the response externally
		 * @param \ManaPHP\Http\Response\CookiesInterface $cookies
		 * @return \ManaPHP\Http\ResponseInterface
		 */
		public function setCookies($cookies);
		/**
		 * Sends cookies to the client
		 *
		 * @return \ManaPHP\Http\ResponseInterface
		 */
		public function sendCookies();


		/**
		 * Prints out HTTP response to the client
		 *
		 * @return \ManaPHP\Http\ResponseInterface
		 */
		public function send();


		/**
		 * Sets an attached file to be sent at the end of the request
		 *
		 * @param string $filePath
		 * @param string $attachmentName
		 * @return \ManaPHP\Http\ResponseInterface
		 */
		public function setFileToSend($filePath, $attachmentName=null);

	}
}
