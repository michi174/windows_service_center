<?php
namespace wsc\bbcode;
/**
 * BBCode (2013 - 02 - 20)
 *
 * Klasse um BB-Code formatierten Text in HTML umzuwandeln.
 *
 * @author 		michi_000
 * @name 		BBCode
 * @version		1.0
 * @copyright	2013 - Michael Strasser
 * @license		Alle Rechte vorbehalten.
 */
class BBCode
{
	protected $allow_smileys;
	protected $allow_html;	
	protected $linebreak;
	
	public function parseText($bbtext, $linebreak = false, $smileys = false, $html = false)
	{
		if($html === false)
		{
			$bbtext	= htmlentities($bbtext);
		}
		if($linebreak === true)
		{
			$bbtext	= nl2br($bbtext);
		}
		if($smileys === true)
		{
			$bbtext = str_replace(':)', 			'<img src="template/win8_style/grafics/smileys/smile.png" />', 		$bbtext);
			$bbtext = str_replace(':angel:', 		'<img src="template/win8_style/grafics/smileys/angel.png" />', 		$bbtext);
			$bbtext = str_replace(':angry:', 		'<img src="template/win8_style/grafics/smileys/angry.png" />', 		$bbtext);
			$bbtext = str_replace(':cheekey:', 		'<img src="template/win8_style/grafics/smileys/cheeky.png" />', 		$bbtext);
			$bbtext = str_replace(':wink:', 		'<img src="template/win8_style/grafics/smileys/wink.png" />', 			$bbtext);
			$bbtext = str_replace(':confused:', 	'<img src="template/win8_style/grafics/smileys/confused.png" />',		$bbtext);
			$bbtext = str_replace(':cool:', 		'<img src="template/win8_style/grafics/smileys/cool.png" />', 			$bbtext);
			$bbtext = str_replace(':cry:', 			'<img src="template/win8_style/grafics/smileys/cry.png" />', 			$bbtext);
			$bbtext = str_replace(':devil:', 		'<img src="template/win8_style/grafics/smileys/devil.png" />', 		$bbtext);
			$bbtext = str_replace(':kiss:', 		'<img src="template/win8_style/grafics/smileys/kiss.png" />', 			$bbtext);
			$bbtext = str_replace(':lol:', 			'<img src="template/win8_style/grafics/smileys/lol.png" />', 			$bbtext);
			$bbtext = str_replace(':love:', 		'<img src="template/win8_style/grafics/smileys/love.png" />', 			$bbtext);
			$bbtext = str_replace(':sad:', 			'<img src="template/win8_style/grafics/smileys/sad.png" />', 			$bbtext);
			$bbtext = str_replace(':sleepy:', 		'<img src="template/win8_style/grafics/smileys/sleepy.png" />', 		$bbtext);
			$bbtext = str_replace(':speechless:', 	'<img src="template/win8_style/grafics/smileys/speechless.png" />',	$bbtext);
			$bbtext = str_replace(':surprised:', 	'<img src="template/win8_style/grafics/smileys/surprised.png" />',	 	$bbtext);
			$bbtext = str_replace(':worried:', 		'<img src="template/win8_style/grafics/smileys/worried.png" />', 		$bbtext);
			
		}
		
		# Formatierungen
		$bbtext = preg_replace('#\[b\](.*)\[\/b\]#ismU', "<b>$1</b>", $bbtext);
		$bbtext = preg_replace('#\[i\](.*)\[\/i\]#ismU', "<i>$1</i>", $bbtext);
		$bbtext = preg_replace('#\[u\](.*)\[\/u\]#ismU', "<u>$1</u>", $bbtext);
		$bbtext = preg_replace('#\[color=([\#A-Za-z0-9]{4,7}|[a-zA-z]+)\](.*)\[\/color\]#ismU', "<span style=\"color: $1\">$2</span>", $bbtext);
		$bbtext = preg_replace('#\[size=([0-9]{2})\](.*)\[\/size\]#ismU', "<span style=\"font-size: $1px\">$2</span>", $bbtext);
		
		# Links
		
		//Wenn Protokoll angegeben ist, dann wird Protokoll �bernommen (Erlaubt: http, https, ftp);
		$bbtext = preg_replace('#\[url\](http|ftp|https)(\:\/\/.*)\[\/url\]#ismU', "<a href=\"$1$2\" target=\"_blank\">$1$2</a>", $bbtext);
		
		//Wenn kein Protokoll angegeben, dann wird automatisch mit http:// versucht.
		$bbtext = preg_replace('#\[url\](.*)\[\/url\]#ismU', "<a href=\"http://$1\" target=\"_blank\">$1</a>", $bbtext);
		
		$bbtext = preg_replace('#\[url=(http|ftp|https)(\:\/\/.*)\](.*)\[\/url\]#ismU', "<a href=\"$1$2\" target=\"_blank\">$3</a>", $bbtext);
		$bbtext = preg_replace('#\[url=(.*)\](.*)\[\/url\]#ismU', "<a href=\"http://$1\" target=\"_blank\">$2</a>", $bbtext);
		
		
		
		# Grafiken
		$bbtext = preg_replace('#\[img\](.*)\[\/img\]#ismU', "<img src=\"$1\" alt=\"$1\" />", $bbtext);
		
		# Zitate
		$bbtext = preg_replace('#\[quote\](.*)\[\/quote\]#ismU', "<div class=\"zitat\">$1</div>", $bbtext);
		
		# Quelltext
		$bbtext = preg_replace('#\[code\](.*)\[\/code\]#ismU', "<div class=\"code\">$1</div>", $bbtext);
		
		# PHP Code
		$bbtext = preg_replace('#\[php\](.*)\[\/php\]#ismU', "<div class=\"code\">" . highlight_string("$1", true) . "</div>", $bbtext);
		
		# Listen
		$bbtext = preg_replace('#\[list\](.*)\[\/list\]#ismU', "<ul>$1</ul>", $bbtext);
		$bbtext = preg_replace('#\[list=(1|a)\](.*)\[\/list\]#ismU', "<ol type=\"$1\">$2</ol>", $bbtext);
		$bbtext = preg_replace('#\[\*\](.*)\\r\\n#ismU', "<li>$1</li>", $bbtext);
		
		return $bbtext;
	}
}
?>