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

    }

    public function tasklistAction()
    {
    	Application_Model_Logger::log('tasklistAction');
        // action body
    }

    

}

