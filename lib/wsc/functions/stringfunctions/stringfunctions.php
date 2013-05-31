<?php
namespace wsc\functions\stringfunctions;
/**
 * StringFuntions (2013 - 05 - 25)
 *
 * Stringfunktionen
 *
 * @author 		michi_000
 * @name 		StringFunctions
 * @version		1.0
 * @copyright	2013 - Michael Strasser
 * @license		Alle Rechte vorbehalten.
 */
class StringFunctions
{
	/**
	 * Limitiert einen String anhand der Anzahl der Wörter.
	 *
	 * @param	string	$string		Text, der gekürzt werden soll
	 * @param	int		$limit		Anzahl der Wörter
	 * 
	 * @since 1.0
	 *
	 */
	
	public static function limitWords($string, $limit)
	{
		$words	= str_word_count($string, 2);
	
		$loopcount	= 0;
	
		if(count($words) > $limit)
		{
			foreach($words as $position => $word)
			{
				if($loopcount === $limit)
				{
					$string	= substr($string, 0, $position);
					break;
				}
				$loopcount += 1;
			}
		}
		return $string;
	}
}
?>