<?php
class WebserviceController extends Zend_Controller_Action {
	
	private $request;
    private $request_data;
    private $res = null;
    private $userId = null;
    private $email = null;
    private $secure;  
    
    public function init() {

        error_reporting(0);
        
        include_once('../library/Custom/functions.php');    
		$this->_helper->viewRenderer->setNoRender();

        $this->request = $this->getRequest();

        // $hash =  $this->request->getParam ( 'hash' );

        $request_data = $this->request->getParams();

        ob_start();
        var_dump($this->request);
        $output = ob_get_clean();
        $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
        Application_Model_Logger::log($output);
        
        if (empty($request_data['stripslashes'])){
            $request_data['stripslashes'] = true;
        }

        if (!$request_data['stripslashes']){
            $this->request_data = sanitizeNestedArrays($request_data);
        }else{
            $this->request_data = $request_data;
        }

        $this->checkHashAction();
        // getUserByHash

        // if($this->request->isPut()) {
        //     parse_str(file_get_contents("php://input"),$request_data);
        //     $request_data = array_merge($request_data, $this->request->getParams());
        // } else {
        //     $request_data = $this->request->getParams();
        // }


    }

    # -------
    private function checkHashAction() {

        // check if hash was passed with data
        if (isset($this->request_data['hash'])) {
            // if so, check if there is a user with that hash
            $secure = new Application_Model_Secure();
            if (strlen($this->request_data['hash']) > 0) {
        
                $res = $secure->getUserByHash($this->request_data['hash']);
                ob_start();
                var_dump($res);
                $output = ob_get_clean();
                $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
                Application_Model_Logger::log("getUserByHash:".$output);
            } else {
                $res = array('status'=>false);
            }
            
            if ($res['status']) {
                // hash is ok
                $this->userId = $res['user']['id'];
                $this->email = $res['user']['email'];
            } else {
                // there is no user with that hash
                $this->res = array('status'=>false,'error'=>'ERROR WS USER IS NOT LOGGED IN');
                $this->sendResponse();
                exit;
            }
        } else {
            // there is no hash in passed data
            $this->res = array('status'=>false,'error'=>'ERROR WS MISSING USER HASH');
            //'Missing user hash.');
            $this->sendResponse();
            exit;
        }
    }

    # -------
    private function sendResponse() {
        if($this->res == null)
            $this->res = array(
                'status' => false,
                'error' => '_ERROR_WS_ACTION_NOT_SPECIFIED'//'Action is not specified.'
            );

        // set a valid content type
        header('Content-Type: application/json; charset=utf-8');

        // change array elements of res into object
        if (is_array($this->res) && count($this->res) > 0){
            // remove all elements that are null or empty arrays
            $this->res = self::arrayCleaner($this->res);
            
            // if(count($this->res)>0)
            //     foreach($this->res as $key=>&$res){
            //         if(is_array($res)) $res = (object)$res;

            //         if (in_array($key,array('error','msg'))){
            //             $res = (object)array('code'=>$res,'text'=>$res);
            //         }
            //     }
        }
        // return the response
        echo json_encode($this->res);
    }

    # -------
    private static function arrayCleaner($a) {
        if (is_array($a)) {
            if (count($a) == 0) //if array is empty return null
                return null;
            else {
                $b = array(); // create temporary array
                foreach($a as $key => $item) {
                    if (is_array($item))
                        $c = self::arrayCleaner($item);
                    else 
                        $c = $item;

                    // if value != null add to temporary array
                    if(!is_null($c))
                        $b[$key] = $c;
                }

                // if temporary array is empty, assign to $a null
                if (count($b) == 0) 
                    $a = null;
                // else asign temporary array
                else
                    $a = $b;
            }
        }

        return $a;
    }

    # -------
    public function articleAction() {

    	Application_Model_Logger::log('articleAction');
        
        $article = new Application_Model_Article();
		
        if($this->request->isDelete()){

        } else if($this->request->isGet()){
        
            $res = $article -> getArticle(
                $this->userId,  
                $this->request_data['articleid']
            );

            $this->res = $res;
            $this->sendResponse();

        } else if($this->request->isPost()){
            
            if (empty($this->request_data['date'])){
                $this->request_data['date'] = date('Y-m-d H:i:s');
            }else{
                $this->request_data['date'] = date('Y-m-d H:i:s',$this->request_data['date']);
            }

                if (empty($this->request_data['articleid'])){
                    $res = $article -> addArticle(
                        $this->userId,  
                        $this->request_data['subject'], 
                        $this->request_data['content'], 
                        $this->request_data['date'], 
                        $this->request_data['public']
                    );
                }else{
                    $res = $article -> updateArticle(
                        $this->userId,  
                        $this->request_data['subject'], 
                        $this->request_data['content'], 
                        $this->request_data['date'], 
                        $this->request_data['public'],
                        $this->request_data['articleid']
                    );
                }  
                
                $this->res = $res;
                $this->sendResponse();

        } else if($this->request->isPut()){

        }
		
    }
   
    # -------
    public function getarticlesAction() {
        
        Application_Model_Logger::log('getarticlesAction');
        $article = new Application_Model_Article();
        
        if($this->request->isDelete()){

        } else if($this->request->isGet()){

             $res = $article -> getArticles(
                $this->userId,  
                $this->request_data['startdate'], 
                $this->request_data['enddate'], 
                $this->request_data['limit'], 
                $this->request_data['contentlimit'],
                $this->request_data['permission'],
                $this->request_data['order']
            );
            $this->res = $res;
            $this->sendResponse();

        } else if($this->request->isPost()){

        } else if($this->request->isPut()){

        }

    }


    # -------
    
}