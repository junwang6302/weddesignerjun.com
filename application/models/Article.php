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
	public function addArticle($userId, $subject, $content, $date=null, $public=false){
		
		$res = array();
		
		try {	

			if (empty($date)) $date = date('Y-m-d H:i:s');

			if (empty($subject)) throw new Exception('SUBJECT IS EMPTY.');

			if (empty($content)) throw new Exception('CONTENT IS EMPTY.');

			if (!empty($public)&&($public == '1')) {

				$stmt = $this->_db->query('INSERT INTO `article` (`subject`, `content`, `date`,`user_id`, `public`) VALUES (?, ?, ?, ?, ?)', array($subject, $content, $date, $userId, '1'));
			}else{
				$stmt = $this->_db->query('INSERT INTO `article` (`subject`, `content`, `date`,`user_id`) VALUES (?, ?, ?, ?)', array($subject, $content, $date, $userId));
			}

			$stmt->close();

		} catch (Exception $e) {

			Application_Model_Logger::log('ADDARTICLE FAILED USER ID: '.$userId . ' ERROR MESSAGE' . $e->getMessage());
			return $res = array(
						'error' => $e->getMessage(),
						'status' => false
					);

		}

			return $res = array(
						'status' => true
					);;

	}

	# -------
	public function updateArticle($userId, $subject=null, $content=null, $date=null, $public=null, $articleId){
		
		Application_Model_Logger::log('updateArticle action');
		$res = array();
		
		try {	

			if (empty($userId)) throw new Exception('USER ID IS EMPTY.');

			if (empty($articleId)) throw new Exception('ARTICLE ID IS EMPTY.');

			$hasArticle = $this -> hasArticle($userId, $articleId);

			if (!$hasArticle['status']) throw new Exception($hasArticle['error']);

			if (!empty($date)){
				Application_Model_Logger::log('update date');
				$stmt = $this->_db->query('UPDATE `article` SET `date`= ? WHERE `id`=? ;', array($date, $articleId));
			}

			if (!empty($subject)) {
				Application_Model_Logger::log('update subject');
				$stmt = $this->_db->query('UPDATE `article` SET `subject`= ? WHERE `id`=? ;', array($subject, $articleId));
			}

			if (!empty($content)) {
				Application_Model_Logger::log('update content');
				$stmt = $this->_db->query('UPDATE `article` SET `content`= ? WHERE `id`=? ;', array($content, $articleId));
			}
			
			if (($public == '0')||($public == '1')){
				Application_Model_Logger::log('update public');
				$stmt = $this->_db->query('UPDATE `article` SET `public`= ? WHERE `id`=? ;', array($public, $articleId));
			}

			$stmt->close();

		} catch (Exception $e) {

			Application_Model_Logger::log('ADDARTICLE FAILED USER ID: '.$userId . ' ERROR MESSAGE' . $e->getMessage());
			return $res = array(
						'error' => $e->getMessage(),
						'status' => false
					);

		}

			return $res = array(
						'status' => true
					);

	}

	# -------
	public function hasArticle($userId, $articleId){
		
		$res = array();
		
		try {
			if (empty($userId)) throw new Exception('USER ID IS EMPTY.');
			if (empty($articleId)) throw new Exception('ARTICLE ID IS EMPTY.');

			$stmt = $this->_db->query('SELECT id FROM article WHERE id = ? AND user_id = ? LIMIT 1;', array($articleId, $userId));

			if ($stmt->rowCount() == 1) {
				$row = $stmt->fetch();
				
				if ($row['id'] == $articleId){
					$res = array(
						'status' => true
					);
				}else{
					throw new Exception('NO PERMISSION OR DUP ARTICLE.');
				}
			}else{
				throw new Exception('NO PERMISSION TO CHCANGE THIS ARTICLE.');
			}

			$stmt->close();

		} catch (Exception $e) {
			$res = array(
				'error' => $e->getMessage(),
				'status' => false
			);
		}
		return $res;
	}


}

?>