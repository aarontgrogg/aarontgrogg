<?php require_once('private.php_speedy.include.head.php') ?>
<?php if($message) { ?><div id="message" class="updated fade"><p><strong><?php echo $message ?></strong></p></div><?php } ?>
<div id="speedy_options">
	<h2>PHP Speedy Configuration Test Results</h2>
	<br />

			<fieldset>
			<legend>Scripts</legend>						
			<?php if(!is_array($scripts)) {?>

			PHP Speedy didn't find any JavaScript or CSS

			<?php } else { ?>

			The following scripts were created during the test. Once Speedy is activated, the source script(s) will be replaced by the combined script.

			<?php foreach($scripts AS $key=>$value) { ?>
			<p>
				<div class="success">
					<div class="left_label">Type:</div>
					<div class="right_label"><?php echo $value['type'] ?></div>
					<div class="spacer_small" style="height:1px;"></div>
	
					<div class="left_label">Source script(s)</div>
						<div class="right_label">
							<?php foreach($value['from'] AS $script) { ?>
							<?php echo $script['src'] ?><br />
							<?php } ?>			
						</div>
					<div class="spacer_small" style="height:1px;"></div>					
	
					<div class="left_label">Combined script</div>
						<div class="right_label">
							<?php echo $value['to'] ?><br />
						</div>
					<div class="spacer_small" style="height:1px;"></div>
				</div>
				
			</p>
			<?php }
			} ?>
			
			<div class="spacer_small" style="height:10px;"></div>
			
			<?php if(is_array($skipped)) {?>
			
			The following scripts were skipped during the test. They will be left alone by Speedy once activated.

			<?php foreach($skipped AS $key=>$value) { ?>
			<p>
				<div class="notice">
					<div class="left_label">Reason:</div>
					<div class="right_label"><?php echo $value['reason'] ?></div>
					<div class="spacer_small" style="height:1px;"></div>
	
					<div class="left_label">Script</div>
						<div class="right_label">
							<?php echo $value['from'] ?>
						</div>
					<div class="spacer_small" style="height:1px;"></div>					
	
				</div>
				
			</p>
			<?php }
			} ?>
			
			<div class="spacer_small" style="height:10px;"></div>
			
			<?php if(is_array($notice)) {?>
			
			The following notices were generated:

			<?php foreach($notice AS $key=>$value) { ?>
			<p>
				<div class="notice">
					<div class="left_label">Notice:</div>
					<div class="right_label"><?php echo $value['notice'] ?></div>
					<div class="spacer_small" style="height:1px;"></div>
	
					<div class="left_label">Script</div>
						<div class="right_label">
							<?php echo $value['from'] ?>
						</div>
					<div class="spacer_small" style="height:1px;"></div>					
	
				</div>
				
			</p>
			<?php }
			} ?>			
			
			<div class="spacer_small" style="height:10px;"></div>			
			
			</fieldset>	
			
<br />
<br />
<a href="http://aciddrop.com" target="_blank" style="border:1px solid #fff"><img src="<?php echo $speedy_lib_path ?>/images/php_speedy_logo_small.gif" style="margin-top:10px" border="0" /></a>
<br />
<br />
<br />	
	
</div>