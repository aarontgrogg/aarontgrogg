<?php	
		// Gzip encode the contents of the output buffer.
		function compress_output_option($output) {
		if(strlen($output) >= 1000) {
		
			$compressed_out = "\x1f\x8b\x08\x00\x00\x00\x00\x00";
			$compressed_out .= substr(gzcompress($output, 2), 0, -4);
		
			if(strpos(" ".$_SERVER["HTTP_ACCEPT_ENCODING"], "x-gzip"))	{
				$encoding = "x-gzip";
			}
			if(strpos(" ".$_SERVER["HTTP_ACCEPT_ENCODING"], "gzip")) 	{
				$encoding = "gzip";
			}
		
			header("Content-Encoding: ".$encoding);
			return $compressed_out;
		} else {
			return $output;
		}
		}
		if (strstr($_SERVER["HTTP_ACCEPT_ENCODING"], "gzip") || strstr($_SERVER["HTTP_ACCEPT_ENCODING"], "x-gzip")) {
			if(function_exists("gzcompress")) {
			ob_start("compress_output_option");
			} else {
			ob_start ("ob_gzhandler");
			}		
		}
		?><?php	
				header("Cache-Control: must-revalidate");
				header("Expires: Sat, 21 Dec 2024 14:08:26 GMT");
				?><?php	
				header("Content-type: text/javascript; charset: UTF-8");
				?>
addComment={moveForm:function(d,f,i,c){var m=this,a,h=m.I(d),b=m.I(i),l=m.I("cancel-comment-reply-link"),j=m.I("comment_parent"),k=m.I("comment_post_ID");if(!h||!b||!l||!j){return}m.respondId=i;c=c||false;if(!m.I("wp-temp-form-div")){a=document.createElement("div");a.id="wp-temp-form-div";a.style.display="none";b.parentNode.insertBefore(a,b)}h.parentNode.insertBefore(b,h.nextSibling);if(k&&c){k.value=c}j.value=f;l.style.display="";l.onclick=function(){var n=addComment,e=n.I("wp-temp-form-div"),o=n.I(n.respondId);if(!e||!o){return}n.I("comment_parent").value="0";e.parentNode.insertBefore(o,e);e.parentNode.removeChild(e);this.style.display="none";this.onclick=null;return false};try{m.I("comment").focus()}catch(g){}return false},I:function(a){return document.getElementById(a)}};