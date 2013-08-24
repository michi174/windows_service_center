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


function toogleView(id)
{
	var display = document.getElementById(id).style.display;
	
	if(display == "none")
	{
		document.getElementById(id).style.display = "block";
	}
	else
	{
		document.getElementById(id).style.display = "none";
	}
}

function showControlCentre(id)
{
	if(document.getElementById(id).style.opacity == 0)
	{
		document.getElementById(id).style.opacity = "1";
		document.getElementById(id).style.top = "190px";
	}
	else
	{
		document.getElementById(id).style.opacity = "0";
		document.getElementById(id).style.top = "-100px";
	}
}