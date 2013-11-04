<?php
require_once realpath(APPLICATION_PATH . '/../library/').'/Soaptest.php';


class ExamplesController extends Zend_Controller_Action
{
    private $_WSDL_URI = "http://blank.stormes.net/examples/soapserver?wsdl";
    
    public function init()
    {	 
        $this->config       = Zend_Registry::get('config');
        $this->acl          = Zend_Registry::get('acl');
        $this->user         = Zend_Registry::get('user');
        $this->applications = Zend_Registry::get('applications');
        $this->navigation   = Zend_Registry::get('navigation');
        
        $this->app          = Zend_Registry::get('app');
        
        $this->customers    = Zend_Registry::get('customers');
        $this->current_customer    = Zend_Registry::get('current_customer');
        $this->user_role    = Zend_Registry::get('user_role');
        // Set the wsdl for SOAP 

        // Set the URI for JSON
    }

    public function indexAction()
    {    
    	
    }

	
    public function soapserverAction()
    {
        // Disable menus and don't render any view.
        $this->_helper->layout()->disableLayout(true);
        $this->_helper->viewRenderer->setNoRender(true);

    	if(isset($_GET['wsdl'])) {
    		//return the WSDL
    		$autodiscover = new Zend_Soap_AutoDiscover();
    	    $autodiscover->setClass('Soaptest');
    	    $autodiscover->handle();
		} else { 
			//handle SOAP request
    		$soap = new Zend_Soap_Server($this->_WSDL_URI); 
    	    $soap->setClass('Soaptest');
    	    $soap->handle();
		}
    }
    
    public function soapclientAction() {
        
        try{
            // Connect to the WSDL and get the available services
            $client = new Zend_Soap_Client($this->_WSDL_URI,array('login' => $_COOKIE['cavuser'], 'password' => $_COOKIE['cavpad']));

            // Call some services and display the results.
            $this->view->add_result = $client->math_add(11, 55);
            $this->view->logical_not_result = $client->logical_not(true);
            $this->view->sort_result = $client->simple_sort( array("d" => "lemon", "a" => "orange", "b" => "banana", "c" => "apple"));
        }
        catch (Exception $ex) {
            // Catch any exceptions and try to display the error.
            print_r($ex);
        }
        
    }
    
    public function phpinfoAction() {
        
    }

    public function warpinfoAction() {
        $this->view->config       = $this->config;
        $this->view->acl          = $this->acl;
        $this->view->user         = $this->user;
        $this->view->navigation   = $this->navigation;
        $this->view->applications = $this->applications;
        $this->view->app          = $this->app;
        $this->view->customers    = $this->customers;
        $this->view->current_customer = $this->current_customer;
        $this->view->user_role    = $this->user_role;
    }

}



