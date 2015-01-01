<?php

class Application_Model_Article{
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
	public function addArticle($userId, $subject, $content, $date=null){
		
		try {	

			if (empty($date)) $date = date('Y-m-d H:i:s');

			if (empty($subject)) throw new Exception('SUBJECT IS EMPTY.');

			if (empty($content)) throw new Exception('CONTENT IS EMPTY.');

			$stmt = $this->_db->query('INSERT INTO `article` (`subject`, `content`, `date`,`user_id`) VALUES (?, ?, ?, ?)', array($subject, $content, $date, $userId));

			$stmt->close();

		} catch (Exception $e) {

			Application_Model_Logger::log('ADDARTICLE FAILED USER ID: '.$userId . ' ERROR MESSAGE' . $e->getMessage());
			return false;

		}
		
			return true;

	}

}

?>