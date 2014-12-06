<?php

/*
 * Logger Model - logging user activity module
 */
class Application_Model_Logger {

  	public function __construct() {}

    public static function log($message = '')
	{
        $config = Zend_Registry::get('config');	
        $session = new Zend_Session_Namespace();	
        
        //WILL COME BACK LATER -J
		// log only if the logger is enabled
		// if ($config->logger->useractivity == 1 && strlen($message) > 0) {
		// 	if (preg_match('/^\d+$/', $session->userId))
		// 		error_log(date('Y-m-d H:i:s') . "\t\t" . $message . "\n", 3, realpath('..') . '/userlog/' . $session->userId . '-' . date('Ymd') . '.log');
		// 	else
		// 		error_log(date('Y-m-d H:i:s') . "\t\t" . $message . "\n", 3, realpath('..') . '/userlog/not-logged-in-' . date('Ymd') . '.log');			
		// }
		if (strlen($message) > 0){
			error_log(date('Y-m-d H:i:s') . "\t\t" . $message . "\n", 3, realpath('..') . 'not-logged-in-' . date('Ymd') . '.log');
	    }
    }
}

?>