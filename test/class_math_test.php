<?php
class math
{
	const PII				= 3.1415926;
	private $id				= 0;
	public static $nummer	= 0;
	
	public function __construct()
	{
		self::$nummer	= self::$nummer+1;
		$this->id		= self::$nummer;
	}
	
	public static function quadrat($zahl)
	{
		return $zahl * $zahl;
	}
	public function ausgabe($zahl)
	{
		echo "------------------<br />Nr.: " . $this->id . ", PI: " . self::PII . "<br /><br/>";
		echo self::quadrat($zahl) . "<br />";
	}
}
echo math::PII . " Math::PII<br />";
echo math::quadrat(10) . " Math::\$qudrat(2)<br />";
echo math::$nummer . " Math::\$nummer<br />";

$x	= new math;
$x->ausgabe(26);

$y	= new math;
$y->ausgabe(4);

$z	= new math;
$z->ausgabe(6);

echo __DIR__;

?>