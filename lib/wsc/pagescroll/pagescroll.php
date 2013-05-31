<?php
namespace wsc\pagescroll;

/**
 * Pagescroll (2013 - 02 - 15)
 *
 * Klasse um bei SQL Ausgaben, die Ausgabe auf mehrere Seiten zu aufzuteilen.
 *
 * @author 		michi_000
 * @name 		Pagescroll
 * @version		1.0
 * @copyright	2013 - Michael Strasser
 * @license		Alle Rechte vorbehalten.
 */
class Pagescroll
{
	/**
	 * @var int Zeilen Pro Seite
	 * @since 1.0
	 */
	protected $number_page	= 10;	//Zeilen pro Seite

	
	/**
	 * @var int Anzahl Posts
	 * @since 1.0
	 */
	protected $number_posts	= NULL; //Anzahl Posts gesamt;

	
	/**
	 * @var int Anfangsposition fuer SQL
	 * @since 1.0
	 */
	protected $start		= NULL;	//Anfangsposition

	
	/**
	 * @var array Linkformatierung
	 * @since 1.0
	 */
	protected $linkformat	= array();

	
	/**
	 * @var array Linknamen
	 * @since 1.0
	 */
	protected $linknames	= array	(	//Achtung! Nicht ändern. Über Methodenaufruf setLinkNames() im Hauptprogramm konfigurieren.
										"first"	=> "Erste Seite",
										"back"	=> "Zur&uuml;ck",
										"next"	=> "Weiter",
										"last"	=> "Letzte Seite"
									);
	
	
	/**
	 * Konstruktor
	 *
	 * Anzahl der Posts pro Seite und Anfangposition für SQL Abfrage wird den jeweiligen Eigenschaften zugewiesen.
	 *
	 * @param (int) posts_pro_seite
	 * @param (int) anfangsposition
	 * @since 1.0
	 */
	public function __construct($number_per_page, $start)
	{
		$this->number_page	= $number_per_page;	
		$this->start		= $start;

	}
	
	protected function checkRequiredParams()
	{
		if(is_null($this->start))
			throw new \Exception("Konstruktor ben&ouml;tigt Startlimit für MySQL Abfrage. Es wurde keine Variable Start &uuml;bergeben. <br />");
		if(is_null($this->number_posts))
			throw new \Exception("Bevor Links ausgegeben werden k&ouml;nnen, muss die Methode getNumberOfPosts() aufgerufen werden. <br />");
	}
	

	/**
	 * Anzahl der in der Datenbank vorhandenen Posts wird gezählt.
	 * 
	 * @param (string) Tabelle in Datenbank
	 * @param (string) "WHERE" Bedingung für SQL Abfrage.
	 * @since 1.0
	 */
	public function getNumberOfPosts($table, $condition = NULL)
	{
		if(!is_null($condition))
		{
			$condition	= "WHERE " . $condition;
		}
		
		$sql	= "SELECT * FROM " . $table . $condition;
		$res	= mysql_query($sql) or die(	"Fehler! Die SQL-Abfrage in getNumberOfPosts() ist ung&uuml;ltig.<br />
											Bitte die &uuml;bergebenen Parameter &uuml;berpr&uuml;fen.<br /><br /><strong>MySQL meldet:</strong><br />
											" . mysql_error() . "<br /><br /><strong>Felgende Abfrage wurde ausgef&uuml;hrt:</strong><br />" . $sql);
		$num	= mysql_num_rows($res);
		
		$this->number_posts	= $num;
	}
	
	
	/**
	 * SQL-Query wird gebaut und zurückgegeben.
	 *
	 * @return (string) SQL-Ergänzung
	 * @since 1.0
	 */	
	public function getQueryLimit()
	{
		$this->checkRequiredParams();
		
		$query	= "LIMIT " . $this->start . ", " . $this->number_page;
				
		return $query;
	}


	/**
	 * Anzahl der Seiten wird berechnet und pro Seite ein Link zurückgegeben.
	 *
	 * @return (array) Seitenlinks
	 * @since 1.0
	 */
	public function getPageLinks()
	{
		$this->checkRequiredParams();
		
		$pages		= intval($this->number_posts / $this->number_page);
		$pages		= ($this->number_posts % $this->number_page > 0) ? $pages += 1 : $pages;
		
		$seite	= intval($this->start / $this->number_page + 1);
		
		for($a=1; $a<=$pages; $a++)
		{
			$page	= ($a-1) * $this->number_page;
			
			if($seite == $a)
			{
				$style	= $this->linkformat['active_class'];
			}
			else
				$style	= $this->linkformat['class'];
			
			if($this->number_posts > $this->number_page)
			{
				$link[]	= "<a href=\"" . $this->linkformat['href'] . "&start=" . $page . "\" class=\"" . $style . "\">" . $this->linkformat['start_tag'] . $a . $this->linkformat['end_tag'] . "</a>";
			}
		}
		return $link;
	}

	
	/**
	 * Link für "zur ersten Seite" wird berechnet.
	 *
	 * @return (string) Link
	 * @since 1.0
	 */
	public function getFirstPage()
	{
		$this->checkRequiredParams();
		
		if($this->start > 0)
		{
			$first	= 0;
			
			$link	= "<a href=\"" . $this->linkformat['href'] . "&start=" . $first . "\" class=\"" . $this->linkformat['class'] . "\">" . $this->linkformat['start_tag'] . $this->linknames['first'] . $this->linkformat['end_tag'] . "</a>";
		}
		return $link;
	}
	
	
	/**
	 * Link für "Vorherige Seite" wird berechnet.
	 *
	 * @return (string) Link
	 * @since 1.0
	 */	
	public function getBackPage()
	{
		$this->checkRequiredParams();
		
		if($this->start > 0)
		{
			$back	= ($this->start - $this->number_page >= 0) ? $this->start - $this->number_page : 0;
			$link	= "<a href=\"" . $this->linkformat['href'] . "&start=" . $back . "\" class=\"" . $this->linkformat['class'] . "\">" . $this->linkformat['start_tag'] . $this->linknames['back'] . $this->linkformat['end_tag'] . "</a>";
		}
		return $link;
	}
	
	
	/**
	 * Link für "Nächste Seite" wird berechnet.
	 *
	 * @return (string) Link
	 * @since 1.0
	 */
	public function getNextPage()
	{
		$this->checkRequiredParams();
		
		$next	= $this->start + $this->number_page;
		
		if($this->start < ($this->number_posts - $this->number_page) && $this->number_posts > $this->number_page)
		{
			$link	= "<a href=\"" . $this->linkformat['href'] . "&start=" . $next . "\" class=\"" . $this->linkformat['class'] . "\">" . $this->linkformat['start_tag'] . $this->linknames['next'] . $this->linkformat['end_tag'] . "</a>";
		}
		return $link;
	}
	
	
	/**
	 * Link für "Letzte Seite" wird berechnet.
	 *
	 * @return (string) Link
	 * @since 1.0
	 */
	public function getLastPage()
	{		
		$this->checkRequiredParams();
		
		//Wenn Anzahl pro Seite nicht 1 ist dann Anzahl der Post für die Letzte Seite berechnen.
		if($this->number_page !== 1)
		{
			$last	= $this->number_posts - ($this->number_posts % $this->number_page);
		}
		//sonst Letzte Seite ist 1 Post
		else 
		{
			$last	= $this->number_posts - $this->number_page;
		}
		
		if($this->start < ($this->number_posts - $this->number_page) && $this->number_posts > $this->number_page)
		{
			$link	= "<a href=\"" . $this->linkformat['href'] . "&start=" . $last . "\" class=\"" . $this->linkformat['class'] . "\">" . $this->linkformat['start_tag'] . $this->linknames['last'] . $this->linkformat['end_tag'] . "</a>";
		}
		
		return $link;
	}
	
	
	/**
	 * Linkformatierung wird übernommen
	 * 
	 * @param (string) Ziel-Link
	 * @param (string) CSS-Klasse
	 * @param (string) CSS-Klasse wenn Seite = Link
	 * @param (string) Start Tag für Links (Präfix)
	 * @param (string) End-Tag für Links (Suffix)
	 * 
	 * @since 1.0
	 */
	public function setLinkFormat($href, $class="", $active_class, $start_tag="", $end_tag="")
	{
		$this->linkformat['href']			= $href;
		$this->linkformat['class']			= $class;
		$this->linkformat['active_class']	= $active_class;
		$this->linkformat['start_tag']		= $start_tag;
		$this->linkformat['end_tag']		= $end_tag;
	}
	
	
	/**
	 * Standardlinknamen werden geändert.
	 * 
	 * @param (string) Linkname["Erste Seite"]
	 * @param (string) Linkname["Zurück"]
	 * @param (string) Linkname["Weiter"]
	 * @param (string) Linkname["Letzte Seite"]
	 * 
	 * @since 1.0
	 */	
	public function setLinkNames($first, $back, $next, $last)
	{
		$this->linkname['first']	= $first;
		$this->linkname['back']		= $back;
		$this->linkname['next']		= $next;
		$this->linkname['last']		= $last;
	}
}

?>