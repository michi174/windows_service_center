<?php

$notify	= new SystemNotification();
$notify->setNotificationType("success");

if(isset($_SESSION['loggedIn']) === true && $permission['admin'] == 1)
{
	$show_form	= true;
	if(isset($_POST['save_x']))
	{
		$catname	= @$_REQUEST['catname'];
		$cattype	= @$_REQUEST['cattype'];
		$description= @$_REQUEST['description'];
		$picture	= @$_REQUEST['url'];
		$url		= @$_REQUEST['url'];
		$intern		= (@$_REQUEST['intern'] != NULL) ? @$_REQUEST['intern'] : 0;
		
		if(empty($_REQUEST['catname']))
		{
			$notify->setNotificationType("error");
			$notify->addMessage("Bitte einen Namen f&uuml;r die Kategorie eingeben.");
		}
		if(empty($_REQUEST['cattype']))
		{
			$notify->setNotificationType("error");
			$notify->addMessage("Bitte eine Kategorie ausw&auml;hlen.");
		}
		if($_REQUEST['cattype'] != 'gallerie' && empty($_REQUEST['description']))
		{
			$notify->setNotificationType("error");
			$notify->addMessage("Bitte eine Beschreibung eingeben.");
		}
		if($_REQUEST['cattype'] != 'forum' && $_REQUEST['cattype'] != 'gallerie' && empty($_FILES['picture']['tmp_name']) && empty($_REQUEST['url']))
		{				
			$notify->setNotificationType("error");
			$notify->addMessage("Bitte ein Bild ausw&auml;hlen bzw. hochladen.");
		}
		if($_REQUEST['cattype'] != 'forum' && $_REQUEST['cattype'] != 'gallerie' && !empty($_FILES['picture']['tmp_name']) && !empty($_REQUEST['url']))
		{
			$notify->setNotificationType("error");
			$notify->addMessage("Es k&ouml;nnen nicht zwei Bilder f&uuml;r eine Kategorie verwendet werden.");
		}
		if(($_REQUEST['cattype'] == 'forum' || $_REQUEST['cattype'] == 'gallerie') && (!empty($_FILES['picture']['tmp_name']) || !empty($_REQUEST['url'])))
		{
			$notify->setNotificationType("error");
			$notify->addMessage("F&uuml;r diesen Bereich kann kein Bild verwendet werden.");
		}

		if(!empty($_FILES['picture']['tmp_name']) && empty($_REQUEST['url']) && $_REQUEST['cattype'] != 'forum' && $_REQUEST['cattype'] != 'gallerie')
		{
			
			$upload			= new Upload($_FILES['picture'], "pic", false);
			$upload->uploadFile();
			$upload_done	= $upload->getUploadStatus();
			
			
			
			if($upload_done['no_error'] !== true)
			{
				$notify->setNotificationType("error");
				$notify->addMessage($upload_done["error_msg"]);
			}
			else
			{
				$picture	= $upload_done["filename"];
			}
			
		}				

		if($notify->getNotificationType() == "success")
		{
			$show_form		= false;
			$sql	= "INSERT INTO categories
						(category, name, description, picture, intern)
					   VALUES
					   	('".$cattype."', '".mysql_real_escape_string($catname)."', '".mysql_real_escape_string($description)."', '".$picture."', '".$intern."')";
			$res	= mysql_query($sql) or $notify->setNotificationType("warning").$notify->addMessage("SQL-Fehler in Datei: " . __FILE__ . "<br /><br />" . mysql_error());
			
			if($res == false) 
			{
				$notify->setNotificationType("error");
				$notify->addMessage("Der Datensatz konnte nicht gespeichert werden!");
			}
			else
			{
				$notify->setNotificationType("success");
				$notify->addMessage("Das war's schon - Die Kategorie wurde erfolgreich angelegt.");
				$notify->addButton("?site=tips", "Weiter &raquo;", "right");
				$notify->addButton("?site=addcat&action=new", "Noch eine Kategorie anlegen", "right");
			}
		}
		switch ($notify->getNotificationType())
		{
			case "error":
					
				$notify->printMessage();
				break;
					
			case "warning":
					
				$notify->printMessage();
				break;
					
			case "information":
				break;
					
			case "success":
				$notify->printMessage();
				$show_form	= false;
				break;
		}
	}
	if($show_form != false)
	{
?>
	<form action="?site=addcat" method="post" name="addcat" id="addcat" enctype="multipart/form-data">
	<table class="formular">
		<tr>
			<td colspan="2" class="headline">
				Neue Kategorie
				<hr class="body_line" width="800" align="left" />
			</td>
		</tr>
		<tr>
			<td width="30%">Name der Kategorie</td>
			<td width="70%"><input name="catname" type="text" style="width:400px;" value="<?php echo @$catname; ?>" required="required" /></td>
		</tr>
		<tr>
			<td>F&uuml;r welchen Bereich wird <br />die Kat. angelegt?</td>
			<td>
				<select name="cattype" size="5" style="width:300px;" required="required" onchange="
					if(this.form.cattype.options[this.form.cattype.selectedIndex].value=='gallerie')
					{
						this.form.picture.disabled='disabled';
						this.form.description.disabled='disabled';
						this.form.url.disabled='disabled';
						changeContent('infoline', '<hr class=\'body_line\' width=\'800\' align=\'left\' /><ul><li>Keine Eingabe f&uuml;r <strong>&quot;Beschreibung&quot;</strong> erforderlich.</li><li>Keine Eingabe für <strong>&quot;Kategoriebild&quot;</strong> erforderlich.</li></ul>');
					}
					else
					{
						this.form.picture.disabled='';
						this.form.description.disabled='';
						this.form.url.disabled='';
						changeContent('infoline', '');
						changeContent('perm_headline','');
						changeContent('perm_para','');
					}
					if(this.form.cattype.options[this.form.cattype.selectedIndex].value=='forum')
					{
						this.form.picture.disabled='disabled';
						this.form.url.disabled='disabled';
						changeContent('infoline', '<hr class=\'body_line\' width=\'800\' align=\'left\'><ul><li>Keine Eingabe f&uuml;r <strong>&quot;Kategoriebild&quot;</strong> erforderlich.</li></ul>');	
						changeContent('perm_headline', 'Zugriffsbeschr&auml;nkung');
						changeContent('perm_para', '<input name=\'intern\' type=\'radio\' value=\'1\' />Ja<input name=\'intern\' type=\'radio\' value=\'0\' checked=\'checked\' />Nein');	
					}"
				>
					<option value="forum" 		<?php if (@$cattype=="forum") {echo "selected=\"selected\"";} ?>		>Forum</option>
					<option value="tips" 		<?php if (@$cattype=="tips") echo "selected=\"selected\""; ?>		>Tipps &amp; Tricks</option>
					<option value="artikel" 	<?php if (@$cattype=="artikel") echo "selected=\"selected\""; ?>	>Artikel</option>
					<option value="downloads"	<?php if (@$cattype=="downloads") echo "selected=\"selected\""; ?>	>Downloads</option>
					<option value="gallerie" 	<?php if (@$cattype=="gallerie") echo "selected=\"selected\""; ?>	>Gallerie (Neues Album)</option>
				</select>
			</td>
		</tr>
		<tr>
			<td valign="middle">Beschreibung (Infotext)</td>
			<td><textarea name="description" style="width:400px;" rows="10" id="description"><?php echo @$description; ?></textarea>
			</td>
		</tr>
		<tr>
			<td valign="middle">Kategoriebild auswählen <br />bzw. hochladen</td>
			<td><input name="picture" style="width:250px;" type="file" /></td>
		</tr>
		<tr>
			<td valign="middle">(URL zum Bild)</td>
			<td><input name="url" style="width:400px;" type="text" value="<?php echo @$url; ?>" /></td>
		</tr>
		<tr>
			<td valign="middle" id="perm_headline"></td>
			<td id="perm_para"></td>
		</tr>
			<tr>
			<td valign="top" colspan="2" id="infoline"></td>
		</tr>
		<tr>
			<td colspan="2" align="right" style="padding-right:75px;">
				<hr class="body_line" width="800" align="left" />
				<a href="#" onclick="showBox('msgbox'); showBox('darkbackground')"><img src="template/win8_style/grafics/form/delete.png" border="0" alt="verwerfen" title="Verwerfen" /></a>
				<input name="save_x" type="hidden" />
				<input name="save" type="image" src="template/win8_style/grafics/form/save.png" alt="speichern" title="Speichern" />
			</td>
		</tr>
	</table>
	<div id="msgbox">
		<span style="display:block; background-color:#6badf6; margin-top:-10px; margin-left:-5px; margin-right:-5px; height:20px; padding-top:5px; text-align:center;">Windows Service Center</span><hr />
		<img alt="warning" src="grafiken/warning.png" style="float:left; margin-top:-5px; margin-right:10px;"/><span style="float:left; margin-top:10px; display:block;">Achtung! Die Daten wurde noch nicht gespeichert.<br />M&ouml;chten Sie Ihre &Auml;nderungen an diesem Formular speichern?</span>
		<a href="javascript:document.addcat.submit()" name="save" class="button_box" style="margin-left:0px;">Speichern</a>
		<a href="?site=tips" class="button_box">Nicht speichern</a>
		<a href="#" class="button_box" onclick="hideBox('msgbox'); hideBox('darkbackground')">Abbrechen</a> 
	</div>
	</form>

<?php
	}
}
else
{
	$notify->setNotificationType("warning");
	$notify->addMessage("Sie haben keine Berechtigung, um diese Seite anzuzeigen.");
	$notify->addButton("javascript:history.back()", "&laquo; Zur&uuml;ck");
	$notify->printMessage();
}
?>