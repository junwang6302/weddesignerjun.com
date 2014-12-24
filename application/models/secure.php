<?php

class Application_Model_Secure{
	protected $_config;
	protected $_db;
	
	# -------
	public function __construct()
	{
		// map db and config object to $this object
		$this->_config = Zend_Registry::get('config');
		$this->_db = Zend_Registry::get('db');
	}

	# -------
	public function getUserById($userId){

		$res = array();
		 Application_Model_Logger::log('br 1'.$userId);
		$stmt = $this->_db->query('SELECT * FROM user WHERE id = ?', array($userId));
		
		 Application_Model_Logger::log('br 2');
		if ($stmt->rowCount() == 1) {
			$row = $stmt->fetch();
			
			ob_start();
			var_dump($row);
			$output = ob_get_clean();
			$output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
			Application_Model_Logger::log('getUserById: '.$userId.' \n'. $output);

			$res = array(
						'user' => $row,
						'status' => true
					);

		}else{
			
			$res = array(
						'error' => 'can not find user!!!',
						'status' => false
					);  
		
		}
		$stmt->close();

		return $res;
	}

}

?>