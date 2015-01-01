<?php
class WebserviceController extends Zend_Controller_Action {
	
	private $request;
    private $request_data;
    private $res = null;
    private $userId = null;
    private $emailAddress = null;
    private $secure;  
    
    public function init() {

		$this->_helper->viewRenderer->setNoRender();

        $this->request = $this->getRequest();

        $hash =  $this->request->getParam ( 'hash' );
        //CHECK HASH HERE-J

        // if($this->request->isPut()) {
        //     parse_str(file_get_contents("php://input"),$request_data);
        //     $request_data = array_merge($request_data, $this->request->getParams());
        // } else {
        //     $request_data = $this->request->getParams();
        // }

    }

    public function addarticleAction() {
    	Application_Model_Logger::log('setarticleAction');
		
		if (empty($this->request->getParam ( 'date' ))){
			$date = date('Y-m-d H:i:s');
		}else{
			$date = date('Y-m-d H:i:s',$this->request->getParam ( 'date' ));
		}

    	$article = new Application_Model_Article();
    	$res['status'] = $article -> addArticle('1',  $this->request->getParam ( 'subject' ), $this->request->getParam ( 'content' ), $date);
    	$this->res = $res;
    	ob_start();
		var_dump($res);
		$output = ob_get_clean();
		$output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
		Application_Model_Logger::log($output);
        $this->sendResponse();
    }

}