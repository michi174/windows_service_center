<?php


/**
 * Limitiert einen String anhand der Anzahl der W�rter.
 * 
 * @param	string	$string		Text, der gek�rzt werden soll
 * @param	int		$limit		Anzahl der W�rter
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