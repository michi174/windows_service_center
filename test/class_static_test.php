<?php
class rechner
{
   static private $anzahlOperationen = 0;
   static public function addiere($l, $r)
   {
      self::$anzahlOperationen += 1;
      return ($l + $r);
   }
   static public function subtrahiere($l, $r)
   {
      self::$anzahlOperationen += 1;
      return ($l - $r);
   }
}
echo rechner::$anzahlOperationen."<br />"; // gibt 0 aus
echo rechner::addiere(22, 20)."<br />"; // gibt 42 aus
echo rechner::subtrahiere(1, 3)."<br />"; // gibt -2 aus
echo rechner::$anzahlOperationen."<br />"; // gibt 2 aus