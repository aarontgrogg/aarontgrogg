<?php
if(empty($_GET['compress']) || $_GET['compress'] != "no") {
require('../php_speedy.php');
//$compressor->ignore('sound,builder');
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Compress Me Test</title>

<script src="../libs/js/prototype.js" type="text/javascript"></script>
<script src="../libs/js/builder.js" type="text/javascript"></script>
<script src="../libs/js/effects.js" type="text/javascript"></script>
<script src="../libs/js/dragdrop.js" type="text/javascript"></script>
<script src="../libs/js/controls.js" type="text/javascript"></script>
<script src="../libs/js/slider.js" type="text/javascript"></script> 
<script src="../libs/js/sound.js" type="text/javascript"></script> 

<script src="/dont_touch_me/ever.php" type="text/javascript"></script> 
<script src="/ignore_me/?js" type="text/javascript"></script> 

<link href="../libs/css/forms.css" rel="stylesheet" type="text/css" />
<link href="../libs/css/grid.css" rel="stylesheet" type="text/css" />
<link href="../libs/css/ie.css" rel="stylesheet" type="text/css" />
<link href="../libs/css/reset.css" rel="stylesheet" type="text/css" />
<link href="../libs/css/typography.css" rel="stylesheet" type="text/css" />

<link href="../libs/css/print.css" rel="stylesheet" type="text/css" media="print" />
<link href="../libs/css/reset.css" rel="stylesheet" type="text/css" media="print" />

<link href="../libs/css/typography.css" rel="alternate" type="text/css" media="handheld" />
<link href="../libs/css/typography.css" rel="alternate" type="text/css" media="print" />
<link href="../libs/css/print.css" rel="alternate" type="text/css" media="print" />

<style type="text/css">
<!--
body { 
text-align:left;
font-family:Verdana, Arial, Helvetica, sans-serif;
margin-left:10px;
}

h1,h2,h3 {
font-family:Georgia, "Times New Roman", Times, serif;
margin-bottom:10px;
}
-->
</style>
</head>
<body>
		<h1>How to speed up your website</h1>
		
		<p>This is a test page from <a href="http://aciddrop.com/php-speedy">PHP Speedy</a></p>
		
		<h2>Is the JavaScript still working?</h2>
		
		<h3>Standard Horizontal Slider</h3> 
		<div id="track1" style="width:200px; background-color:#ccc; height:10px;">
			<div id="handle1" style="width:10px; height:15px; background-color:#f00; cursor:move;"></div>
		</div>
		<p id="debug1">&nbsp;</p>		
		

		<h2>Is the CSS still working?</h2>
		
		<p class="error">error</p>
		<p class="highlight">highlight</p>
		<p class="notice">notice</p>
		<p class="removed">removed</p>						
		
		<?php if(empty($_GET['no_images'])) { ?>
		
		<h2>What were the speed gains?</h2>
		<p>(Note the test were done before adding these images)</p>		
		
		<h3>This page with Firebug, before compression</h3>
		<img src="uncompressed.gif" alt="Firebug before compression" />		
				
		<h3>This page with Firebug, after compression</h3>
		<img src="compressed.gif" alt="Firebug before compression" />	
		
		<h3>This page with Yslow, before compression</h3>
		<img src="uncompressed_yslow.gif" alt="Firebug before compression" />	
		
		<h3>This page with Yslow, after compression</h3>
		<img src="compressed_yslow.gif" alt="Firebug before compression" />				
		
		<?php } ?>



</body>
<script type="text/javascript" language="javascript">
// horizontal slider control
new Control.Slider('handle1', 'track1', {
	onSlide: function(v) { $('debug1').innerHTML = 'slide: ' + v },
	onChange: function(v) { $('debug1').innerHTML = 'changed: ' + v }
});	
</script>
</html>
<?php
if(empty($_GET['compress']) || $_GET['compress'] != "no") {
$compressor->finish();
}
?>