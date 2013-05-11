<?php
/*Hier werden alle Formulare für die Plugins
  "forum, tips, artikel, news,... (cms)" enthalten sein.*/

$error		= new SystemNotification("error");
$warning	= new SystemNotification("warning");
$info		= new SystemNotification("information");
$success	= new SystemNotification("success");
$show_form	= true;

switch($formular)
{
	case "category":

	$area			= NULL;
	$title			= NULL;
	$description	= NULL;
	$picture		= NULL;
	$intern			= NULL;

	switch($_REQUEST['action'])
	{
		case "new":
		
		if(isset($_POST['save_x']))
		{
			//Hier werden den oben deklarierten Variablen Werte zugewiesen.

			//$checkform	= checkForm($type="not_empty_bzw._empty", $felder); //als Array die Felder eingeben, die nicht leer sein dürfen.
			
			if($checkform === true)
			{
				$sql	= ""; //SQL-INSERT
				$res	= mysql_query($sql) or die(mysql_error());
				
				if($res == true)
				{
					$success->addMessage("Erfolgsmeldung");
					$show_form	= false;
				}
				else
				{
					$error->addMessage("Fehlermeldung");
				}
			}
			else
			{
				foreach($checkform as $errors)
				{
					$error->addMessage($errors);
				}
			}		
		}
		case "update":
		case "delete":
	}

	$formular	 = "<input name=\"area\" value=\"" . $area . "\">";
	$formular	.= "<input name=\"title\" value=\"" . $title . "\">";
	$formular	.= "<textarea name=\"description\"> " . $description . "</textarea>";
	$formular	.= "<input type=\"file\" name=\"picture\">";
	$formular	.= "<input type=\"checkbox\" name=\"intern\">";

	case "board":
	case "topic":
	case "post":
	
	

}

if($show_form === true)
{
	echo $formular;
}
?>
