<?php

namespace controller\acl_test;
use wsc\controller\controller_abstract;
use wsc\application\Application;


class acl_test extends controller_abstract
{
	public function view_action()
	{
		$view	= $this->createView();
		$view->assign("application", Application::getInstance());
		$view->assign("acl", Application::getInstance()->load("acl"));
		
	}
	
	public function default_action() 
	{
		
	}
}
?>