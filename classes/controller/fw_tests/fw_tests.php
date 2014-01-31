<?php

namespace controller\fw_tests;

use wsc\controller\controller_abstract;
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
		$view	= $this->createView();		
	}
	
	public function default_action() 
	{
		$view	= $this->createView();
	}
	
	public function form_action()
	{
		$view = $this->createView();
		
		$vorname	= new Text("vorname");
		$vorname->setAttribute("placeholder", "Vorname");
		
		$nachname	= new Text("nachname");
		$nachname->setAttribute("placeholder", "Nachname");
		
		$password	= new Password("pwd");
		$password->setLabel("Passwort");
		$password->setAttribute("placeholder", $password->getLabel());
		
		$password_rpd	= new Password("pwd_wdh");
		$password_rpd->setLabel("Wiederholung");
		$password_rpd->setAttribute("placeholder", $password_rpd->getLabel());
		
		
		$senden		= new Submit("speichern");
		$senden->setAttribute("value", "Formular abschicken...");
		
		$view->assign(array(
			'nachname'	=> $nachname,
			'vorname'	=> $vorname,
			'senden'	=> $senden,
			'password'	=> $password,
		    'password_rpd'    => $password_rpd
		));
		
	}
}

?>