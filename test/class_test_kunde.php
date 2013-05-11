<?php
	class Kunde
	{
		public $name;
		protected $speicherplatz	= 50;
		protected $belegt			= 0;
		
		public function __construct($name)
		{
			if(!empty($name))
				$this->name	= $name;
			else
				$this->name	= "Es wurde kein Name eingebeben!";
		}
		
			
		public function ausgabe()
		{
			
			
			echo "<br />".$this->name."<br /><br />";
			echo $this->speicherplatz." MB Speicherplatz<br />davon belegt: ";
			echo $this->belegt." MB<br />Freier Speicherplatz: ";
			echo $this->speicherplatz-$this->belegt." MB<br/>";
		}
		
		public function speichern($menge)
		{
			if(($this->speicherplatz - $this->beglegt) >= $this->speicherplatz)
				$this->belegt += $menge;
		}
		
	}
	
	$kunde_1	= new Kunde("Michael Strasser");
	$kunde_1->ausgabe();
	$kunde_1->speichern(20);
	$kunde_1->ausgabe();