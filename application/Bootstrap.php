<?php

Zend_Loader::loadClass('Zend_Registry');
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

	# -------

    protected function _initRegistry()
    {
        $config = new Zend_Config($this->getOptions());
        Zend_Registry::set('config', $config);

        // $resource = $this->getPluginResource('db');
        // $db = $resource->getDbAdapter();
        // $db->setFetchMode(Zend_Db::FETCH_ASSOC);
        // Zend_Registry::set('db', $db);
    }


	protected function _initRoutes()
    {
    	// set up baseUrl
        $config = new Zend_Config($this->getOptions());
        Application_Model_Logger::log('_initRoutes');
    }


}

