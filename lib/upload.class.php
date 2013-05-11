<?php
/**
 * Upload (2013 - 01 - 26)
 * 
 * Zum Hochladen von Dateien aus Formularen.
 * Mit Dateierweiterungsbeschränkung.
 *
 * @author 		michi_000
 * @name 		Upload
 * @version		1.1
 * @copyright	2013 - Michael Strasser
 * @license		Alle Rechte vorbehalten.
 */
class Upload
{
	/**
	 * @var string Dateiklasse (pic, file, music, video, all, custom)
	 * @since 1.0
	 */
	protected $fileclass				= NULL; // Bestimmt Dateiklasse -> Wird vom Konstruktor Übergeben (Bei Objekterzeugung pic || file || music || video || all || custom wählen)
	
	
	/**
	 * @var string Uploadverzeichnis
	 * @since 1.0
	 */
	protected $upload_dir				= "files/uploaded_files"; //In dieses Verzeichnis werden Dateien hochgeladen.
	
	
	/**
	 * @var array Hochzuladende Datei
	 * @since 1.1
	 */
	protected $uploaded_file			= array(); //Datei die vom Formular hochgeladen wurde ("$_FILES[][]").
	
	
	/**
	 * @var array Dateierweiterungen -> wird von getAllowedFileExtensions() instiiert.
	 * @since 1.1
	 */
	protected $fileextensions			= array(); //Erlaubte Erweiterungen -> Können in getAllowedFileExtensions() verändert werden.
	
	
	/**
	 * @var int Dateigröße in Bytes
	 * @since 1.0
	 */
	protected $max_file_size			= 5242880; // max. Dateigröße in Byte (5 MByte)
	
	
	/**
	 * @var array Statusmeldung zur Übergabe an getUploadStatus().
	 * @since 1.1
	 */
	protected $upload_status			= array(); // Dient als Lieferant für Fehlermeldungen ins Hauptprogramm - wird von getUploadStatus() ans Hauptprogramm übergeben. 
	
	
	/**
	 * Konstruktor
	 * 
	 * Fileklasse wird zugewiesen. Falls Fileklasse "custom" ist, werden die erlaubten Erweiterungen der Eigenschaft $fileextensions zugewiesen.
	 * 
	 * @param array "$_FILES[][]" aus einem Formular wird erwartet.
	 * @param string Dateiklasse (pic, music, video, custom, all,  ...)
	 * @param optional bool false oder array erlaubte Dateierweiterungen
	 * @since 1.0
	 */		
	public function __construct($file, $fileclass, $custom = false)
	{
		$this->uploaded_file	= $file;
		$this->fileclass		= (!empty($fileclass)) ? $fileclass : "all";
		
		if ($custom !== false)
		{
			$this->fileextensions	= $custom; //$custom muss Array sein, beim Aufruf muss fileclass custom gewählt werden.
		}
	}
	
	
	/**
	 * Dateigröße und Erweiterung werden festegestellt.
	 *
	 * @return (array) Dateigröße und Erweiterung
	 * @since 1.0
	 */		
	protected function getFileProberties()
	{
		$fileextension 		= strtolower(strrchr($this->uploaded_file['name'],'.'));
		$filesize		= $this->uploaded_file['size'];
		
		$fileproberties	= array	(
								"size"	=> $filesize,
								"extension"	=> $fileextension
								);
								
		return $fileproberties;
	}
	
	
	/**
	 * Dateierweiterungen werden den "fileclasses" zugeordnet.
	 * @since 1.1
	 */
	protected function getAllowedFileExtensions()
	{
		switch ($this->fileclass)
		{
			case "pic":
				$this->fileextensions[]	= ".jpg";
				$this->fileextensions[]	= ".jpeg";
				$this->fileextensions[]	= ".png";
				$this->fileextensions[]	= ".bmp";
				$this->fileextensions[]	= ".gif";
				break;
				
			case "file":
				$this->fileextensions[]	= ".exe";
				$this->fileextensions[]	= ".bat";
				$this->fileextensions[]	= ".zip";
				$this->fileextensions[]	= ".rar";
				$this->fileextensions[]	= ".7z";
				break;
				
			case "music":
				$this->fileextensions[]	= ".mp3";
				$this->fileextensions[]	= ".wma";
				$this->fileextensions[]	= ".wave";
				$this->fileextensions[]	= ".ogg";
				break;
				
			case "video":
				$this->fileextensions[]	= ".wmv";
				$this->fileextensions[]	= ".mp4";
				$this->fileextensions[]	= ".avi";
				$this->fileextensions[]	= ".mpg";
				$this->fileextensions[]	= ".mpeg";
				break;
				
			case "all":
				$this->fileextensions[]	= "";
				break;
			
			case "custom":
				break;
				
			default:
				$this->fileextensions[]	= "";
				break;		
		}
	}

	
	/**
	 * Es wird festegellt ob Datei nicht zu groß ist und ob die Erweiterung, der beim Erzeugen des Objektesgewählten "fileclass" enthaltenen Erweiterungen entspricht.
	 *
	 * @return (string) Fehlermeldung wenn ein Fehler auftritt oder (bool) false wenn kein Fehler auftritt.
	 * @since 1.0
	 */
	protected function checkFileProberties()
	{
		$proberties	= $this->getFileProberties($this->uploaded_file);

		if($proberties['size'] > $this->max_file_size)
		{
			$error = TRUE;
			$err_msg	.= "Datei zu groÃŸ!";  
		}
		
		//Falls Dateierweiterung nicht im Array und Dateiklasse nicht "all" ist wird ein Fehler erzeugt.
		if(!in_array($proberties['extension'], $this->fileextensions) && ($this->fileclass != "all" )) 
		{	
			$error		= TRUE;
			$err_msg	.= "Dateityp ist nicht erlaubt! Muss eine Datei des folgenden Types sein: *". implode(", *",$this->fileextensions);
		}
		
		if($error == TRUE)
			return $err_msg;
		else
			return FALSE;
	}

	
	/**
	 * Prüft ob das Uploadverzeichnis exisitiert und nicht schreibgeschützt ist.
	 * @return (string) Fehlermeldung wenn ein Fehler auftritt oder (bool) false wenn kein Fehler auftritt.
	 * @since 1.0
	 */
	protected function checkSaveDir()
	{
		if (!is_writeable($this->upload_dir))
		{
			$error		= TRUE;
			$err_msg	= "Hoppla! Das Verzeichnis &quot;".$this->upload_dir."&quot; existiert nicht oder ist schreibgeschÃ¼tzt.";
			
			return $err_msg;
		}
		else
			return FALSE;
	}
	

	/**
	 * Methode zum Bestimmen der Dateinamen: Die ID der letzten hochgeladenen Datei, die in der
	 * Datenbank gespeichert wurde wird ausgelesen, +1 gerechnet und zurückgeschickt.
	 * Achtung! Der Dateiname hat noch keine Endung -> Diese muss extra hinzugefügt werden.
	 * 
	 * @return string Neuer Name der Datei OHNE Dateierweiterung.
	 * @since 1.0
	 */
	protected function getFileName()
	{
		$sql		= "SELECT id FROM uploaded_files ORDER BY id DESC LIMIT 1"; //Letzten geschriebenen Datensatz aus der Tabelle auslesen.
		$res		= mysql_query($sql) or die(mysql_error());
		$row		= mysql_fetch_assoc($res);
		$num		= mysql_num_rows($res);
		
		//Falls Ergebnis der Datenbankabfrage > 0 Dateiname = Ergebnis + 1 sonst ist der Dateiname 1;
		$filename	= ($num > 0) ? $row['id'] + 1 : $filename = 1;
		
		return $filename;
	}
	
	
	/**
	 * Datei wird hochgeladen und ein Eintrag in die Datenbank erstellt.
	 * @since 1.0		 *
	 */
	public function uploadFile()
	{	
		$this->getAllowedFileExtensions();
		
		$file_proberties	= $this->getFileProberties();
		$error_file			= $this->checkFileProberties();
		$error_dir			= $this->checkSaveDir();
		$filename			= $this->getFileName().$file_proberties['extension'];

		//Falls keine Fehler mit der Datei und dem Verzeichnis auftreten.
		if ($error_file == FALSE && $error_dir == FALSE)
		{

			move_uploaded_file($this->uploaded_file['tmp_name'],$this->upload_dir."/".$filename);
			
			$sql	= 	"INSERT uploaded_files
											(
												type, 
												ip, 
												date, 
												user, 
												extension
											) 
						VALUES 
							(
								'".$file_proberties['class']."',
								'".$_SERVER['REMOTE_ADDR']."',
								'".time()."',
								'".$_SESSION['userid']."',
								'".$file_proberties['extension']."'
							)";
				
			$res	= mysql_query($sql) or die(mysql_error());
						
			$this->upload_status	= array(	'no_error' 	=>	true, 
												'filename'	=>	$filename,
												'error_msg'	=>	""
											);
		}
		else
		{
			$this->upload_status	= array(	'no_error' 	=>	false, 
												'filename'	=>	"",
												'error_msg'	=>	$error_file.$error_dir
											);
		}
	}
	
	
	/**
	 * Uploadstatus wird abgefragt und kann über Hauptprogramm verwendet werden.
	 * @since 1.1		 *
	 */
	public function getUploadStatus()
	{
		return $this->upload_status;
	}
	
	public static function getFileData($file)
	{
		if(!empty($file))
		{
			$sql	= "SELECT * FROM uploaded_files WHERE id = " . $file;
			$res	= mysql_query($sql) or die(mysql_error());
			$row	= mysql_fetch_assoc($res);
	
			return $row;
		}
		else
		{
			return false;
		}
		
	}
}
?>