<?php

namespace wsc\logout;

/**
 * Login (2013 - 02 - 14)
 *
 * Klasse um einen Benutzer auszuloggen.
 *
 * @author 		michi_000
 * @name 		Login
 * @version		1.0
 * @copyright	2013 - Michael Strasser
 * @license		Alle Rechte vorbehalten.
 * @access		final
 */
final class Logout 
{
	/**
	 * Loggt den Benutzer aus.
	 *
	 * @since 1.0
	 */
	public static function logoutUser()
	{
		session_unset();
		session_destroy();
		unset($_COOKIE['login']);
		setcookie("login", "", time()-1);

	}
	
	
	/**
	 * Löscht Benutzerberechtigungen
	 *
	 * @return (NULL) Berechtigungen
	 * @since 1.0
	 */
	public static function unsetPermissions()
	{
		return NULL;
	}
}

?>