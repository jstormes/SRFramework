<?php

class Application_Model_Shared_UserApplication extends Zend_Db_Table_Abstract
{

    protected $_name = 'user_application';

    protected function _setupDatabaseAdapter()
    {
        // see _initDatabase() in the Bootstrap.php file
        $this->_db = Zend_Registry::get('shared_db');
        parent::_setupDatabaseAdapter();
    }
    
    public function getUserRoleByApplication($user_id,$application_id) {
        $sel = $this->select()->setIntegrityCheck(false);
        $sel->from(array('u' => 'user_application'),array());
        $sel->join(array('a' => 'application_role'), 'a.application_role_id = u.application_role_id');
        $sel->where('u.user_id = ?',$user_id);
        $sel->where('u.application_id = ?',$application_id);
        return $this->fetchAll($sel)->current();
    }
}