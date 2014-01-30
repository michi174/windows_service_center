<?php

namespace controller\admin;

use wsc\controller\controller_abstract;
use wsc\functions\tools\Tools;
use wsc\view\renderer\Tpl;

/**
 *
 * @author Michi
 *        
 */
class admin extends controller_abstract 
{
	public function view_action()
	{
		$view	= $this->createView();
		$view->setRenderer(new Tpl());
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