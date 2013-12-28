<?php

namespace subcontroller\header;

use wsc\controller\Subcontroller_abstract;

/**
 *
 * @author Michi
 *        
 */
class header extends Subcontroller_abstract 
{
	public function runBeforeMain()
	{
		//View des headers erzeugen.
		echo "ich bin der Header und beinhalte das Menue.<br />";
	}
}

?>