<?php

namespace controller\fw_tests;

use wsc\controller\controller_abstract;
use wsc\form\element\Password;
use wsc\form\element\Submit;
use wsc\form\element\Text;
use wsc\form\Form;
use wsc\form\element\Element;
use wsc\systemnotification\SystemNotification;
use wsc\validator\StringLength;
use wsc\validator\Between;

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
	    $view     = $this->createView();
		$notify   = new SystemNotification();
		
		$register = new Form("register");
		$register->setAttribute("action", "#");
		
		$vorname  = (new Text("vorname"))
                    ->setAttribute("placeholder", "Vorname")
                    ->setRequired()
                    ->setDisplayName("Vorname");
		
		$nachname = (new Text("nachname"))
            		->setAttribute("placeholder", "Nachname")
            		->setRequired();
		
		$password = (new Password("pwd"))
                    ->setDisplayName("Passwort")
                    ->setLabel("Passwort")
                    ->setAttribute("placeholder", "Passwort")
                    ->addValidator(
                        (new StringLength(
                            array(
                                'min' => 6,
		                        'max' => 20)))
                        ->setMessage(StringLength::IS_TOO_SHORT, "Das Passwort muss mindestens {min} Zeichen lang sein."));
		
		$male     = (new Element("gender"))
                    ->setAttribute("type", "checkbox")
                    ->setLabel("Ich habe die <a href=\"#\">AGB</a> gelesen, verstanden und akzeptiere diese");
		
		$password_rpd = (new Password("pwd_wdh"))
                        ->setLabel("Wiederholung")
                        ->setAttribute("placeholder", "Wiederholung");
		
		$senden   = (new Submit("speichern"))
                    ->setAttribute("value", "Formular abschicken...");
		
        $register   ->add($vorname)
                    ->add($nachname)
                    ->add($password)
                    ->add($password_rpd)
                    ->add($senden)
                    ->add($male);
		
		if(isset($_POST['speichern']))
		{
		    if($register->isValid())
		    {
		        $notify->addMessage("Das Formular wurde erfolgreich validiert.", "success");
		        $view->assign("valid", true);
		    }
		    else
		    {
		        $message  = "Es sind folgende Fehler bei der Validierung aufgetreten:<br><br>";
		        
		        foreach ($register->getMessages() as $element => $messages)
		        {
		            $element  = $register->get($element)->getDisplayName();
		            $message  .= $element . "<br>";
		            
		            foreach ($messages as $element_msg)
		            {
		                $message  .= "&nbsp&bull;&nbsp;" . $element_msg . "<br>";
		            }
		        }
		        $notify->addMessage($message, "error");
		    }
		}
		
		$view->assign(array(
		    'register'        => $register,
		    'notification'    => $notify->printMessage(true),
		));
		
	}
}

?>