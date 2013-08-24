<?php
$acl	= new wsc\acl\Acl;
$tpl	= new wsc\template\Template;

$ressources	= $acl->getAllResources();

foreach ($ressources as $row)
{
	foreach($row as $title => $value)
	{
		$titles[]	= $title;
	}
	break;
}

$tpl->setTemplateDir("test/");
$tpl->addTemplate("acl_test.html");

$tpl->assign("rsc_title", $titles);
$tpl->assign("ressources", $ressources);

$tpl->display();

?>