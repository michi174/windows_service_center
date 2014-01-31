<?php

namespace controller\error;

use wsc\controller\controller_abstract;
use wsc\systemnotification\SystemNotification;
use wsc\view\renderer\Tpl;
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
		$view		= $this->createView();
		$view->setRenderer(new Tpl());
		
		$error		= new SystemNotification();
		
		$error->addMessage("Keine Berechtigung um diese Seite anzuzeigen.");
		$content	= $error->printMessage(true);
		
		$view->add($content);
	}
	
	/**
	 * (non-PHPdoc)
	 *
	 * @see \wsc\controller\controller_abstract::default_action()
	 *
	 */
	public function default_action() 
	{
		$view	= $this->createView();
		
		if(!empty($_SERVER['HTTP_REFERER']))
		{
			Application::getInstance()->load("debugger")->log("Es gibt einen kaputten Link: ". $_SERVER['HTTP_REFERER']);
		}
	}
}

?>