<?php
use wsc\systemnotification\SystemNotification;
$tpl	= new wsc\template\Template();
$acl	= new wsc\acl\Acl();

if($acl->hasPermission($user, "backend", "view"))
{
	$tpl->setTemplateDir($config->get("abs_project_path")."/template/win8_style/templates/");
	$tpl->addTemplate("index.html");
	
	$tpl->display();
}
else
{
	$error	= new wsc\systemnotification\SystemNotification("error");
	$error->addMessage("<h4>Zugriff verweigert!</h4><br />Sie haben keine Berechtigung, um diese Seite anzuzeigen.");
	$error->printMessage();
	
}

?>