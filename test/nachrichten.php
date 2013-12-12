<?php
date_default_timezone_set("UTC");
$handle = fopen ("nachricht.txt", "r");

$zeile	= 0;
$last_int = false;

$eva	= "Eva-maria";
$sarah	= "Sarah Fabella";

$name		= "";
$datum		= "";
$message	= "";

$messages	= array();

while (!feof($handle)) 
{
	$nachricht = fgets($handle);
	
	//Name filtern
	if((strpos($nachricht, $eva) !== false || strpos($nachricht, $sarah) !== false) && $last_int == true)
	{
		$name	= $nachricht;
	}
	//Nachricht filtern
	if((strpos($nachricht, $eva) === false && strpos($nachricht, $sarah) === false) && !is_numeric(substr($nachricht, 0,2)))
	{
		$message	= $nachricht;
	}
	
	//Datum filtern
	if(is_numeric(substr($nachricht, 0,2)))
	{
		$datum		= $nachricht;
		$last_int	= true;
	}
	else 
	{
		$last_int = false;
	}

	//Wenn alle Felder befüllt sind, kann es in das Array gespeichert werden.
	if(!empty($datum) && !empty($name) && !empty($message))
	{
		$messages[]	= array("date" => trim($datum), "name" => trim($name), "text" => trim($message));
		
		$datum 		= "";
		$message	= "";
		$name		= "";
	}
	
	$zeile += 1;

}
fclose ($handle);

echo "	<table width=\"100%\">
			<tr>
				<td>Name</td>
				<td>Datum</td>
				<td>Nachricht</td>
			</tr>
		";
foreach($messages as $message)
{
	echo "	<tr>
				<td>".$message['name']."</td>
				<td>".$message['date']."</td>
				<td>".$message['text']."</td>
			</tr>";
}
echo 
"		</table>"
?>