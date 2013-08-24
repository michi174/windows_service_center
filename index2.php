<?php

session_start();

require_once 'lib/autoloader.func.php';
require_once 'admin/config.php';

$db		= new wsc\database\Database();
$plug	= new wsc\pluginmanager\PluginManager();

$forward_link	= $_SERVER['QUERY_STRING'];

$cookie 		= NULL;
$referer_site	= (isset($_REQUEST[DEFAULT_LINK])) ? $_REQUEST[DEFAULT_LINK] : NULL;

if(isset($_REQUEST['check_plugins']))
{
	$plug->checkPlugins();
}

$page_error	= NULL;
$plugins	= wsc\pluginmanager\PluginManager::getPlugins(false);

if(isset($_REQUEST['logout']))
{
	$userdata	= wsc\logout\Logout::logoutUser();
	$permission	= wsc\logout\Logout::unsetPermissions();
}

if(isset($_POST['login_x']) || (isset($_COOKIE['login']) && isset($_SESSION['loggedIn']) !== true))
{
	if(isset($_POST['login_x']))
	{
		$username	= $_POST['username'];
		$password	= $_POST['password'];
	}
	else 
	{
		$username	= NULL;
		$password	= NULL;
	}
	if(isset($_POST['save_login']))
	{
		$cookie	= (!is_null($_POST['save_login'])) ? true : false;
	}
	
	$login		= new wsc\login\Login($db, $username, $password, $cookie);
	
	try
	{
		$login->loginUser();
	}
	catch (wsc\login\exception\LoginErrorException $error)
	{
		$login_notification		= new wsc\systemnotification\SystemNotification("error");
		$login_notification->addMessage($error->getMessage());
	}
}
if(isset($_SESSION['loggedIn']) === true)
{
	$userdata	= wsc\login\Login::getUserData($_SESSION['userid']);
	$permission = wsc\login\Login::getUserPermission($_SESSION['userid']);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" href="template/win8_style/stylesheets/standard.css" type="text/css" />
    <link rel="stylesheet" href="template/win8_style/stylesheets/notifications.css" type="text/css" />
    <link rel="stylesheet" href="template/win8_style/stylesheets/editor.css" type="text/css" />
    <link rel="stylesheet" href="template/win8_style/stylesheets/livetiles.css" type="text/css" />
    <script type="text/javascript" src="js_functions.js"></script>
    <title>Windows Service Center - The Ultimate Servicecenter</title>
</head>
<body style="margin-top:0px;">
<div id="darkbackground"></div>
<div class="website">
<div class="box_header">
<!-- CSS Background Picture -->
</div>
<div class="menurow_top">
	<ul>
		<?php wsc\pluginmanager\PluginManager::getPlugins(true);
		?>
		<li class="search">
			<form method="post" id="search" action="#">
				<input type="text" placeholder="Suchbegriff eingeben..." style="width:230px; height:20px;" />
				<input type="image" src="grafiken/search.png" style="width:16px; height:16px;" />
			</form>
		</li>
	</ul>
</div>

<div class="box_menu_extension" id="infoDIV">
    <a href="?site=addcat">Kategorie hinzuf&uuml;gen</a>&nbsp;&middot;&nbsp;
    <a href="?site=addtext">Tipp erstellen</a>&nbsp;&middot;&nbsp;
    <a href="?site=tpl_test">Template Test</a>&nbsp;&middot;&nbsp;
    <a href="#">Men&uuml;erweiterung 4</a>&nbsp;&middot;&nbsp;
    <a href="?check_plugins">Plug-In's einlesen</a>&nbsp;&middot;&nbsp;
    <a href="#">Men&uuml;erweiterung 6</a>&nbsp;&middot;&nbsp;
</div>
<div class="box_content">
<div id="box_content_text">

<?php
				
		if(isset($_REQUEST[DEFAULT_LINK]) && !empty($_REQUEST[DEFAULT_LINK]))
		{
			if(in_array($_REQUEST[DEFAULT_LINK], $plugins))
			{
				$file	= PLUGIN_DIR . $_REQUEST[DEFAULT_LINK] . "/index.php";
				if(file_exists($file))
				{
					include($file);
				}
				else
				{
					include('404.php');
				}				
			}
			else
			{
				switch($_REQUEST[DEFAULT_LINK])
				{
					case 'addcat':
						include('addcategorie.php');
						break;
					case 'addtext':
						include('addtext.php');
						break;
					case 'tpl_test':
						include('test/tpl_test.php');
						break;
					default:
						include('404.php');
						break;
				}
			}
		}
		else
		{
			include('home.php');
		}
		?>
    </div>
    <div id="box_content_tiles" >
	<?php
		if(!isset($_SESSION['loggedIn']))
		{
			if(isset($_POST['login_x']) && ($login_notification instanceof wsc\systemnotification\SystemNotification))
			{
				$login_notification->printMessage();
			}
		//HTML Ausgabe...
	?>
        <div class="box_infopanel_login">
            <form action="<?php echo "?" . $forward_link; ?>" method="post" name="login" id="search">
			<fieldset>
			<legend>Benutzeranmeldung</legend>
                <table width="98%" cellspacing="0" align="left">
                    <tr>
                    	<td rowspan="2"><img src="grafiken/login.png" height="45" alt="login" title="Anmelden, um erweitere Funktionen zu nutzen." style="border-radius:2px;" /></td>
                        <td align="right">
                            <input name="username" type="text" placeholder="Benutzername oder E-Mail"style="width:154px; height:18px;" tabindex="1" />
                        </td>
                    </tr>
                    <tr>
                    	<td align="right">
						<input name="password" type="password" placeholder="Passwort eingeben..." style="width:129px; height:18px;" tabindex="2" />
						<input type="image" src="grafiken/login_form.png" name="login" style="width:20px; height:20px; margin-left:0px; border:1px solid #bbb;" tabindex="4" />
						</td>
                   	</tr>
                   	<tr>
                   		<td></td>
                   		<td align="left">	
							<input id="permanent_login" type="checkbox" name="save_login" tabindex="3" />
							<label for="permanent_login" style="">Angemeldet bleiben</label>
						</td>
                   	</tr>
                </table>
				</fieldset>
            </form>
			<br />
			<a href="#">Benutzername/Passwort vergessen</a>
        	<a href="#">Neues Benutzerkonto anlegen</a>     
        </div>
		<div class="clearing"></div>
		<?php 
		;
		}
		else
		{
		?>
		<div class="box_infopanel_controlcentre">
			<div class="livetile_1x1_userpic"><img src="grafiken/na.jpg" alt="Foto" height="60px" width="45px" /></div>
			<a href="#">
				<div class="livetile_2x1_username">
					<?php
						echo strtoupper("".$userdata['firstname']."<br />".$userdata['lastname']."");
					?>		
				</div>
			</a>
			<div class="clearing"></div>
            <div class="infopanel_controlcentre_tiles">
			<a href="#" class="infopanel_controlcentre_messages"></a>
			<a href="#" class="infopanel_controlcentre_notes"></a>
			<a href="#" class="infopanel_controlcentre_controls"></a>
			<a href="<?php echo "?" . $forward_link ."&logout" ?>" class="infopanel_controlcentre_logout"></a>
            <div class="clearing"></div>
            </div>
			
		</div>		
		<?php
		}
		?>
        <div class="livetile_2x1_calendar">
            <?php 
                echo "<strong>".strftime("%A, %d. %B %Y", time())."</strong><br /><br />Keine Eintr&auml;ge f&uuml;r die n&auml;chsten 7 Tage.<br /><br />
                <a href=\"#\"><span style=\"font-size:15px; font-weight:bold;\">Kalender</span></a> (Woche ".date("W").")";
            ?>
        </div>
    </div>
    <div class="clearing"></div>
</div>
<div class="box_footer">
	<a href="#">Impressum</a> | &copy; 2013 Michael Strasser. Alle Rechte vorbehalten.
</div>
</div>
</body>
</html>