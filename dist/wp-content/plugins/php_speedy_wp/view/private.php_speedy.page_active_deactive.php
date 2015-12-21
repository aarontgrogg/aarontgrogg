<?php require_once('private.php_speedy.include.head.php') ?>
<?php if($message) { ?><div id="message" class="updated fade"><p><strong><?php echo $message ?></strong></p></div><?php } ?>
<div id="speedy_options">
	<h2>PHP Speedy <?php echo $title ?></h2>
	<br />

			<fieldset>
			<legend><?php echo $title ?></legend>						
			<p>
				<form method="post" action="">
				<input type="hidden" name="action" value="<?php echo $action ?>" />
				<input type="hidden" name="sub" value="activate_deactivate" />	
				<p class="notice">
				<?php echo $intro ?>
				<br />	<br />
				<input type="submit" name="Submit" value="<?php echo $title ?>" />
				</p>
				</form>								
			</p>
			</fieldset>
			
<br />
<br />
<a href="http://aciddrop.com" target="_blank" style="border:1px solid #fff"><img src="<?php echo $speedy_lib_path ?>/images/php_speedy_logo_small.gif" style="margin-top:10px" border="0" /></a>
<br />
<br />
<br />	
	
</div>