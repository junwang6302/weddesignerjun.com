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
            
            // if(file_exists('../application/languages/'.$this->lang.'/default.mo'))
            //     $this->translate = new Zend_Translate('gettext','../application/languages/'.$this->lang.'/default.mo',$this->lang);
            // else
            //     $this->translate = new Zend_Translate('gettext','../application/languages/en/default.mo','en');
            
            if(count($this->res)>0)
                foreach($this->res as $key=>&$res){
                    if(is_array($res)) $res = (object)$res;

                    if (in_array($key,array('error','msg'))){
                        $res = (object)array('code'=>$res,'text'=>$this->translate->_($res));
                    }
                }
        }
        // return the response
        echo json_encode($this->res);
    }

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


    public function articleAction() {
    	Application_Model_Logger::log('setarticleAction');
        $article = new Application_Model_Article();
		
        if($this->request->isDelete()){

        } else if($this->request->isGet()){
            
        } else if($this->request->isPost()){
            // $date = $this->request->getParam ( 'date' );
            if (empty($this->request->getParam ( 'date' ))){
            // if (empty($date)){
                $date = date('Y-m-d H:i:s');
            }else{
                $date = date('Y-m-d H:i:s',$this->request->getParam ( 'date' ));
            }
                $res['status'] = $article -> addArticle('1',  $this->request->getParam ( 'subject' ), $this->request->getParam ( 'content' ), $date);
                $this->res = $res;
                ob_start();
                var_dump($res);
                $output = ob_get_clean();
                $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
                Application_Model_Logger::log($output);
                $this->sendResponse();

        } else if($this->request->isPut()){

        }

		
    }

}