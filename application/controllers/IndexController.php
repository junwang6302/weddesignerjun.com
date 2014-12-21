<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
    }

    public function tasklistAction()
    {
    	Application_Model_Logger::log('tasklistAction');
        // action body
    }

    public function mainAction()
    {
        Application_Model_Logger::log('mainAction');
        // action body
    }


}

