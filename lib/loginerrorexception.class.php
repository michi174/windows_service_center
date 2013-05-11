<?php
/**
 * LoginErrorException (2013 - 02 - 09)
 * 
 * Klasse um Fehler zu behandeln.
 * 
 * @author 		michi_000
 * @name 		Login
 * @version		1.0
 * @copyright	2013 - Michael Strasser
 * @license		Alle Rechte vorbehalten.
 */ 
class LoginErrorException extends Exception 
{
	
	/**
	 * Konstruktor
	 * 
	 * übergibt die Fehlermeldung an die Parentklasse Exception
	 *
	 * @param message
	 *        	
	 */
	public function __construct($message, $code = NULL) 
	{
		parent::__construct ($message, $code);
	}
}

?>