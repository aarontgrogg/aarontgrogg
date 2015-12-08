<?php require_once('private.php_speedy.include.head.php') ?>
<?php if($message) { ?><div id="message" class="updated fade"><p><strong><?php echo $message ?></strong></p></div><?php } ?>
<div id="speedy_options">
	<div style="background-color:#e4e4e4;padding-left:5px;padding-bottom:5px">
		<h3>Known Issues</h3>
		Whilst it tries to be a plug-and-play solution PHP Speedy can't (yet) do everything. The following are some issues you should take into account if you are having problems:
		<ul>
			<li>The config.php file and the cache directory must be writable by the server</li>
			<li>PHP Speedy doesn't compress external JavaScript or CSS files. This means your widgets, tracking codes etc will not be compressed</li>			
			<li>PHP Speedy ignores JavaScript of CSS served with any extension other than .js or .css. This is because if the extension is .php or something else, the file is probably dynamic and therefore probably shouldn't be cached</li>									
			<li>If you have a JavaScript that loads other JavaScripts via document.write this may cause problems. The new scripts will be loaded after the entire block of all your other scripts, thereby changing the load order. In this case, you should manually link to each script individually.</li>						
			<li>PHP Speedy ignores querystrings in the links to your JS and CSS files</li>						
			<li>PHP Speedy only sets the expiration for your CSS and JS files. It doesn't (yet) handle the images, which is probably why you won't score an A in Y-Slow for far future expires.</li>									
			<li>Enabling page gzip compression just enables the option in Wordpress (in Options | Reading). PHP Speedy itself does no page gzipping.</li>
			<li>In Wordpress MU, you cannot configure your MU sites for PHP Speedy individually; the same config affects all the sites you have enabled PHP Speedy on. This is a feature (not a bug). You should also turn off PHP Speedy file cleanup if using MU. </li>																							
			<li>PHP Speedy doesn't support the @import syntax for including CSS files</li>
			<li><strong>Don't forget to <a href='<?=$this->base_admin_url?>&sub=activate_deactivate'>activate</a> PHP Speedy</strong> once you have tested the configuration!</li>															
		</ul>
	</div>
	<br />
	<h2>PHP Speedy Configuration</h2>	
	<br />
<form method="post" action="">
	
			<fieldset>
			<legend><?php echo $options['options_file']['title'] ?></legend>
			
			<?php echo $options['options_file']['intro'] ?>
			
			<p>
				<label for="options_file" class="float">File:</label>						
				<?php echo $options['options_file']['value'] ?>
			</p>
			
			<div class="spacer_small" style="height:20px;"></div>
			
			<?php if($config['options_file_writeable']) { ?>
			<span class="success">The file is writable</span>
			<?php } else { ?>
			<div class="error">The file is not writable. Please set the file permissions to 777. Normally this can be done via FTP by navigating to the file, right clicking and changing properties or permissions.</div>
			<?php } ?>
			</fieldset>	

			<fieldset>
			<legend><?php echo $options['cache_dir']['title'] ?></legend>
			
			<?php echo $options['cache_dir']['intro'] ?>
			
			<p>
				<label for="cache_dir" class="float">Directory:</label>						
				<input type="text" name="speedy[cache_dir]" id="speedy__cache_dir" value="<?php echo $options['cache_dir']['value'] ?>" class="textfield" />
			</p>
			
			<div class="spacer_small" style="height:20px;"></div>
			
			<?php if($config['cache_writeable']) { ?>
			<span class="success">The directory is writable</span>
			<?php } else { ?>
			<div class="error">The directory is not writable. Please set the file permissions to 777. Normally this can be done via FTP by navigating to the directory, right clicking and changing properties or permissions.</div>
			<?php } ?>
			</fieldset>		

			<fieldset>
			<legend><?php echo $options['js_libraries']['title'] ?></legend>
			
			<?php if(!empty($GLOBALS['php_speedy_wp']['no_script_loader'])) { ?>
			
				<div class="notice">
					You need Wordpress 2.1 or greater to use this feature
				</div>
							
			<?php } else { ?>
			
				<?php echo $options['js_libraries']['intro'] ?>
				
				<p>
					<label>JS Libraries:</label>	
					<div class="spacer_small" style="height:5px;"></div>		
					
					<?php foreach($js_libraries AS $library) {  ?>
					
						<div class="info" style="background-color:#e4e4e4;border-bottom:2px solid #ffffff;width:400px">
							<label for="cache_dir" class="float" style="font-weight:normal;width:300px"><?php echo $library['wp']->handle ?> (<?php echo $library['wp']->ver ?>)</label>					
							<input name="speedy[js_libraries][]" type="checkbox" value="<?php echo $library['wp']->handle ?>" <? if(strstr($options['js_libraries']['value'],$library['wp']->handle)) {  ?>checked<? } ?> style="margin-top:8px" />
							<div class="spacer_small" style="height:1px;"></div>
						</div>	
						
					<?php } ?>							
					
				</p>
			
			<?php } ?>
			
			<div class="spacer_small" style="height:20px;"></div>
			
			</fieldset>	
			
			<fieldset>
			<legend><?php echo $options['ignore_list']['title'] ?></legend>
			
			<?php echo $options['ignore_list']['intro'] ?>
			
			<p>
				<label for="cache_dir" class="float">Ignore list:</label>						
				<input type="text" name="speedy[ignore_list]" id="speedy__ignore_list" value="<?php echo $options['ignore_list']['value'] ?>" class="textfield" />
			</p>
			
			<div class="spacer_small" style="height:20px;"></div>
			
			</fieldset>						

			<?php foreach($options AS $key=>$type) { 
					if(is_array($type['value'])) {
			?>	
				<fieldset class="spd_options">
					<legend><?php echo $type['title'] ?></legend>
					
					<?php echo $type['intro'] ?>
					<br /><br />
			
						<?php foreach($type['value'] AS $option=>$value) {  ?>
						
						<label><?php echo $key . " " . $option ?></label>
							<div class="info">
							Yes: <input name="speedy[<?php echo $key ?>][<?php echo $option ?>]" type="radio" value="1" <?php if(!empty($value)) { ?>checked<?php } ?> class="radio">
							No: <input name="speedy[<?php echo $key ?>][<?php echo $option ?>]" type="radio" value="0" <?php if(empty($value)) { ?>checked<?php } ?> class="radio">				
							</div>	
							
						<?php } ?>
					
				</fieldset>
			<?php 
				}
			} ?>			

	</p>
	
	<?php if(empty($allow_activation)) { ?>
	<p class="error">		
	Either the options file or the cache directory is not writeable by the server. Please fix this issue before continuing.
	<?php } ?>
	</p>	

	<input type="hidden" name="action" value="update_configuration" />
	<input type="hidden" name="sub" value="page_configure" />	
	<p class="success">	
	Happy with the configuration? Then click the button below to set your options. You should then test your configuration.
	<br />	<br />
	<input type="submit" name="Submit" value="Set Options" />
	</p>
	</form>	
	
<br />
<br />
<a href="http://aciddrop.com" target="_blank" style="border:1px solid #fff"><img src="<?php echo $speedy_lib_path ?>/images/php_speedy_logo_small.gif" style="margin-top:10px" border="0" /></a>
<br />
<br />
<br />	
	
</div>