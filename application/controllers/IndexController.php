<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {   
        Application_Model_Logger::log('indexAction');
        // action body
        $secure = new Application_Model_Secure();
        Application_Model_Logger::log('br 0');
        $user = $secure->getUserById(1);
        ob_start();
        var_dump($user);
        $output = ob_get_clean();
        $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
        Application_Model_Logger::log('getUserById in IndexController : '.$output);

    }

    public function tasklistAction()
    {
    	Application_Model_Logger::log('tasklistAction');
        // action body
    }

    // public function mainAction()
    // {
    //     Application_Model_Logger::log('mainAction');
    //     // action body
    // }


}

