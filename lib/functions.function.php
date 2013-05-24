<?php


/**
 * Limitiert einen String anhand der Anzahl der Wörter.
 * 
 * @param	string	$string		Text, der gekürzt werden soll
 * @param	int		$limit		Anzahl der Wörter
 * 
 */
function limitStringbyWords($string, $limit)
{
	$words	= str_word_count($string, 2);
	
	$loopcount	= 0;
	
	if(count($words) > $limit)
	{
		foreach($words as $position => $word)
		{
			if($loopcount === $limit)
			{
				$string	= substr($string, 0, $position)."&hellip;";
				break;
			}
			$loopcount += 1;
		}
	}
	return $string;
}
?>