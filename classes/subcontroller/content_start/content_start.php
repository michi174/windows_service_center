<?php

namespace subcontroller\content_start;

use wsc\controller\Subcontroller_abstract;
use wsc\view\View_template;
use wsc\application\Application;

/**
 *
 * @author Michi
 *        
 */
class content_start extends Subcontroller_abstract 
{
	public function runBeforeMain()
	{
		$view	= new View_template(true);		
		return $view;
	}
}

?>