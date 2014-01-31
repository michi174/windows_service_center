<?php
use wsc\systemnotification\SystemNotification;

$error	= new SystemNotification("warning");

$error->addMessage("404 - Die von ihnen angeforderte Seite wurde nicht gefunden.");
$error->addButton("javascript:history.back()", "&laquo; Zur&uuml;ck");
$error->printMessage();
?>