<?php

class ProjectController extends Zend_Controller_Action
{

	public function init()
	{
		$this->table = new Application_Model_Grids_Grid();
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
		
		
		
		// setup the view
		$this->view->table = $this->table;

	}
	
	public function serviceAction() 
	{
		$Table = new Application_Model_Grids_Grid();
		
		$this->view->layout()->disableLayout(true);
		$this->_helper->viewRenderer->setNoRender(true);
		//******************************************************************
		// Macro actions:
		// if request is for a js file serve the js file.
		if ($this->_getParam('js','false')!='false') {
			$js=new PHPSlickGrid_Minify_js();
			$js->serve_file($_GET['js']);
			exit(); // stop the view from being displayed
			break;
		}
		// if request is for a css file serve the css file.
		if ($this->_getParam('css','false')!='false') {
			$css=new PHPSlickGrid_Minify_css();
			$css->serve_file($_GET['css']);		
			exit(); // stop the view from being displayed
			break;
		}
		// if the request is for json api then server json api.
		if ($this->_getParam('json','false')!='false') {

			// Create a new instance of a JSON webservice service using our source table and grid configuration.
			$server = new PHPSlickGrid_JSON($this->table);
			// Expose the JSON database table service trough this action.
			$server->handle();
		
			exit(); // stop the view from being displayed
			break;
		}
		//******************************************************************
	}
	
	
	
}