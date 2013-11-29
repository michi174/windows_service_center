$(document).ready(function(){
    //jQuery Code	

	//---
	$('#testbox').hover(function()
	{
		$('#box_content_tiles').show('slide',{direction:'right'});
	})
	
	$('#box_content_tiles').mouseover(function()
	{
		$('#cc_fixed').val('false');
	})
		//---
	
	$('#box_content_text').mouseover(function()
	{
		if($('#cc_fixed').val() != 'true')
		{
			$('#box_content_tiles').hide('slide',{direction:'right'});
		}
	})
	
	$('#header_user_info').click(function()
	{
		
		$('#box_content_tiles').toggle('slide',{direction:'right'});
		$('#cc_fixed').val('true');
	})
});

function changeContent(id, shtml)
{
	document.getElementById(id).innerHTML = shtml;
}


function showBox(id)
{
	document.getElementById(id).style.display = "block";
}


function hideBox(id)
{
	document.getElementById(id).style.display = "none";
}

function showSystemNotification(id)
{
	document.getElementById(id).style.opacity = "0";
}
    
window.setInterval("uhr()",1000);

function uhr()
{
d = new Date ();

h = (d.getHours () < 10 ? '0' + d.getHours () : d.getHours ());
m = (d.getMinutes () < 10 ? '0' + d.getMinutes () : d.getMinutes ());
s = (d.getSeconds () < 10 ? '0' + d.getSeconds () : d.getSeconds ());

document.getElementById("zeit").innerHTML = h + ':' + m + ':' + s;
}