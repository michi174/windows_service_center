<?php

namespace subcontroller\head;

use wsc\controller\Subcontroller_abstract;

/**
 *
 * @author Michi
 *        
 */
class head extends Subcontroller_abstract 
{
	public function runBeforeMain()
	{
		//TODO: View des Heads erzeugen.
		echo "Ich bin der Head. Ich beinhalte den oeffnenden HTML Tag, den Head Tag und die CSS Inkludes sowie die MetaTags und header.<br />";
	}
}

?>