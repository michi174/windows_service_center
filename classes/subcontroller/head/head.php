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
		
	}
	public function runBeforeMain()
	{
		$view	= new View_template(true);
		return $view;
	}
}

?>