<?php

class PaymentController extends Zend_Controller_Action
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
    
    public function cartAction()
    {   
        Application_Model_Logger::log('cartAction');
        // action body
        $secure = new Application_Model_Secure();

    }

    public function shippingAction()
    {   
        Application_Model_Logger::log('shippingAction');
        // action body
        $secure = new Application_Model_Secure();

    }

}

