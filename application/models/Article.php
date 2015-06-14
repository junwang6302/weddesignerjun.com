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
	public function addArticle($userId, $subject, $content, $date=null, $public=false, $tags=null){
		
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

			if (!empty($tags)){

				$addTag = $this -> addTag($this->_db->lastInsertId(), $tags);
				
				if (!$addTag['status']) throw new Exception('FAILED TO ADD TAG.');
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
	public function updateArticle($userId, $subject=null, $content=null, $date=null, $public=null, $articleId, $tags=null){
		//NEED TO ADD CODE HERE -JUN
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

			if (!empty($tags)){
				$addTag = $this -> addTag($this->_db->lastInsertId(), $tags);
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


			$isArticlePublic = $this->isArticlePublic($articleId);

			if ($isArticlePublic['status']){
				$res = array(
						'status' => true
					);
				return $res;
			}

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
				throw new Exception('NO PERMISSION.');
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

	# -------
	public function isArticlePublic($articleId){
		
		$res = array();
		
		try {

			if (empty($articleId)) throw new Exception('ARTICLE ID IS EMPTY.');
			
			$stmt = $this->_db->query('SELECT public FROM article WHERE id = ? LIMIT 1;', array($articleId));

			if ($stmt->rowCount() == 1) {
				$row = $stmt->fetch();
				
				if ($row['public'] == 1){
					$res = array(
						'status' => true
					);
				}else{
					throw new Exception('ARTICLE '.$articleId.' IS NOT PUBLIC.');
				}
			}else{
				throw new Exception('ARTICLE '.$articleId.' DOES NOT EXIST.');
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

	# -------
	public function isTagged($articleId, $tag){
		
		$res = array();
		
		try {

			// if (empty($articleId)) throw new Exception('ARTICLE ID IS EMPTY.');

			// if (empty($tag)) throw new Exception('TAG IS EMPTY.');
			
			$stmt = $this->_db->query('SELECT tag FROM tag WHERE article_id = ? AND tag = ?;', array($articleId, $tag));

			if ($stmt->rowCount() > 0) {
				
				$res = array(
					'status' => true
				);

			}else{

				$res = array(
					'status' => false
				);

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

	# -------
	public function addTag($articleId, $tags){
		
		$res = array();
		
		try {

			if (empty($articleId)) throw new Exception('ARTICLE ID IS EMPTY.');

			if (empty($tags)) throw new Exception('TAG IS EMPTY.');
			
			$tagList = explode(',', $tags);
			
			foreach($tagList as $tag) {
				
				$isTagged = $this->isTagged($articleId, $tag);
				
				if (!$isTagged['status']){
					$stmt = $this->_db->query('INSERT INTO `tag` (`article_id`, `tag`) VALUES (?, ?);', array($articleId, $tag));
			    }

		    }		

			$stmt->close();

		} catch (Exception $e) {
			
			Application_Model_Logger::log('CAN NOT ADD TAG: '. $e->getMessage());
			$res = array(
				'error' =>'CAN NOT ADD TAG: '. $e->getMessage(),
				'status' => false
			);
		}
		return $res;
	}
	# -------
	public function getArticle($userId, $articleId){
		
		$res = array();
		
		try {
			if (empty($userId)) throw new Exception('USER ID IS EMPTY.');
			if (empty($articleId)) throw new Exception('ARTICLE ID IS EMPTY.');

			$hasArticle = $this -> hasArticle($userId, $articleId);
			if (!$hasArticle['status']) throw new Exception($hasArticle['error']);

			$stmt = $this->_db->query('SELECT * FROM `article` WHERE `id` = ? LIMIT 1;', array($articleId));

			if ($stmt->rowCount() == 1) {
				$row = $stmt->fetch();
					$res = array(
						'article' => $row,
						'status' => true
					);
			}else{
				throw new Exception('NO ARTICLE FOUND.');
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

	# -------
	public function getArticles($userId, $startdate, $enddate, $limit, $contentlimit, $permission, $order){
		$res = array();

		try {

			$query = 'SELECT * FROM `article` WHERE `id` IS NOT NULL ';

			if (empty($userId)) throw new Exception('USER ID IS EMPTY.');
			if (empty($limit)) $limit = 6;
			
			if (!empty($startdate)) {
				$startdate = date('Y-m-d H:i:s', $startdate);
				$query = $query." AND `date` > `$startdate` ";
			}

			if (!empty($enddate)) {
				$enddate = date('Y-m-d H:i:s', $enddate);
				$query = $query." AND `date` < `$enddate` ";
			}

			if (empty($permission)) {
				$permission = 'public';
			}
			
			if ($permission != 'public') {
				$query = $query." AND `user_id` = $userId ";
			}else{
				$query = $query." AND (`public` = 1 OR user_id = $userId) ";
			}
			
			if (empty($contentlimit)) $contentlimit = 200;
			Application_Model_Logger::log('query: '.$query.' ORDER BY date DESC LIMIT ?;');

			if ($order!='oldest'){
		
				$stmt = $this->_db->query($query.' ORDER BY date DESC LIMIT ?;', array($limit));
				Application_Model_Logger::log('query: '.$query.' ORDER BY date DESC LIMIT ?;');
			}else{
				
				$stmt = $this->_db->query($query.' ORDER BY date LIMIT ?;', array($limit));
				Application_Model_Logger::log('query: '.$query.' ORDER BY date LIMIT ?;');
			}
			

			if ($stmt->rowCount() > 0) {
				
				$row = $stmt->fetchAll();

				$res = array(
					'articles' => $row,
					'status' => true
				);

				ob_start();
				var_dump($res);
				$output = ob_get_clean();
				$output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
				Application_Model_Logger::log('getArticles res: '.$output);

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



	# -------


}

?>