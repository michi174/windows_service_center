<?php

namespace subcontroller\console;

use wsc\controller\Subcontroller_abstract;
use wsc\view\View_template;
use wsc\application\Application;

/**
 *
 * @author Michi
 *        
 */
class Console extends Subcontroller_abstract
{
	public function runAfterMain()
	{
		$view	= new View_template(true);
		
		$debugger	= Application::getInstance()->load("Debugger");
		$messages	= $debugger->getLog();
		
		$view->assignVar("LOG_MSGS", $messages);
		$view->assignVar("NUM_LOG_ENTRIES", count($messages));

		return $view;
	}
}

?>