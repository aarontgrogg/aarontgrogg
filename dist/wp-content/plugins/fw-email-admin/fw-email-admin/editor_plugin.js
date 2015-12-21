(function(){
	function attachcss() {
		var w = window.innerWidth-20, // window - scrollbar
			m = w/2, // get mid-point
			l = m - 310, // subtract half of overlay width (10 + 600 + 10)
			h = '<style>';
		h += '#email-action{float:right;margin-top:10px;}';
		h += '#email-iframe-div{position:absolute;top:50px;left:'+l+'px;z-index:100;padding:10px;background:rgba(0,0,0,.6);}';
		h += '#email-iframe-close{float:right;font:bold 14px/2 Arial,sans-serif;color:#fff;cursor:pointer;}';
		h += '#email-iframe{float:left;clear:both;width:600px;height:600px;}';
		h += '</style>';
		jQuery('body:first').append(h);
	}
	function attachbutton() {
		jQuery('<div id="email-action"><input type="button" value="Email Post" id="email-post" class="button-primary" name="email-post" style="float:right;" /></div>').insertAfter('#publishing-action');
		attachopen();
	}
	function attachopen() {
		jQuery('#email-post').click(function() {
			var id = String(document.location).split('post=')[1].split('&')[0],
				h = '',
				src = (location.href.match('localhost')) ? 'localhost/aarontgrogg' : 'aarontgrogg.com';
			h += '<div id="email-iframe-div"><a id="email-iframe-close">Close</a>';

			h += '<iframe id="email-iframe" src="http://'+src+'/firstwednesday/send-post-as-email/?post='+id+'"></iframe>';
			h += '</div>';
			jQuery('body:first').append(h);
			attachclose();
		});
	}
	function attachclose() {
		jQuery('#email-iframe-close').click(function() {
			jQuery('#email-iframe-div').remove();
		});
	}
	attachcss();
	attachbutton();
})();