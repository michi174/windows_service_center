<?php

namespace controller\fw_tests;

use wsc\controller\controller_abstract;
use wsc\view\View_php;
use wsc\form\element\Element;
use wsc\form\element\Password;
use wsc\form\element\Submit;
use wsc\form\element\Text;

/**
 *
 * @author Michi
 *        
 */
class fw_tests extends controller_abstract {
	
	public function validator_action()
	{
		$view	= new View_php();	
		
		return $view;
	}
	
	public function default_action() 
	{
		$view	= new View_php();
		return $view;
	}
	
	public function form_action()
	{
		$this->view	= new View_php();
		
		$vorname	= new Text("vorname");
		$vorname->setAttribute("placeholder", "Vorname");
		
		$nachname	= new Text("nachname");
		$nachname->setAttribute("placeholder", "Nachname");
		
		$password	= new Password("pwd");
		$password->setLabel("Passwort");
		$password->setAttribute("placeholder", $password->getLabel());
		
		
		$senden		= new Submit("speichern");
		$senden->setAttribute("value", "Formular abschicken...");
		
		$this->view->assign(array(
			'nachname'	=> $nachname,
			'vorname'	=> $vorname,
			'senden'	=> $senden,
			'pw'		=> $password
		));
		
		return $this->view;
	}
}

?>