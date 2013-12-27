<?php

namespace controller\acl_test;
use wsc\controller\controller_abstract;

class acl_test extends controller_abstract
{
	public function view_action()
	{
		//Hier werden Daten vom Model geladen und in die View Schicht übermittelt.
	}
	
	public function detailview_action()
	{
		//wenn eine ID bearbeitet werden muss, wird diese nicht über einen Parameter in der Action übergeben.
		//Diese muss direkt in der Action aus dem Requestobjekt geholt werden.
		//Beispiel:
		//-----------------------------------------------------------------------------------------------------
		//if($this->request->issetGet("id"))
		//{
		//	new model_acltest(get("id"));
		//}
		//-----------------------------------------------------------------------------------------------------
	}
	
	public function default_action() 
	{
		parent::default_action();
	}
}

?>