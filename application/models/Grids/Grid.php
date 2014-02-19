<?php
class Application_Model_Grids_Grid extends PHPSlickGrid_Db_Table
{

	protected $_name = 'grid';
	protected $_primary = 'grid_id';

	protected $_friendlyName = 'Grid';
	
	protected function _gridInit() {
		//$this->_gridName="cow";
		//$this->project_id = Zend_Registry::get('peoject_id');
	}
	
	public function updateItem($updt_dtm, $row, $options=null) {
		
		Zend_Registry::get('log')->debug($row);
		
		//$row['project_id']=$this->project_id;
			
		parent::updateItem($updt_dtm, $row);
	}
}