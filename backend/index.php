<?php
use wsc\systemnotification\SystemNotification;
$tpl	= new wsc\template\Template();
$acl	= new wsc\acl\Acl();

if($acl->hasPermission($user, "backend", "view"))
{
	$tpl->setTemplateDir("template/win8_style/templates/backend");
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