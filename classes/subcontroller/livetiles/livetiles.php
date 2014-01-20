<?php

namespace subcontroller\livetiles;

use wsc\controller\Subcontroller_abstract;
use wsc\view\View_template;
use wsc\application\Application;

/**
 *
 * @author Michi
 *        
 */
class Livetiles extends Subcontroller_abstract
{
	private $application;
	
	public function __construct()
	{
		$this->application = Application::getInstance();
	}
	private function build()
	{

	}
	
	public function runBeforeMain()
	{
		$auth	= $this->application->load("Auth");
		$user	= $auth->getUser();
		$config	= $this->application->load("Config");
		
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
		
		$next	= $this->application->load("request")->get("next") == null 
					? "?".$config->get("forward_link") 
					: "?".$this->application->load("request")->get("next");
		$show_error	= ($auth->getErrors() !== false) ? 1 : 0;
		
		$view	= new View_template(true);
		
		$view->assignVar("ERRORS", $auth->getErrors());
		$view->assignVar("LOGGED_IN", $auth->isLoggedIn());
		$view->assignVar("SHOW_LOGIN_ERROR", $show_error);
		
		$view->assignVar("FIRSTNAME", $user->data['firstname']);
		$view->assignVar("LASTNAME", $user->data['lastname']);
		

		
		$view->assignVar("NEXT", $next);
		$view->assignVar("DATE", $date);
		
		return $view;
	}
	
}

?>