<?php

namespace controller\fw_tests;

use wsc\controller\controller_abstract;
use wsc\form\element\Password;
use wsc\form\element\Submit;
use wsc\form\element\Text;
use wsc\form\Form;
use wsc\form\element\Element;

/**
 *
 * @author Michi
 *        
 */
class fw_tests extends controller_abstract 
{
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
		
		$register     = new Form("register");
		$register->setAttribute("action", "#");
		
		$vorname      = new Text("vorname");
		$vorname->setAttribute("placeholder", "Vorname");
		
		$nachname     = new Text("nachname");
		$nachname->setAttribute("placeholder", "Nachname");
		
		$password     = new Password("pwd");
		$password->setLabel("Passwort");
		$password->setAttribute("placeholder", $password->getLabel());
		
		$male         = new Element("gender");
		$male->setAttribute("type", "checkbox");
		$male->setLabel("Ich habe die <a href=\"#\">AGB</a> gelesen, verstanden und akzeptiere diese");
		
		$password_rpd = new Password("pwd_wdh");
		$password_rpd->setLabel("Wiederholung");
		$password_rpd->setAttribute("placeholder", $password_rpd->getLabel());
		
		$senden       = new Submit("speichern");
		$senden->setAttribute("value", "Formular abschicken...");
		
		$register->add($vorname);
		$register->add($nachname);
		$register->add($password);
		$register->add($password_rpd);
		$register->add($senden);
		$register->add($male);
		
		$view->assign(array(
		    'register'        => $register,
		));
		
	}
}

?>