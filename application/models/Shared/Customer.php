<?php

class Application_Model_Shared_Customer extends Zend_Db_Table_Abstract
{

    protected $_name = 'customer';
    
    protected function _setupDatabaseAdapter()
    {
        // see _initDatabase() in the Bootstrap.php file
        $this->_db = Zend_Registry::get('shared_db');
        parent::_setupDatabaseAdapter();
    }
    
	public function getCustomersByUserId($uid) {
    	
    	$query = $this->select()->setIntegrityCheck(false);
    	$query->from(array('c' => 'customer'),array('customer_id','customer_nm'));
    	$query->join(array('u' => 'customer_user'), 'c.customer_id = u.customer_id ',array());
		$query->where('u.user_id = ? and u.deleted=0 and c.deleted=0',$uid);    	
		$results = $this->fetchAll($query);
		return $results;
    }
    
    public function getCustomerPairsByUserId($uid) {
        $UID = $this->getAdapter()->quote($uid,'INTEGER');
        $SQL = @"
                SELECT customer.customer_id, customer_nm
                FROM customer 
                JOIN customer_user on customer_user.customer_id = customer.customer_id
                WHERE user_id =  $UID AND customer_user.deleted=0 AND customer.deleted=0
                ORDER BY customer_nm
                ";
        return $this->getAdapter()->fetchPairs($SQL);
    }
    
    public function getUsersByCustomerId($cid) {
    	 
    	$query = $this->select()->setIntegrityCheck(false);
    	$query->from(array('u' => 'user'),array('user_id','user_nm','user_full_nm'));
    	$query->join(array('c' => 'customer_user'), 'c.user_id = u.user_id ',array());
    	$query->where('c.customer_id = ? and u.deleted=0 and c.deleted=0',$cid);
    	$results = $this->fetchAll($query);
    	return $results;
    }

    public function getCustomersByApplicationId($appid) {
    	 $query = $this->select()->setIntegrityCheck(false);
    	$query->from(array('c' => 'customer'),array('customer_id','customer_nm'));
    	$query->join(array('a' => 'customer_application'), 'c.customer_id = a.customer_id ',array());
    	$query->where('a.application_id = ? and a.deleted=0 and c.deleted=0',$appid);
    	$results = $this->fetchAll($query);
    	return $results;
    }
    
    public function getApplicationsByCustomerId($cid) {
    
    	$query = $this->select()->setIntegrityCheck(false);
    	$query->from(array('a' => 'application'),array('application_id','application_nm','application_url'));
    	$query->join(array('c' => 'customer_application'), 'c.application_id = a.application_id ',array());
    	$query->where('c.customer_id = ? and a.deleted=0 and a.deleted=0',$cid);
    	$results = $this->fetchAll($query);
    	return $results;
    }
    
    
    public function getUnAssignedCustomersByApplicationId($appid) {
    	$db = $this->getAdapter();
    	$sql = $db->quoteInto("select customer_id, customer_nm from customer where customer_id not in (select customer_id from customer_application where application_id = ?)",$appid);
    	$results = $db->fetchall($sql);
    	return $results;
    }
    
    public function getUnAssignedCustomersByUserId($uid) {
    	$db = $this->getAdapter();
    	$sql = $db->quoteInto("select customer_id, customer_nm from customer where customer_id not in (select customer_id from customer_user where user_id = ?)",$uid);
    	$results = $db->fetchall($sql);
    	return $results;
    }
    
}

