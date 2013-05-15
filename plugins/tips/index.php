<?php
$notify	= new SystemNotification();
$db		= new Database();
$output	= new Template($db);

if(!empty($_REQUEST['parent']) && !empty($_REQUEST['action']))
{
	$parent_id	= $_REQUEST['parent'];
	$output->setTemplateDir(__DIR__ . "/templates");
	
	switch ($_REQUEST['action'])
	{
		case "boards":
			
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
		case "topics";
			$output->addTemplate("topics.html");
			
			$sql_head	= "	SELECT
								* 
							FROM 
								cms_boards 
							WHERE 
								id = ". $parent_id;
			$res_head	= $db->query($sql_head) or die($db->error);
			$row_head	= $res_head->fetch_assoc();
			
			$output->assign("BOARD_NAME", $row_head['title']);
			
			$sql_topics	= "	SELECT
								cms_topics.*, cms_topics.id as tid,
								uploaded_files.id as filename, uploaded_files.extension as fileextension,
								userdata.firstname, userdata.lastname
							FROM 
								cms_topics
							JOIN  
								uploaded_files
							ON
								uploaded_files.id = cms_topics.picture
							JOIN
								userdata
							ON
								userdata.id = cms_topics.author				
							WHERE
								cms_topics.parent = " . $parent_id;
			
			$topic_vars	= array(	"ID" 					=> "tid",
									"TITLE"					=> "title",
									"SUBTITLE"				=> "subtitle",
									"FIRSTNAME"				=> "firstname",
									"LASTNAME"				=> "lastname",
									"DATE"					=> "date",
									"KLICKS"				=> "clicks",
									"AVATAR_FILENAME" 		=> "filename",
									"AVATAR_FILEEXTENSION" 	=> "fileextension"
									
								);
			$output->assignDatarow("TOPIC", $sql_topics, $topic_vars);
			$output->assignFunction("TOPIC.DATE", 'strftime("%d. %B %Y um %H:%M", {var})');
			
			
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