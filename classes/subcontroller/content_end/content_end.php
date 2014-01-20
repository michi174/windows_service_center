<?php

namespace subcontroller\content_end;

use wsc\controller\Subcontroller_abstract;
use wsc\view\View_template;

/**
 *
 * @author Michi
 *        
 */
class content_end extends Subcontroller_abstract 
{
	public function runAfterMain()
	{
		$view	= new View_template(true);
		
		return $view;
	}
}

?>