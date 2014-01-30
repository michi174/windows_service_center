
$(document).ready(function(){
    //jQuery Code
		
	//DevConsole
	
	$('#console-toggler').click(function(){
		$('#console').toggle(100);
	});
	
	
	if($('#num-log-entries').val() > 0)
	{
		$('#console-toggler').append(' ('+$('#num-log-entries').val()+')');
	}
	
	
	
	//Windows
	$('.window').draggable({
		handle:'.window-title',
		containment:'parent',
		stack: ".window",
	});
	

	
	//Closeables
	$closeable	= $(".closeable");
	
	
	$closeable.hover(function()
	{
		$closeable.removeClass('readytoclose');
		
		$(this).append("<div id='close-cross'></id>");		
		$(this).addClass('readytoclose');
		
		$('#close-cross').show().position({
			of:(this),
			my: 'right top',
			at: 'right-5 top+5'
		});
		
	}, function()
	{
		$(this).removeClass('readytoclose');
		$('#close-cross').remove();	
	});
	
	$(document).on('click', '#close-cross', function()
	{
		$('.readytoclose').hide(100);
		$('#darkbackground').hide();
	});
	
	//Sanftes scrollen zu Anker
	$('a[href*=#]').click(function(event)
	{
		event.preventDefault();
		
		var ziel = $(this).attr("href");

		$('html,body').animate({
			scrollTop: $(ziel).offset().top-180
		}, 500);
	});	
	
	//Zum Anfang scrollen
	$('#top-arrow').click(function()
	{
		$('html,body').animate({
			scrollTop: 0
		}, 500);
	});
	
	//Beim scrollen wird der Pfeil nach oben gezeigt.
	$(document).scroll(function()
	{
		if($(document).scrollTop() >= 500)
		{
			if($('#top-arrow').not(':visible'))
			{
				$('#top-arrow').show(0);
			}
		}
		else
		{
			$('#top-arrow').hide(0);
		}
	}).stop();
	//Wenn der Slider berührt wird, dass wird angezeigt.
	$('#live_tile_slider').hover(function()
	{
		$('#box_content_tiles').show('slide',{direction:'right'});
	});
	
	//Beim Berühren von box_content_text wird der Tiles Container ausgeblendet, außer cc_fixed ist wahr.
	$('#box_content_tiles').mouseleave(function()
	{
		if($.cookie("cc_fix") != "true")
		{
			$('#box_content_tiles').hide('slide',{direction:'right'});
		}
	})
	
	//Beim Klick auf den Benutzernamen wird der Tiles Container ein- bzw. ausgeblendet.
	$('#header-user-info').click(function()
	{
		$('#box_content_tiles').toggle('slide',{direction:'right'});
		$('#cc_fixed').val('true');
		
		if($.cookie("cc_fix") == "true")
		{
			$.removeCookie("cc_fix");
		}
		else
		{
			$.cookie("cc_fix", "true");
		}
	})
	
	if($.cookie("cc_fix") == "true")
	{
		$('#box_content_tiles').show(0);
	}
	
});

	function msgBox(message, title, fokus)
	{
		fokus = fokus || false;
		
		$('#console-content').html(message);
		$('#console-title').html(title);
		
		if(fokus === true)
		{
			$('#darkbackground').show();
		}
			
		$('#console').show();
	}

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