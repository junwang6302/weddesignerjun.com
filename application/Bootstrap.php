<?php

Zend_Loader::loadClass('Zend_Registry');
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

	# -------

    protected function _initRegistry()
    {
        $config = new Zend_Config($this->getOptions());
        Zend_Registry::set('config', $config);
        Application_Model_Logger::log('_initRegistry');

		// ob_start();
		// var_dump($config);
		// $output = ob_get_clean();
		// $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
		// Application_Model_Logger::log('output: '.$output);
        // $resource = $this->getPluginResource('db');
        // $db = $resource->getDbAdapter();
        // $db->setFetchMode(Zend_Db::FETCH_ASSOC);
        // Zend_Registry::set('db', $db);
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
        $router->addRoute('tasklist', new Zend_Controller_Router_Route('tasklist/', array('controller' => 'index', 'action' => 'tasklist')));
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

       protected function _initAutoLoadModuleAdmin()
    {
        $autoloader = new Zend_Application_Module_AutoLoader(
                array(
            'namespace' => 'Admin',
            'basePath' => APPLICATION_PATH . '.modules/admin',
                )
        );

        return $autoloader;
    }

    protected function _initAutoLoadModuleDefault()
    {
        $autoLoader = new Zend_Application_Module_Autoloader(
                array(
            'namespace' => 'Webdesignerjun',
            'basePath' => dirname(__FILE__),
                )
        );

        return $autoLoader;
    }


}

