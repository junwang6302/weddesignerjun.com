<?php

Zend_Loader::loadClass('Zend_Registry');
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

	# -------
	protected function _initResourceAutoloader()
	{
	     $autoloader = new Zend_Loader_Autoloader_Resource(array(
	        'basePath'  => APPLICATION_PATH,
	        'namespace' => 'Application',
	     ));

	     $autoloader->addResourceTypes(array(
		     'model' => array(
		         'path'      => 'models',
		         'namespace' => 'Model',
		     ),
		     'form' => array(
		         'path'      => 'forms',
		         'namespace' => 'Form',
		     ),
		 ));

	     return $autoloader;
	}
	# -------

    protected function _initRegistry()
    {
        $config = new Zend_Config($this->getOptions());
        Zend_Registry::set('config', $config);
        Application_Model_Logger::log('_initRegistry' .APPLICATION_PATH);
        
        $resource = $this->getPluginResource('db');

        $db = $resource->getDbAdapter();
        $db->setFetchMode(Zend_Db::FETCH_ASSOC);
        Zend_Registry::set('db', $db);

    }


	protected function _initRoutes()
    {
    	$this->bootstrap('frontController');
        $config = new Zend_Config($this->getOptions());
        Application_Model_Logger::log('_initRoutes');
        
        //GET MOBILE USER AGENT -J
        // $mobileBrowsers = array('iPhone', 'Android', 'webOS', 'BlackBerry', 'iPod');
        // $mobileUser = false;
        // foreach ($mobileBrowsers as $mb)
        // {
        //     if (strpos($_SERVER['HTTP_USER_AGENT'], $mb) !== false)
        //     {
        //         $mobileUser = true;
        //         break;
        //     }
        // }


        $router = $this->frontController->getRouter();
        // $router->addRoute('main', new Zend_Controller_Router_Route('main/', array('controller' => 'index', 'action' => 'main')));
        $router->addRoute('addarticle', new Zend_Controller_Router_Route('addarticle/', array('controller' => 'index', 'action' => 'addarticle')));
        $router->addRoute('articlelist', new Zend_Controller_Router_Route('articlelist/', array('controller' => 'index', 'action' => 'articlelist')));
        $router->addRoute('tasklist', new Zend_Controller_Router_Route('tasklist/', array('controller' => 'index', 'action' => 'tasklist')));
        $router->addRoute('article', new Zend_Controller_Router_Route('article/', array('controller' => 'index', 'action' => 'article')));
        
        $router->addRoute('WS01', new Zend_Controller_Router_Route('/webservice/article', array('controller' => 'webservice', 'action' => 'article')));
        $router->addRoute('WS02', new Zend_Controller_Router_Route('/webservice/getarticles', array('controller' => 'webservice', 'action' => 'getarticles')));
    }


	protected function _initView()
    {
    	Application_Model_Logger::log('_initView');
    	// Initialize view
        $view = new Zend_View();
        $view->headTitle('Webdesignerjun');

        // Add it to the ViewRenderer
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper(
                        'ViewRenderer'
        );

        // define view script path
        $viewScriptPath = ':controller/:action.:suffix';
        // if (MOBILE_VERSION === true)
        //     $viewScriptPath = 'mobile/' . $viewScriptPath;

        $viewRenderer->setView($view)
                ->setViewScriptPathSpec($viewScriptPath);

        // Return it, so that it can be stored by the bootstrap
        return $view;
    }


}

