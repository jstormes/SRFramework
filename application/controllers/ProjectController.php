<?php

class ProjectController extends Zend_Controller_Action
{

	public function init()
	{
		//$this->table = new Application_Model_Grids_Grid();
	}
	
	public function indexAction()
	{
		// add required jQuery modules 
		$this->view->headScript()->appendFile('/slickgrid/lib/jquery.event.drag-2.2.js');
		$this->view->headScript()->appendFile('/slickgrid/lib/jquery.event.drop-2.2.js');
		
		
		
		// add the slick.grid modules
		$this->view->headScript()->appendFile('/slickgrid/slick.core.js');
		$this->view->headScript()->appendFile('/slickgrid/slick.grid.js');
		
		// Add the slickgrid css 
		$this->view->headLink()->appendStylesheet('/slickgrid/slick.grid.css','screen, print');
		
		$table = new Application_Model_Grids_Grid();
		$this->view->table = $table;

	}
	
	
	
}