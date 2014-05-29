<?php

class ProjectController extends Zend_Controller_Action
{

	public function init()
	{
		// Setup our grid
		$this->grid = new Application_Model_Grids_Grid();
	}
	
	public function indexAction()
	{
		
		// Add jQuery assets
		$this->view->headScript()->appendFile('/js/jquery.event.drag-2.2.js');
		$this->view->headScript()->appendFile('/js/jquery.event.drop-2.2.js');
		
		// Add store to manage local storage
		$this->view->headScript()->appendFile('/js/store.min.js');
		
		// Add SlickGrid assets
		$this->view->headScript()->appendFile('/slickgrid/slick.core.js');
		$this->view->headScript()->appendFile('/slickgrid/slick.grid.js');
		$this->view->headLink()->appendStylesheet('/slickgrid/slick.grid.css','screen, print');
		
		// Add PHPSlickGrid assets
		$this->view->headScript()->appendFile('/phpslickgrid/js/json/datacache.js');
		$this->view->headLink()->appendStylesheet('/phpslickgrid/css/fix_for_bootstrap.css','screen, print');
		$this->view->headLink()->appendStylesheet('/phpslickgrid/css/phpslickgrid.css','screen, print');
		$this->view->headScript()->appendFile('/phpslickgrid/js/editors/mysql.js');
		
		// setup the view
		$this->view->grid = $this->grid;

	}
	
	public function jsonAction() 
	{
		// Disable view and layout
		$this->_helper->layout()->disableLayout(true);
		$this->_helper->viewRenderer->setNoRender(true);
		
		// Create a new instance of a JSON web-service service using our grid.
		$server = new PHPSlickGrid_JSON($this->grid);
		
		//$server->add_class($this->grid);
		
		// Expose the JSON database table service trough this action.
		$server->handle();

	}	
	
}