<?php

namespace controller\index;

use wsc\controller\controller_abstract;
use wsc\functions\tools\Tools;
/**
 *
 * @author Michi
 *        
 */
class index extends controller_abstract
{
	

	public function default_action() 
	{
		Tools::internalRedirect("error", "notfound", array("next" => urlencode($_SERVER['QUERY_STRING'])));
	}
}

?>