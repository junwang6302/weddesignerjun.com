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
		$stmt = $this->_db->query('SELECT * FROM user WHERE id = ?', array($userId));
		
		if ($stmt->rowCount() == 1) {
			$row = $stmt->fetch();
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