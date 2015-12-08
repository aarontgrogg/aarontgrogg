<?php if(! WsdUtil::canLoad()) { return; } ?>


<div class="">
    <blockquote>
        <p><?php echo __('Your WordPress database contains every post, every comment and every link you have on your blog. If your database gets erased or corrupted, you stand to lose everything you have written. There are many reasons why this could happen and not all are things you can control. But what you can do is <strong>back up your data</strong>.'); ?></p>
        <p style="text-align: center;"><?php echo __('<strong>Please backup your database before using this tool!</strong>');?></p>
        <p style="text-align: right;"><cite><a href="http://codex.wordpress.org/WordPress_Backups" target="_blank">Wordpress</a></cite></p>
    </blockquote>
</div>

<?php
/*
 * Check if the backups directory is writable
 *======================================================
 */
$wsd_bckDirPath = WSS_PLUGIN_DIR.'res/backups/';
if (is_dir($wsd_bckDirPath) && is_writable($wsd_bckDirPath)) :
?>


<?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST')
    {
        if (isset($_POST['wsd_db_backup']))
        {

            if ('' <> ($fname = WsdUtil::backupDatabase())) {
                echo '<p class="acx-info-box">';
					echo '<span>',__('Database successfully backed up!'),'</span>';
					echo '<br/><span>',__('Download backup file'),': </span>';
					echo '<a href="',WSS_PLUGIN_URL.'res/backups/',$fname,'" style="color:#000">',$fname,'</a>';
                echo '</p>';
            }
            else {
                echo '<p class="acx-info-box">';
					echo __('The database could not be backed up!');
					echo '<br/>',__("A possible error might be that you didn't set up writing permissions for the backups directory!");
                echo '</p>';
            }
        }
    }
?>
<div class="acx-section-box">
    <form action="#bckdb" method="post">
        <input type="hidden" name="wsd_db_backup"/>
        <input type="submit" class="button-primary" name="backupDatabaseButton" value="<?php echo __('Backup now!');?>"/>
    </form>
</div>

<?php else : //!! The directory is not writable. Display the info message

	echo '<p class="acx-info-box">';
		printf(__('<strong>Important</strong>: The <code title="%s">backups</code> directory <strong>MUST</strong> be writable in order to use this feature!')
            ,WSS_PLUGIN_DIR.'res/backups');
	echo '</p>';
endif; ?>
