<?php

namespace subcontroller\footer;

use wsc\controller\Subcontroller_abstract;

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
		echo "Ich bin der Footer und ich beinhalte den schliessenden body und html Tag.";
	}
}

?>