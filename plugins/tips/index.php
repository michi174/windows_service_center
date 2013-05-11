<?php
$notify	= new SystemNotification();
$db		= new Database();
$output	= new Template($db);

if(!empty($_REQUEST['parent']) && !empty($_REQUEST['action']))
{
	$parent_id	= $_REQUEST['parent'];
	
	switch ($_REQUEST['action'])
	{
		case "boards":
			
			$output->setTemplateDir(__DIR__ . "/templates");
			$output->addTemplate("boards.html");
			
			$sql_categories	= "	SELECT
									*
								FROM 
									cms_categories
								WHERE
									area = " . $parent_id;
			
			
			$category_vars	= array(	"NAME" 		=> "title",
										"DISPLAY"	=> "collapse",
										"ID"		=> "id"
									);
			
			$output->assignDatarow("CATEGORY", $sql_categories, $category_vars);
			
			$sql_boards		= "	SELECT 
									cms_boards.title, cms_boards.description, cms_boards.id as bid,
									uploaded_files.id as filename, uploaded_files.extension as fileextension
								FROM 
									cms_boards 
								JOIN  
									uploaded_files
								ON
									uploaded_files.id = cms_boards.picture
								WHERE 
									cms_boards.parent = {CATEGORY.ID}
								";
			
			$boards_vars	= array(	"ID" 					=> "bid",
										"DESCRIPTION" 			=> "description",
										"NAME" 					=> "title",
										"AVATAR_FILENAME" 		=> "filename",
										"AVATAR_FILEEXTENSION" 	=> "fileextension"
									);
			
			$output->assignSubrow("BOARDS", "CATEGORY", $sql_boards, $boards_vars);
			break;
		case "overview";
		break;
			
		case "detailview":
		break;
	}
	$output->display();
}
else
{
	$notify->addMessage("Die angeforterte Aktion konnte nicht ausgef&uuml;hrt werden.");
}
$notify->printMessage();
?>