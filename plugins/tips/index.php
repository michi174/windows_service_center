<?php
$notify	= new SystemNotification();
$db		= new Database();
$output	= new Template($db);
$bbcode	= new BBCode();

if(!empty($_REQUEST['id']) && !empty($_REQUEST['action']))
{
	$parent_id	= $_REQUEST['id'];
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
								cms_boards.title
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
								userdata.firstname, userdata.lastname, userdata.username,
								cms_posts.text
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
							JOIN
								cms_posts
							ON 
								cms_posts.parent = cms_topics.id			
							WHERE
								cms_topics.parent = " . $parent_id . "
							GROUP BY
								cms_posts.parent";
			
			$topic_vars	= array(	"ID" 					=> "tid",
									"TITLE"					=> "title",
									"SUBTITLE"				=> "subtitle",
									"TEXT"					=> "text",
									"FIRSTNAME"				=> "firstname",
									"LASTNAME"				=> "lastname",
									"USERNAME"				=> "username",
									"DATE"					=> "date",
									"KLICKS"				=> "clicks",
									"AVATAR_FILENAME" 		=> "filename",
									"AVATAR_FILEEXTENSION" 	=> "fileextension"
									
								);
			$output->assignDatarow("TOPIC", $sql_topics, $topic_vars);
			$output->assignFunction("TOPIC.DATE", 'strftime("%d. %B %Y um %H:%M", {var})');
			$output->assignFunction("TOPIC.TEXT", 'limitStringbyWords($this->bbcode->parseText("{var}", false, true, false), 45)');
			
			
		break;
			
		case "detailview":
			$output->addTemplate("detailview.html");
			
			$sql_detail	= "	SELECT 
								cms_posts.*,
								cms_topics.subtitle, cms_topics.title , cms_topics.source,
								userdata.firstname, userdata.lastname, userdata.username
							FROM 
								cms_posts
							JOIN
								cms_topics
							ON
								cms_posts.parent	= cms_topics.id
							JOIN
								userdata
							ON 
								cms_topics.author	= userdata.id
							WHERE 
								cms_posts.id = " . $parent_id;
			
			$res_detail	= $db->query($sql_detail) or die($db->error);
			$row_detail	= $res_detail->fetch_object();

			$output->assign("TITLE", $row_detail->title);
			$output->assign("SUBTITLE", $row_detail->subtitle);
			$output->assign("DATE", strftime("%d. %B %Y um %H:%M", $row_detail->date));
			$output->assign("TEXT", $bbcode->parseText($row_detail->text, true, true));
			$output->assign("SOURCE", $bbcode->parseText($row_detail->source, true, true));
			$output->assign("AUTHOR", $row_detail->username);
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