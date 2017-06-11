<?php

class Bootstrap {

	function __construct() {

		$url = isset($_GET['url']) ? $_GET['url'] : null;
		$url = rtrim($url, '/');
		$url = explode('/', $url);

		//print_r($url);
		
		if (empty($url[0])) {
			require 'controllers/Pharmacy_Controller.php';
			$controller = new Pharmacy(); //By default , Pharmacy tab would be loaded.
			$controller->display();
			return false;
		}

		$file = 'controllers/' . $url[0] . '_Controller.php';
		if (file_exists($file)) {
			require $file;
		} else {
			$this->error();
		}
		
		$controller = new $url[0];
		$controller->loadModel($url[0]);      
                                       
                                        /*This section would be good for a generalised MVC Framework for other forms in system.*/
		// calling methods
                                      /* 
		if (isset($url[2])) {
			if (method_exists($controller, $url[1])) {
				$controller->{$url[1]}($url[2]);
			} else {
				$this->error();
			}
		} else {
			if (isset($url[1])) {
				if (method_exists($controller, $url[1])) {
					$controller->{$url[1]}();
				} else {
					$this->error();
				}
			} else {
				$controller->index();
			}
		}
                                         // method_exists( ) — Checks if the class method exists in the given object. 	
                                        */                                      
                            
                                    // check that the requested controller and action are both allowed
                                    // if someone tries to access something else he will be redirected to the error error controller
                                    global $controllers_list;
                                    //print_r($controllers_list);
                                    if (array_key_exists($url[0], $controllers_list))
                                     {    
                                           //  Controller present
                                          if(isset($url[1]))
                                              {
                                                    if (in_array($url[1], $controllers_list[$url[0]])) 
                                                     { 
                                                        //    action Present
                                                        $controller->{$url[1]}();
                                                    } 
                                                    else 
                                                    {                                
                                                        $this->error();
                                                    }                                            
                                              }
                                           else $controller->router(); 
                                            // By default, every controller should point to action called 'router' , if no action is present in url.
                                    } 
                                    else {                               
                                        $this->error();
                                    }	
	}
	
	function error() {
		require 'controllers/error_Controller.php';
		$controller = new Error();
		$controller->index();
		return false;
	}
            
}