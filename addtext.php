<?php
$categorie	= NULL;
$header		= NULL;
$title		= NULL;
$text		= NULL;
$picture	= NULL;
$keywords	= NULL;
$source		= NULL;
$comments	= NULL;

$show_form	= true;

$notify		= new SystemNotification("error");

if(isset($_POST['save_x']))
{
	$categorie	= $_POST['categorie'];
	$header		= $_POST['header'];
	$title		= $_POST['title'];
	$text		= $_POST['message'];
	$picture	= $_POST['picture'];
	$keywords	= $_POST['keywords'];
	$source		= $_POST['source'];
	$comments	= ($_POST['comments'] == true) ? 1 : 0 ;
	
	if(empty($header))
	{
		$notify->addMessage("Bitte Header eingeben!");
	}
	if(empty($title))
	{
		$notify->addMessage("Bitte Titel eingeben!");
	}
	if($categorie == 0)
	{
		$notify->addMessage("Bitte Kategorie ausw&auml;hlen!");
	}
	if(empty($text))
	{
		$notify->addMessage("Bitte Text eingeben!");
	}

	if($notify->getNotificationType() == "success")
	{
		$sql	= "	INSERT
							tips
							(
								userid,
								cat,
								ip,
								date,
								title,
								header,
								text,
								picture,
								keywords,
								source,
								comments,
								clicks
							)
						VALUES
							(
								'" . $_SESSION['userid'] . "',
								'" . $categorie . "',
								'" . $_SERVER['REMOTE_ADDR'] . "',
								'" . time() . "',
								'" . $title . "',
								'" . $header . "',
								'" . $text . "',
								'" . $picture . "',
								'" . $keywords . "',
								'" . $source . "',
								'" . $comments . "',
								'" . 0 . "'
							)";
			
		$res	= mysql_query($sql) or $notify->setNotificationType("warning").$notify->addMessage("SQL-Fehler in Datei: " . __FILE__ . "<br /><br />" . mysql_error());

		if($res == false)
		{
			$notify->addMessage("Der Datensatz konnte nicht gespeichert werden!");
		}
		else
		{
			$notify->addMessage("Das war's. Der Tipp wurde erfolgreich gespeichert.", "success");
			$notify->addButton("?site=tips&action=categories", "Weiter zu den Tipps &raquo;", "right");
			$show_form	= false;
		}	
	}

		$notify->printMessage();
}
?>

<?php 
if(isset($_SESSION['loggedIn']) === true && $permission['admin'] == 1)
{
	if ($show_form !== false)
	{
?>
<div class="formular">
<div class="post_headline">Beitrag verfassen</div>
<form action="#" method="post" name="post" id="addtext">
<div>
<input type="text" name="title" placeholder="Titel eingeben&hellip;" style="width:600px;" value="<?php echo $title ?>" />
<input type="text" name="header" placeholder="Header" style="width:600px;" value="<?php echo $header ?>"  />
<br />
<select name="categorie" class="categorie" style="margin-left:0px;">
	<option value="0">Kategorie ausw&auml;hlen</option>
<?php 
		$sql_tt_cat	= "SELECT * FROM areas";
		$res_tt_cat	= mysql_query($sql_tt_cat) or die("SQL-Fehler in Datei: " . __FILE__ . "<br /><br />" . mysql_error());
		$num_tt_cat	= mysql_num_rows($res_tt_cat);
			
		if($num_tt_cat > 0)
		{
			while(($row_tt_cat = mysql_fetch_assoc($res_tt_cat)) == true)
			{
				if($row_tt_cat['id'] == $categorie)
				{
					$selected	= "selected=\"selected\"";
				}
				echo "<option value=\"" . $row_tt_cat['id'] . "\" " . $selected . ">" . $row_tt_cat['display_name'] . "</option>";
			}
		}
		else
		{
			echo "<option value=\"0\">Keine Kategorien vorhanden</option>";
		}
		
?>
</select>
<br /><br />
<a href="#" class="pagescroll_link">Bild ausw&auml;hlen</a>
</div>
<br />
<div class="addtext_symbolbar">
	<ul class="selections">
		<li>
			<select name="test1" class="fontsize">
				<option value="1">Schriftgröße</option>
				<option value="1">Schriftgröße</option>
				<option value="1">Schriftgröße</option>
			</select>
		</li>
		<li>
			<select name="test2" class="fontcolor">
				<option value="1">Schriftfarbe</option>
				<option value="1">Schriftfarbe</option>
				<option value="1">Schriftfarbe</option>
			</select>
		</li>
		<li>
			<select name="test3" class="fontstyle">
				<option value="1">Schriftart</option>
				<option value="1">Schriftart</option>
				<option value="1">Schriftart</option>
			</select>
		</li>
	</ul>
	<ul class="textdecoration">
		<li><a href="#" class="bold" title="Fett">F</a></li>
		<li><a href="#" class="italic" title="Kursiv">K</a></li>
		<li><a href="#" class="underline" title="Unterstrichen">U</a></li>
	</ul>
	<ul class="textalign">
		<li><a href="#"><img src="template/win8_style/grafics/editor/Text-Left-Align.png" border="0" title="Linksb&uuml;ndig" /></a></li>
		<li><a href="#"><img src="template/win8_style/grafics/editor/Text-Center-Align.png" border="0" title="Zentriert" /></a></li>
		<li><a href="#"><img src="template/win8_style/grafics/editor/Text-Right-Align.png" border="0" title="Rechtsb&uuml;ndig" /></a></li>
		<li><a href="#"><img src="template/win8_style/grafics/editor/Text-Justify-Align.png" border="0" title="Blocksatz" /></a></li>
	</ul>
	<ul class="textalign">
		<li><a href="#"><img src="template/win8_style/grafics/editor/unsorted_list.png" border="0" title="Unsortierte Liste" /></a></li>
		<li><a href="#"><img src="template/win8_style/grafics/editor/sorted_list.png" border="0" title="Sortierte Liste" /></a></li>
	</ul>
	<ul class="insert">
		<li><a href="#"><img src="template/win8_style/grafics/editor/link.png" border="0" title="Link einf&uuml;gen" /></a></li>
		<li><a href="#"><img src="template/win8_style/grafics/editor/picture.png" border="0" title="Bild einf&uuml;gen" /></a></li>
		<li><a href="#" class="bold" title="Sonderzeichen einf&uuml;gen">&Omega;</a></li>
	</ul>
	<div class="clearing"></div>
</div>
<div><textarea name="message" placeholder="Text eingeben&hellip;" style="height:600px; width:880px;"><?php echo $text ?></textarea></div>
<div><textarea name="keywords" placeholder="Keywords" style="width:880px; height:50px;"><?php echo $keywords ?></textarea></div>
<div><input type="text" name="source" placeholder="Quelle" style="width:884px;" value="<?php echo $source ?>" /></div>
<div>
	<input type="checkbox" name="comments" checked="checked" /> Kommentare zulassen
</div>
<div style="margin-top:10px; text-align:right; width:884px;">
	<input type="hidden" name="picture" value="<?php echo $picture ?>" />
	<a href="#" onclick="showBox('msgbox'); showBox('darkbackground')"><img src="template/win8_style/grafics/form/delete.png" border="0" alt="verwerfen" title="Verwerfen" /></a>
	<a href="#" onclick="showBox('preview'); showBox('darkbackground')"><img src="template/win8_style/grafics/form/preview.png" border="0" alt="verwerfen" title="Vorschau" /></a>
	<input name="save" type="image" src="template/win8_style/grafics/form/save.png" alt="speichern" title="Speichern" />
</div>
</form>
</div>
<?php
	}//Endif showform
}//Endif permission == admin
else
{
	$notify->addMessage("Sie haben keine Berechtigung, um diese Seite anzuzeigen.", "warning");
	$notify->addButton("javascript:history.back()", "&laquo; Zur&uuml;ck");
	$notify->printMessage();
}
?>