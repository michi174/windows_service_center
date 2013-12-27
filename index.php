<?php
use wsc\database\Database;
use wsc\auth\Auth;
use wsc\functions\tools\Tools;
use wsc\template\Template;
use wsc\pluginmanager\PluginManager;
use wsc\acl\Acl;
use wsc\application\Application;

session_start();
date_default_timezone_set('Europe/Vienna');
setlocale (LC_ALL, 'deu');

require_once '../framework/config.php';
require_once 'autoloader.php';

//Anwendung konfigurieren
$config->set("project_dir", "windows_service_center");
$config->set("abs_project_path", $config->get("doc_root")."/".$config->get("project_dir"));
$config->readIniFile($config->get("abs_project_path").'/admin/config.ini');
$config->set("forward_link", $_SERVER['QUERY_STRING']);

$app		= new Application();

//Module registrieren
$app->register("Database", Database::getInstance());
$app->register("Acl", new Acl($app));
$app->register("Auth", new Auth($app));

try 
{
	$db			= $app->load("Database");
	$auth		= $app->load("Auth");
	$acl		= $app->load("Acl");
}
catch (Exception $e)
{
	die("Es ist ein Fehler aufgetreten in: <br />
	<strong>". $e->getFile()." Zeile: ".$e->getLine()."</strong><br /><br />
	Meldung: <br />".$e->getMessage()."<br /><br />
	Backtrace: <br />".nl2br($e->getTraceAsString()));
}

$app->load("FrontController")->addSubController("main", array("acl_test", "controller"));
$app->run();

$plugins	= PluginManager::getPlugins(false);

if(isset($_GET['logout']))
{
	$auth->logout();
	header('Location:?'.str_replace('&logout', "", $config->get("forward_link")));  //ACHTUNG: Nicht einsetzen. Muss über Repsonse Klasse gelöst werden!
}
if(isset($_POST['login_x']))
{
	$username	= $_POST['username'];
	$password	= $_POST['password'];
	$cookie		= (isset($_POST['save_login'])) ? true : false;

	$auth->login($username, $password, $cookie);
}

$user		= $auth->getUser();



//Ab hier sollte bereits der FrontController übernehmen. Bis das funktioniert und um nicht immer eine weiße Seite zu sehen,
//wird hier ein View erzeugt.
$date	= array();
$date["d"]	= strftime("%d", time()); 	//Tag als Zahl
$date["m"]	= strftime("%m", time()); 	//Monat als Zahl
$date["Y"]	= strftime("%Y", time()); 	//Jahr als 4-stellige Zahl
$date["H"]	= strftime("%H", time()); 	//Stunden (24 h)
$date["M"]	= strftime("%M", time()); 	//Minuten
$date["S"]	= strftime("%S", time()); 	//Sekunden
$date["A"]	= strftime("%A", time()); 	//Tag als Text
$date["B"]	= strftime("%B", time()); 	//Monat als Text
$date["V"]	= date("W", time()); 		//Kalenderwoche
$date["u"]	= strftime("%u", time()); 	//Kalendertag

$page_error	= NULL;

//Templateeinstellungen
$head		= new Template();
$header		= new Template();
$footer		= new Template();
$livetiles	= new Template();
$content	= new Template();

$head->setTemplateDir($config->get("abs_project_path")."/template/win8_style/templates/");
$head->addTemplate("head.html");

$header->setTemplateDir($config->get("abs_project_path")."/template/win8_style/templates/");
$header->addTemplate("header.html");
$header->assign("LOGGED_IN", $auth->isLoggedIn());
$header->assign("FIRSTNAME", $user->data['firstname']);
$header->assign("BACKEND_VIEW", $acl->hasPermission($user, "backend", "view"));
$header->assign("PLUGINS", $plugins);

$livetiles->setTemplateDir($config->get("abs_project_path")."/template/win8_style/templates/");
$livetiles->addTemplate("livetiles.html");
$livetiles->assign("LOGGED_IN", $auth->isLoggedIn());
$livetiles->assign("FIRSTNAME", $user->data['firstname']);
$livetiles->assign("LASTNAME", $user->data['lastname']);
$livetiles->assign("SELF_LINK", "?".$config->get("forward_link"));
$livetiles->assign("DATE", $date);

$footer->setTemplateDir($config->get("abs_project_path")."/template/win8_style/templates/");
$footer->addTemplate("footer.html");


//Provisorische Ausgabe. Sollte über die Views in den Controllern erfolgen.
$head->display();
$header->display();
$livetiles->display();

?>
<div class="box_content">
	<div id="box_content_text">
		<?php 
			if(isset($_REQUEST[$config->get("default_link")]) && !empty($_REQUEST[$config->get("default_link")]))
	        {
	            if(Tools::array_search_recursive($_REQUEST[$config->get("default_link")], $plugins) !== false)
	            {
	                $file	= PLUGIN_DIR . $_REQUEST[$config->get("default_link")] . "/index.php";
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
	                switch($_REQUEST[$config->get("default_link")])
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
	                    case 'admin':
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
</div>
<?php
$footer->display();
?>