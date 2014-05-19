<?php
class Application_Model_Grids_Grid extends PHPSlickGrid_Db_Table
{

	/* Required setting for the SlickGrid */
	protected $_name = 'grid';
	protected $_primary = 'grid_id';
	protected $_upd_dtm_col = 'updt_dtm';
	protected $_deleted_col = 'deleted';

	protected $_friendlyName = 'Grid';
	
	/**
	 * Current Project Id
	 * 
	 * @var integer
	 */
	public $project_id=null;
	
	protected function _gridInit() {

		$this->project_id = Zend_Registry::get('project_id');
		
	}
	
	public function _updateItem($row, $state=null) {
		
		$row['project_id']=$this->project_id;
			
		return $row;
	}
	
	public function _addItem($row,$options=null) {
		
		$row['project_id']=$this->project_id;
		
		return $row;
	}
	
	public function addConditionsToSelect(Zend_Db_Select $select) {
	
		$select->where('project_id = ?',$this->project_id);
		
		return $select;
	}
	
	// Add data to the PollReply so that 
	// onPollReply.subscribe() can get it.
	private function PollReply($data) {
		return $data;
	}
	

	// get data from the PollReqest.
	private function PollRequest($data) {
		return $data;
	}
	
	/**
	 * 
	 *
	 * By: jstormes Apr 8, 2014
	 *
	 * @param string $stuff
	 */
	public function ThisIsANewFunction($stuff) {
		
	}
}