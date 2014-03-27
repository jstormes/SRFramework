<?php
class Application_Model_Grids_Grid extends PHPSlickGrid_Db_Table
{

	protected $_name = 'grid';
	protected $_primary = 'grid_id';

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
	
	public function updateItem($updt_dtm, $row, $options=null) {
		
		$row['project_id']=$this->project_id;
			
		parent::updateItem($updt_dtm, $row);
	}
	
	public function addItem($row,$options=null) {
		
		$row['project_id']=$this->project_id;
		
		parent::addItem($row, $options);
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
}