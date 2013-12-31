<?php

namespace subcontroller\head;

use wsc\controller\Subcontroller_abstract;
use wsc\view\View_template;

/**
 *
 * @author Michi
 *        
 */
class head extends Subcontroller_abstract 
{
	private function buildHead()
	{
		$view	= new View_template($this->getSubControllerName($this));
		$view->display();
	}
	public function runBeforeMain()
	{
		//TODO: View des Heads erzeugen.
		$this->buildHead();
	}
}

?>