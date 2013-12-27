<?php

namespace subcontroller\main;

use wsc\controller\Subcontroller_abstract;

/**
 *
 * @author Michi
 *        
 */
class main extends Subcontroller_abstract 
{
	public function ichBinEineBeliebigeMethode()
	{
		//Mach was...
	}
	
	public function runBeforeMain()
	{
		echo "Ich bin vor dem MainController gelaufen.<br />";
	}
	
	public function runAfterMain()
	{
		echo "Ich bin nach dem MainController gelaufen.<br />";
	}
}

?>