<?php
$error	= new SystemNotification("error");
$error->addMessage("<strong>Oops! Es ist ein Fehler aufgetreten.<br />404 - Seite nicht gefunden.</strong><br /><br /> Die angeforderte Seite wurde nicht gefunden.");
$error->addButton("javascript:history.back()", "&laquo; Zur&uuml;ck");
$error->printMessage();
?>