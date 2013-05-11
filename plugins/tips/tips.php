<?php 
$notify	= new SystemNotification();
$notify->setNotificationType("success");

//Kategorien auflisten
switch ($_REQUEST['action'])
{
	case "categories":
	//HTML Ausgabe...
?>
<div class="headline">Tipps & Tricks</div>
<div class="new_articles">
	<div class="new_article_pics"><img src="" height="120" width="213" /></div>
	<div class="new_article_pics"><img src="" height="120" width="213" /></div>	
	<div class="new_article_pics"><img src="" height="120" width="213" /></div>
	<div class="new_article_pics"><img src="" height="120" width="213" /></div>
	<div class="clearing"></div>
</div>
<?php 
		$sql_tt_cat	= "SELECT * FROM categories WHERE category = 'tips' ORDER BY name ASC";
		$res_tt_cat	= mysql_query($sql_tt_cat) or die(mysql_error());
		$num_tt_cat	= mysql_num_rows($res_tt_cat);
		
		//Wenn Kategorien vorhanden sind 
		if($num_tt_cat>0)
		{
			
			//Kategorien ausgeben
			while(($row_tt_cat = mysql_fetch_array($res_tt_cat)) == true)
			{
				$sql_tt_po	= "SELECT * FROM tips WHERE cat = '".$row_tt_cat['id']."'";
				$res_tt_po	= mysql_query($sql_tt_po) or die(mysql_error());
				$num_tt_po	= mysql_num_rows($res_tt_po);
				
				$bbcode		= new BBCode(true, false, false);
	
				//HTML Ausgabe...
?>
<div class="categories">
	<div class="categorie_pics"><img src="<?php echo "files/uploaded_files/".$row_tt_cat['picture'] ?>" alt="cat_pic_<?php echo $row_tt_cat['id'] ?>"/></div>
	<div class="categorie_text">
		<span class="categorie_hl"><?php echo "<a href=\"?site=tips&action=overview&cat=".$row_tt_cat['id']."\">".$row_tt_cat['name']."</a>"; ?></span>
		<br><?php echo $bbcode->parseText($row_tt_cat['description']) ?></div>
	<div class="clearing"></div>
	<div class="categorie_status"><?php echo $num_tt_po ?>&nbsp;Tipps in dieser Kategorie</div>
</div>
<?php
			} //Ende While
		} //Ende If
	
		//Wenn keine Kategorien vorhanden sind.
		else
		{
			$notify->setNotificationType("information");
			$notify->addMessage("Es sind keine Kategorien vorhanden.");
			if($permission['tips'] == 1)
			{
				$notify->addButton("?site=addcat&action=new", "Eine Kategorie anlegen");
			}
		}
		break;
	case "overview":
		$sql_tt_cat	= "SELECT * FROM categories WHERE id = " . $_GET['cat'];
		$res_tt_cat	= mysql_query($sql_tt_cat) or die(__FILE__.__FILE__.mysql_error());
		$row_tt_cat	= mysql_fetch_array($res_tt_cat);
?>
<div class="headline">
<?php echo $row_tt_cat['name'] ?>
</div>
<?php
		//Wenn start einen Wert hat, sonst start=0
		$start	= (isset($_GET['start'])) ? $_GET['start'] : 0;
	
		//Neue Blätterfunktion initialisieren
		$pagescroll	= new Pagescroll(MAX_TIPS_PER_PAGE, $start);
		$pagescroll->getNumberOfPosts("tips", "cat = '" . $_GET['cat'] . "'");
		
		$linkformat	= $pagescroll->setLinkFormat("?site=tips&action=overview&cat=" . $_GET['cat'], "pagescroll_link", "pagescroll_link_active");
		//Ausgabe Blätterfunktion
		try
		{
			$postlimit	= $pagescroll->getQueryLimit();
			
			$links	= $pagescroll->getFirstPage();
			$links	.=$pagescroll->getBackPage();
			$links	.=@implode("", $pagescroll->getPageLinks());
			$links	.=$pagescroll->getNextPage();
			$links	.=$pagescroll->getLastPage();
		}
		catch (Exception $error)
		{
			echo $error->getMessage();
		}
			echo "<div class=\"pagescroll_link_box\">";
			echo $links;
			echo "</div>";
		
		$sql_tt_po	= "SELECT * FROM tips WHERE cat = '" . $_GET['cat'] . "'" . $postlimit. "";
		$res_tt_po	= mysql_query($sql_tt_po) or die(__FILE__.__FILE__.mysql_error());
		$num_tt_po	= mysql_num_rows($res_tt_po);
		
		//Wenn Tipps vorhanden sind...	
		if($num_tt_po > 0)
		{
			//...Tips ausgeben
			while(($row_tt_po = mysql_fetch_assoc($res_tt_po)) == true)
			{
				$post_userdata	= Login::getUserData($row_tt_po['userid']);
				$post_imagedata	= Upload::getFileData($row_tt_po['picture']);
				$bbcode			= new BBCode(false, false, false);
				
				//HTML Ausgabe...
?>

<div class="categories">
	<div class="categorie_pics"><img src="files/uploaded_files/<?php echo $post_imagedata['id'] . $post_imagedata['extension'] ?>" alt="picture" /></div>
	<div class="categorie_text">
		<strong><?php echo $row_tt_po['header'] ?></strong><br />
		<span style="font-size:16px"><?php echo "<a href=\"?site=tips&action=detailview&id=".$row_tt_po['id']."\">".$row_tt_po['title']."</a>"; ?></span><br /><br />
		<?php echo $bbcode->parseText(substr($row_tt_po['text'], 0, 150)) . "&hellip;"; ?>
	</div>
	<div class="clearing"></div>
	<div class="categorie_status"><?php echo $post_userdata['firstname'] . "&nbsp;" .$post_userdata['lastname'] . " am " . date("d.m.Y \\u\\m H:i", $row_tt_po['date']) . " | " . $row_tt_po['clicks'] . " Klicks | 0 Kommentare"; ?> </div>
</div><br />
<?php
			} //Ende While
			echo "<div class=\"pagescroll_link_box\">";
			echo $links;
			echo "</div>";
			
		} //Ende If Tipps vorhanden
		//sonst Fehlermeldung
		else
		{
			$notify->setNotificationType("information");
			$notify->addMessage("In dieser Kategorie sind leider noch keine Eintr&auml;ge vorhanden.");
		}
		break;
	case "detailview":

		$sql_tt_po	= "SELECT * FROM tips WHERE id = " . $_GET['id'];
		$res_tt_po	= mysql_query($sql_tt_po) or die(mysql_error());
		$num_tt_po	= mysql_num_rows($res_tt_po);
		
		//Wenn angeforderter Post gefunden wurde
		if ($num_tt_po > 0)
		{
			$row_tt_po	= mysql_fetch_assoc($res_tt_po);
			$post_autor	= Login::getUserData($row_tt_po['userid']);
			
			$sql_upd	= "UPDATE tips SET clicks = clicks+1 WHERE id = " . $_REQUEST['id'];
			$res_upd	= mysql_query($sql_upd);
			
			$bbcode	= new BBCode(true, true, false);
			
			//HTML Ausgabe...
?>
<div class="post_dateline"><?php echo "Verfasst am ". date("d.m.Y, H:i" , $row_tt_po['date']); ?></div>
<hr width="95%" />
<div class="post_body">
	<div class="post_header"><?php echo $row_tt_po['header']?></div>
	<div class="post_headline"><?php echo $row_tt_po['title']?></div>
	<div><?php echo $bbcode->parseText($row_tt_po['text'])?></div>
</div>
<div class="post_bar">
	<ul>
		<li class="facebook"><a href="#">Facebook</a></li>
		<li class="twitter"><a href="#">Twitter</a></li>
		<li class="twitter"><a href="#">Google+</a></li>
		<?php
			if ($row_tt_po['comments'] == 1)
			{
				echo "<li class=\"kommentare\"><a href=\"#\">0 Kommentare &raquo;</a></li>";
			}
		?>
	</ul>
</div>
<div class="post_infos">
	<?php 
		echo 	"Autor: <a href=\"#\">" . $post_autor['firstname'] . " " . $post_autor['lastname'] . "</a>
				| Quelle: " . $bbcode->parseText($row_tt_po['source']); 
	?>
</div>
<div class="post_extension">
<strong>Verwandte Tipps</strong>

</div>
<?php 
		} //Ende if Einträge vorhanden
		else 
		{
			$notify->setNotificationType("information");
			$notify->addMessage("Der gew&uuml;nschte Beitrag wurde leider nicht gefunden.");
		}
		break;
default:
	$notify->setNotificationType("warning");
	$notify->addMessage("Die gew&uuml;nschte Seite konnte nicht geladen werden.");
} 

	switch ($notify->getNotificationType())
	{
		case "information":
			$notify->addButton("javascript:history.back()", "&laquo; Zur&uuml;ck");
			break;
	}
	
	$notify->printMessage();

?>