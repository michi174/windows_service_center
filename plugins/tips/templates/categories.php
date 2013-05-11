<?php 
while(($data = $res->fetch_assoc()) == true )
{
	//HTML Ausgabe....
?>
<div><?=$data['title']?></div>
<?php
	if($show_boards === true)
	{
		$boards	= new CMS;
		
	}
?>





<?php
}
?>