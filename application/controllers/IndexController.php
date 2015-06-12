<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $secure = new Application_Model_Secure();

        if (!$secure->isLoggedIn()){
            $this->view->userHash = '123';
        }
    }

    public function indexAction()
    {   
        Application_Model_Logger::log('indexAction');
        // action body
    }

    public function addarticleAction()
    {   
        Application_Model_Logger::log('addarticleAction');
        // action body
    }

    public function articlelistAction()
    {   
        Application_Model_Logger::log('articlelistAction');
        // action body
    } 

    public function articleAction()
    {   
        Application_Model_Logger::log('articleAction');
        // action body
        $this->view->article_id = $this->getRequest()->getParam('id');
    }

    public function tasklistAction()
    {
    	Application_Model_Logger::log('tasklistAction');
        // action body
    }

    

}

