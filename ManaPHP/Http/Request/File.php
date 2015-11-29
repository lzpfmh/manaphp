<?php 

namespace ManaPHP\Http\Request {

	/**
	 * ManaPHP\Http\Request\File
	 *
	 * Provides OO wrappers to the $_FILES superglobal
	 *
	 *<code>
	 *	class PostsController extends \ManaPHP\Mvc\Controller
	 *	{
	 *
	 *		public function uploadAction()
	 *		{
	 *			//Check if the user has uploaded files
	 *			if ($this->request->hasFiles() == true) {
	 *				//Print the real file names and their sizes
	 *				foreach ($this->request->getUploadedFiles() as $file){
	 *					echo $file->getName(), " ", $file->getSize(), "\n";
	 *				}
	 *			}
	 *		}
	 *
	 *	}
	 *</code>
	 */
	
	class File extends \SplFileInfo implements FileInterface {

		protected $_name;

		protected $_tmp;

		protected $_size;

		protected $_type;

		protected $_real_type;

		protected $_error;

		protected $_key;

		protected $_extension;

		/**
		 * \ManaPHP\Http\Request\File constructor
		 *
		 * @param array $file
		 * @param string $key
		 */
		public function __construct($file, $key=null){
			parent::__construct($file);

			if(isset($file['name'])){
				$this->_name =$file['name'];
				$this->_extension =pathinfo($file['name'],PATHINFO_EXTENSION);
			}

			if(isset($file['tmp_name'])){
				$this->_tmp =$file['tmp_name'];
			}

			if(isset($file['size'])){
				$this->_size =$file['size'];
			}

			if(isset($file['type'])){
				$this->_type =$file['type'];
			}

			if(isset($file['error'])){
				$this->_error =$file['error'];
			}

			$this->_key =$key;
		}


		/**
		 * Returns the file size of the uploaded file
		 *
		 * @return int
		 */
		public function getSize(){
			return $this->_size;
		}


		/**
		 * Returns the real name of the uploaded file
		 *
		 * @return string
		 */
		public function getName(){
			return $this->_name;
		}


		/**
		 * Returns the temporary name of the uploaded file
		 *
		 * @return string
		 */
		public function getTempName(){
			return $this->_tmp;
		}


		/**
		 * Returns the mime type reported by the browser
		 * This mime type is not completely secure, use getRealType() instead
		 *
		 * @return string
		 */
		public function getType(){
			return $this->_type;
		}


		/**
		 * Gets the real mime type of the upload file using finfo
		 *
		 * @return string
		 */
		public function getRealType(){
			$finfo =finfo_open(FILEINFO_MIME_TYPE);
			if(!is_resource($finfo)){
				return '';
			}

			$mime =finfo_file($finfo,$this->_tmp);
			finfo_close($finfo);

			return $mime;
		}


		/**
		 * Returns the error code
		 *
		 * @return string
		 */
		public function getError(){
			return $this->_error;
		}


		/**
		 * Returns the file key
		 *
		 * @return string
		 */
		public function getKey(){
			return $this->_key;
		}


		/**
		 * Checks whether the file has been uploaded via Post.
		 *
		 * @return boolean
		 */
		public function isUploadedFile(){

			return is_string($this->_tmp) &&is_uploaded_file($this->_tmp);
		}


		/**
		 * Moves the temporary file to a destination within the application
		 *
		 * @param string $destination
		 * @return boolean
		 */
		public function moveTo($destination){
			return move_uploaded_file($this->_tmp ,$destination);
		}


		/**
		 * Returns the file extension
		 *
		 * @return string
		 */
		public function getExtension(){
			return $this->_extension;
		}

	}
}
