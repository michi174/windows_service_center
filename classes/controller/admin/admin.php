<?php

namespace controller\admin;

use wsc\controller\controller_abstract;
use wsc\view\View_template;
use wsc\functions\tools\Tools;

/**
 *
 * @author Michi
 *        
 */
class admin extends controller_abstract 
{
	public function view_action()
	{
		$view	= new View_template();
		$view->display();
	}
	
	/**
	 * (non-PHPdoc)
	 *
	 * @see \wsc\controller\controller_abstract::default_action()
	 *
	 */
	public function default_action() 
	{
		Tools::internalRedirect("admin", "view");	
	}
}
?>