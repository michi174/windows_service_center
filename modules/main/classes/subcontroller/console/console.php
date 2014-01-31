<?php

namespace subcontroller\console;

use wsc\controller\Subcontroller_abstract;
use wsc\application\Application;
use wsc\view\renderer\Tpl;
use wsc\view\Html;

/**
 *
 * @author Michi
 *        
 */
class Console extends Subcontroller_abstract
{
	public function runAfterMain()
	{
		$view	= $this->createView(new Html());
		$view->setRenderer(new Tpl());
		
		$debugger	= Application::getInstance()->load("Debugger");
		$messages	= $debugger->getLog();
		
		$view->renderer->assignVar("LOG_MSGS", $messages);
		$view->renderer->assignVar("NUM_LOG_ENTRIES", count($messages));

		return $view;
	}
}

?>