<?php

class Application_Model_Shared_Application extends Zend_Db_Table_Abstract
{

    protected $_name = 'application';
    
    /**
     * Use the PhpSlickGrid Rowset class
     * rather than the Zend Rowset class.
     * 
     * @var string
     */
    // protected $_rowsetClass = 'PhpSlickGrid_Db_Table_Rowset';
    
    protected $_referenceMap    = array(
    		'user_application' => array(
    				'columns'           => array('application_id'),
    				'refTableClass'     => 'Application_Model_Shared_UserApplication',
    				'refColumns'        => array('application_id')
    		)
    
    );
    
    
    protected function _setupDatabaseAdapter()
    {
        // see _initDatabases() in the Bootstrap.php file
        $this->_db = Zend_Registry::get('shared_db');
        parent::_setupDatabaseAdapter();
    }
        public function getApplicationsByUserId($uid) {
        
        $query = $this->select()->setIntegrityCheck(false);
        $query->from(array('a' => 'application'),array('application_id','application_nm', 'application_url','application_landing_url'));
        $query->join(array('u' => 'user_application'), 'u.application_id = a.application_id',array());
        $query->where('u.user_id = ? and u.deleted=0 and a.deleted=0',$uid);  
        $query->order('a.application_nm');
        $results = $this->fetchAll($query);
        return $results;
    }

    public function getApplicationsByCustomerId($cid) {
        $query = $this->select()->setIntegrityCheck(false);
        $query->from(array('a' => 'application'),array('application_id','application_nm', 'application_url','application_landing_url'));
        $query->join(array('c' => 'customer_application'), 'c.application_id = a.application_id',array());
        $query->where('c.customer_id = ? and c.deleted=0 and a.deleted=0',$cid);
        $results = $this->fetchAll($query);
        return $results;
    }
    
    public function getApplicationsDetailsByUserId($uid) {

        $split_hostname=explode(".", $_SERVER['SERVER_NAME']);
        $domain=$split_hostname[count($split_hostname)-2].".".$split_hostname[count($split_hostname)-1];
        $query = $this->select()->setIntegrityCheck(false);
        $query->from(array('a' => 'application'),array('application_id','application_nm', 'CONCAT(\'http://\', application_url,\'.\',\''.$domain. '\',coalesce(application_landing_url,\'\'))  as application_url','application_landing_url'));
        $query->join(array('u' => 'user_application'), 'u.application_id = a.application_id',array());
        $query->where('u.user_id = ? and u.deleted=0 and a.deleted=0',$uid);
        $results = $this->fetchAll($query);
        return $results;
    }
    
    
   
    
    public function getUnAssignedApplicationsByUserId($uid) {
        $db = $this->getAdapter();
        $sql = $db->quoteInto("select application_id, application_nm from application where application_id not in (select application_id from user_application where user_id = ?)",$uid);
        $results = $db->fetchall($sql);
        return $results;
    }
    
    public function getUnAssignedApplicationsByCustomerId($cid) {
        $db = $this->getAdapter();
        $sql = $db->quoteInto("select application_id, application_nm from application where application_id not in (select application_id from customer_application where customer_id = ?)",$cid);
        $results = $db->fetchall($sql);
        return $results;
    }
    
    public function  getApplicationByName ($applnm) {
        $db = Zend_Registry::get('db2');
        $query = $db->select();
        $query->from(array('a' => 'application'),array('application_id','application_nm',''));
        $query->where('a.application_nm=?',$applnm);
        return($db->fetchAll($query));
    }
    
    public function getUsersByApplicationAndClient($aid,$cid) {
        // select * from user where user_id in 
        // (select user_application.user_id from customer_user 
        // join user_application on user_application.user_id = customer_user.user_id
        // where customer_id=$cid and application_id=$aid)
        $db = $this->getAdapter();
        $cid = $this->getAdapter()->quote($cid,'INTEGER');
        $aid = $this->getAdapter()->quote($aid,'INTEGER');
        $sql = "select user_id, user_nm, user_abbr, user_full_nm, user_company, user_phone1, user_phone2 from user where user_id in 
                 (select user_application.user_id from customer_user 
                 join user_application on user_application.user_id = customer_user.user_id
                 where customer_id=$cid and application_id=$aid)
                 order by user_full_nm";
        $results = $db->fetchall($sql);
        return $results;
        
    }
    
}