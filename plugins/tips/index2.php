<?php

$notify		= new SystemNotification("information");
$db_notify	= new SystemNotification("error");
$db			= new Database();

$parent_id	= $_REQUEST['parent'];

switch ($_REQUEST['action'])
{
	case "boards":
			
		$categories	= new CMS("categories", "area", $parent_id, "boards");
		$categories->printData();

		break;
		
	case "overview";
	{
		
	}
			break;
		
	case "detailview":
		break;
}
$db_notify->printMessage();
$notify->printMessage();

?>