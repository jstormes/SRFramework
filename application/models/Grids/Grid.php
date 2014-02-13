<?php
class Application_Model_Grids_Grid extends PHPSlickGrid_Db_Table
{

	protected $_name = 'grid';
	protected $_primary = 'grid_id';

	protected $_friendlyName = 'Grid';
	
	protected function _gridInit() {
		//$this->_gridName="cow";
	
	}
}