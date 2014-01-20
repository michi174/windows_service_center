<?php
use wsc\systemnotification\SystemNotification;

$error	= new SystemNotification("error");

$error->addMessage("Es ist ein unerwarteter Fehler aufgetreten. Bitte versuchen Sie Ihre durchgef&uuml;hrte Aktion zu wiederholen.");
$error->addButton("javascript:history.back()", "&laquo; Zur&uuml;ck");
$error->printMessage();
?>