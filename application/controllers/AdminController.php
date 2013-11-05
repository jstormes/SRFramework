<?php
/**
 * 
 * @author jstormes
 *
 */
class AdminController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $this->config       = Zend_Registry::get('config');
        $this->acl          = Zend_Registry::get('acl');
        $this->user         = Zend_Registry::get('user');
        $this->applications = Zend_Registry::get('applications');
        $this->navigation   = Zend_Registry::get('navigation');
        $this->app          = Zend_Registry::get('app');       
        $this->customers    = Zend_Registry::get('customers');
        $this->current_customer    = Zend_Registry::get('current_customer');
        $this->user_role    = Zend_Registry::get('user_role');
    }

    public function indexAction()
    {
        // action body
    }

    public function usersAction()
    {
        // action body
        
        // *******************************
        // Add Javascript to page header
        // *******************************
        $this->view->headScript()->prependFile('//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js');
        $this->view->headScript()->appendFile('/js/jquery.dataTables.js');
        
        // Get the list of users
        $UserTable = new Application_Model_Common_User();
        $this->view->users = $UserTable->fetchAll();
        
    }

    public function usermaintAction()
    {
        // action body
        
        $UserTable = new Application_Model_Common_User();
        $ApplicaitonTable = new Application_Model_Common_Application();
        
        // Get the primary key for requested record
        $user_id=$this->_request->getParam('user_id',0);
        
        // Get ClientRows
        if ($user_id==0) {
            $this->view->user = $UserTable->createRow();
            echo "this is 0";
        }
        else {
            $this->view->user = $UserTable->find($user_id)->current();
            $this->view->applications = $ApplicaitonTable->getApplicationsByUserId($user_id);
            $this->view->applist = $ApplicaitonTable->getUnAssignedApplicationsByUserId($user_id);
            print_r($this->view->applist);
        }
        
        // Get ApplicaitonRows
        
        // if postback
            // Recapture UserRow
            
            
    }


}





