<?php

namespace subcontroller\footer;

use wsc\controller\Subcontroller_abstract;
use wsc\view\View_template;

/**
 *
 * @author Michi
 *        
 */
class footer extends Subcontroller_abstract 
{
	public function runAfterMain()
	{
		//View des Footers erzeugen.
		$view	= new View_template($this->getSubControllerName($this));
		$view->display();
	}
}

?>