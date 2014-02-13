<?php

class ProjectController extends Zend_Controller_Action
{

	public function init()
	{
		$this->grid = new Application_Model_Grids_Grid();
	}
	
	public function indexAction()
	{
		
		// add required jQuery files
		$this->view->headScript()->appendFile('/js/jquery.event.drag-2.2.js');
		$this->view->headScript()->appendFile('/js/jquery.event.drop-2.2.js');
		
		
		// add the SlickGrid files
		$this->view->headScript()->appendFile('/slickgrid/slick.core.js');
		$this->view->headScript()->appendFile('/slickgrid/slick.grid.js');
		$this->view->headLink()->appendStylesheet('/slickgrid/slick.grid.css','screen, print');
		
		// add the PHPSlickGrid files
		$this->view->headScript()->appendFile('/phpslickgrid/js/json/datacache.js');
		$this->view->headLink()->appendStylesheet('/phpslickgrid/css/fix_for_bootstrap.css','screen, print');
		
		// setup the view
		$this->view->grid = $this->grid;

	}
	
	public function jsonAction() 
	{
		// Disable view and layout
		$this->_helper->layout()->disableLayout(true);
		$this->_helper->viewRenderer->setNoRender(true);
		
		// Create a new instance of a JSON web-service service using our source table and grid configuration.
		$server = new PHPSlickGrid_JSON($this->grid);
		
		// Expose the JSON database table service trough this action.
		$server->handle();

	}

	
	
	
}