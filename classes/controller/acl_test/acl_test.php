<?php

namespace controller\acl_test;
use wsc\controller\controller_abstract;
use wsc\view\View_html;

class acl_test extends controller_abstract
{
	public function view_action()
	{
		$view	= new View_html();	

		ob_start();
		
		include 'test/acl_test.php';
		
		$content	= ob_get_clean();
		
		$view->add($content);
		$view->display();
		
		ob_end_clean();
		
	}
	
	public function default_action() 
	{
		
	}
}

?>