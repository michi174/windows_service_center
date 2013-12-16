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
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
    <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
    <script type="text/javascript" src="js_functions.js"></script>
    
    <title>Windows Service Center - The Ultimate Servicecenter</title>
</head>
<body>
<div id="darkbackground"></div>
<header class="box_header">
    <div class="header_wrapper" style="width:1200px; margin:auto; font-family:'Segoe UI', Tahoma, Helvetica, Sans-Serif;">
        <div class="header_pic" style="float:left; padding-top:10px;"><img src="template/win8_style/grafics/other/win8_logo_small.png" /></div>
        
        <div style="float:right; width:91%; border-bottom:1px solid #111; box-shadow: 0px 1px #333">
        	<div class="header_text" style="float:left; font-size:36px; color:#3CF; letter-spacing:3px;">Windows Service Center</div>
            <div id="header_notification_bar" style="float:right; line-height:24px;">
            	<a href="#" title="Benachrichtigung"><img src="template/win8_style/grafics/usercontrolcentre/header_notification.png" /></a>
                <a href="#" title="Nachrichten"><img src="template/win8_style/grafics/usercontrolcentre/header_message.png" /></a>
                
				<?php if($acl->hasPermission($user, "backend", "view"))
                {
				?>
                <a href="?site=backend" title="Backend"><img src="template/win8_style/grafics/usercontrolcentre/backend_settings.png" /></a>
                <?php 
                }
				?>
            </div>
        	<div id="header_user_info"><?php echo "Benutzer: " . $user->data['firstname'] . "<br />";?></div>
        	<div class="clearing"></div>
        </div>
        <div class="clearing"></div>
        
        <div class="header_subtitle" style="margin-left:117px; margin-top:-50px; letter-spacing:2px;">The Ultimate Servicecenter</div>
	</div>
</header>
<nav>
    <div class="menurow_top">
        <div class="menurow_top_items">
            <ul>
                <?php wsc\pluginmanager\PluginManager::getPlugins(true);?>
                <li class="search">
                    <form method="post" id="search" action="#">
                        <input type="text" placeholder="Suchbegriff eingeben..." style="width:230px; height:20px;" />
                        <input type="image" src="grafiken/search.png" style="width:16px; height:16px;" />
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div class="menu_extension" id="infoDIV">
    <div class="menu_extension_items">
        <a href="?site=addcat">Kategorie hinzuf&uuml;gen</a>&nbsp;&middot;&nbsp;
        <a href="?site=addtext">Tipp erstellen</a>&nbsp;&middot;&nbsp;
        <a href="?site=tpl_test">Template Test</a>&nbsp;&middot;&nbsp;
        <a href="?site=acl_test&action=view">ACL Admin</a>&nbsp;&middot;&nbsp;
        <a href="javascript:void()" id="">JavaScript</a>&nbsp;&middot;&nbsp;
        <a href="?check_plugins">Plug-In's einlesen</a>
    </div>
</div>
<div id="testbox"></div>
<div class="box_content">
<input type="hidden" id="cc_fixed" value=""/>
<?php
if(isset($_POST['login_x']) && isset($login_notification))
{
 //HTML Ausgabe...
?>
<div class="imp_error" id="imp_error">
	<div style="padding:5px; background-color:#AAA">
        <div style="float:left;">Systemmitteilung</div>
        <div style="float:right; text-decoration:none;"><a href="javascript:void()" onclick="showSystemNotification('imp_error')">x</a></div>
        <div class="clearing"></div> 
    </div>
</div>
  
<?php 	
}
?>
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
                    case 'acl_test':
                      	include('test/acl_test.php');
                        break;
                    case 'backend':
                       	include('backend/index.php');
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
        ?>   	
        <div class="box_infopanel_login">
            <form action="<?php echo "?" . $forward_link; ?>" method="post" name="login" id="search">
            <fieldset>
            <legend>Benutzeranmeldung</legend>
                <table width="98%" align="left" cellspacing="0">
                    <tr>
                        <td rowspan="2" width="10%">
                        	<img src="grafiken/login.png" height="45" alt="login" title="Anmelden, um erweitere Funktionen zu nutzen." style="border-radius:2px;" />
                        </td>
                        <td align="right" width="90%">
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
                        <td>&nbsp;</td>
                        <td align="left" style="padding-left:9px;">	
                            <input id="permanent_login" type="checkbox" name="save_login" tabindex="3" />
                            <label for="permanent_login">Angemeldet bleiben</label>
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
                        echo strtoupper("".$user->data['firstname']."<br />".$user->data['lastname']."");
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
                echo "	<span style=\"font-size:15px; font-weight:bold;\">".strftime("%A, %d. %B %Y", time()).", <span id=\"zeit\">Zeit wird ermittelt...</span></span>
						<br /><br />Keine Eintr&auml;ge f&uuml;r die n&auml;chsten 7 Tage.<br /><br /><br /><br />
                		<a href=\"#\"><span style=\"font-size:15px; font-weight:bold;\">Kalender</span></a> (Woche ".date("W").")";
            ?>
        </div>
    </div>
    <div class="clearing"></div>
</div>
<footer class="box_footer">
	<div class="footer_content">
    	<a href="#">Impressum</a> | &copy; 2013 Michael Strasser. Alle Rechte vorbehalten.
    </div>
</footer>
</body>
</html>