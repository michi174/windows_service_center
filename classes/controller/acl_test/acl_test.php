<?php

namespace controller\acl_test;
use wsc\controller\controller_abstract;
use wsc\application\Application;
use wsc\view\View_php;

class acl_test extends controller_abstract
{
	public function view_action()
	{
		$this->view	= new View_php();
		
		$application	= Application::getInstance();
		$acl			= $this->application->load("acl");
		
		$this->view->assign("application", $application);
		$this->view->assign("acl", $acl);

		return $this->view;		
	}
	
	public function default_action() 
	{
		
	}
}
?>