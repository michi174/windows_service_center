<?php

namespace controller\error;

use wsc\controller\controller_abstract;
use wsc\view\View_php;
use wsc\systemnotification\SystemNotification;
use wsc\view\View_template;
use wsc\application\Application;

/**
 *
 * @author Michi
 *        
 */
class Error extends controller_abstract
{
	public function nopermission_action()
	{
		$view		= new View_template();
		$error		= new SystemNotification();
		
		$error->addMessage("Keine Berechtigung um diese Seite anzuzeigen.");
		$content	= $error->printMessage(true);
		
		$view->add($content);
		return $view;	
	}
	
	/**
	 * (non-PHPdoc)
	 *
	 * @see \wsc\controller\controller_abstract::default_action()
	 *
	 */
	public function default_action() 
	{
		$view	= new View_php();
		return $view;
	}
}

?>