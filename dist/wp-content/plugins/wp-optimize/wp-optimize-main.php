<?php
# --------------------------------------- #
# prevent file from being accessed directly
# --------------------------------------- #
if ( ! defined( 'WPINC' ) ) {
	die;
}

$text = '';

if (isset($_POST["clean-revisions"])) {
    $text .= wpo_cleanUpSystem('revisions');
    }

        if ( isset( $_POST["clean-revisions"] ) ) {
            $user_options["user-revisions"] = TRUE;
        } else {
            $user_options["user-revisions"] = FALSE;
        }


if (isset($_POST["clean-autodraft"])) {
    $text .= wpo_cleanUpSystem('autodraft');


    //$user_options["user-drafts"] = $user_options["user-drafts"] ? 'true' : 'false';
    }

        if ( isset( $_POST["clean-autodraft"] ) ) {
        $user_options["user-drafts"] = TRUE;
        } else {
        $user_options["user-drafts"] = FALSE;
        }

if (isset($_POST["clean-comments"])) {
    $text .= wpo_cleanUpSystem('spam');
    }

        if ( isset( $_POST["clean-comments"] ) ) {
        $user_options["user-spams"] = TRUE;
        } else {
        $user_options["user-spams"] = FALSE;
        }


if (isset($_POST["unapproved-comments"])) {
    $text .= wpo_cleanUpSystem('unapproved');
    }

        if ( isset( $_POST["unapproved-comments"] ) ) {
        $user_options["user-unapproved"] = TRUE;
        } else {
        $user_options["user-unapproved"] = FALSE;
        }

if (isset($_POST["clean-pingbacks"])) {
    $text .= wpo_cleanUpSystem('pingbacks');
    }
if (isset($_POST["clean-trackbacks"])) {
    $text .= wpo_cleanUpSystem('trackbacks');
    }

if (isset($_POST["clean-transient"])) {
    $text .= wpo_cleanUpSystem('transient_options');
    }

if (isset($_POST["clean-postmeta"])) {
    $text .= wpo_cleanUpSystem('postmeta');
    }

if (isset($_POST["clean-tags"])) {
    $text .= wpo_cleanUpSystem('tags');
    }

if (isset($_POST["optimize-db"])) {
    $text .= DB_NAME.' '.__('Database Optimized!', 'wp-optimize').'<br>';
    }
        if ( isset( $_POST["optimize-db"] ) ) {
        $user_options["user-optimize"] = TRUE;
        } else {
        $user_options["user-optimize"] = FALSE;
        }

if (isset($_POST["wp-optimize"])) {
    update_option( 'wp-optimize-settings', $user_options );
}


if ($text !==''){
     echo '<div id="message" class="updated">';
     echo '<strong>'.$text.'</strong></div>';
    }

?>



<?php
Function optimizeTablesQuick($Optimize){
    global $wpdb;
    $total_gain = 0;
	$row_usage = 0;
	$data_usage = 0;
	$index_usage = 0;
	$overhead_usage = 0;
	$tablesstatus = $wpdb->get_results("SHOW TABLE STATUS");

    foreach($tablesstatus as  $tablestatus) {

        $row_usage += $tablestatus->Rows;
        $data_usage += $tablestatus->Data_length;
        $index_usage +=  $tablestatus->Index_length;
            if ($tablestatus->Engine != 'InnoDB'){
            $overhead_usage += $tablestatus->Data_free;
            $total_gain += $tablestatus->Data_free;
            }
        }

    if ($Optimize == "yes" ){
    //if ($tablestatus->Engine != 'InnoDB'){
    $tables = $wpdb->get_col("SHOW TABLES");
    foreach($tables as $table_name) {
    $local_query = 'OPTIMIZE TABLE `'.$table_name.'`';
    wpo_debugLog('optimizing .... '.$table_name);
    $result_query  = $wpdb->query($local_query);
    } //end for

    wpo_updateTotalCleaned(strval($total_gain));

			// #TODO for testing only
            // $thistime = current_time( "timestamp", 0 );
            // $thedate = gmdate(get_option('date_format') . ' ' . get_option('time_format'), $thistime );
			// list($part3, $part4) = wpo_getCurrentDBSize();
			// wpo_sendEmail($thedate, strval($part4)); //TODO TEMPORARY - send email to admin

            // wpo_debugLog('Invoked+ Updating options with value +'.$part4);

    wpo_debugLog('Total Gain .... '.strval($total_gain));
    } //end if
    //
// clear up total gain values
   $total_gain = 0;

} // end of function

?>



<div class="wpo_section wpo_group">
<form action="#" method="post" enctype="multipart/form-data" name="optimize_form" id="optimize_form">
	<div class="wpo_col wpo_span_1_of_3">
	<div class="postbox">
	<!-- <h3 class="hndle"><?php _e('Clean-up options', 'wp-optimize'); ?></h3> -->
		<div class="inside">
		<h3><?php _e('Clean-up options', 'wp-optimize');
                    $wpo_user_selection = get_option( 'wp-optimize-settings' );
                    //var_dump($wpo_user_selection);
                ?></h3>
		<p>
		<label>
		<input name="clean-revisions" id="clean-revisions" type="checkbox" value="true" <?php echo $wpo_user_selection['user-revisions'] == 'true' ? 'checked="checked"':''; ?> />
		<?php
	    if ( get_option( OPTION_NAME_RETENTION_ENABLED, 'false' ) == 'true' ) {
		_e('Clean post revisions which are older than ', 'wp-optimize');
		echo get_option( OPTION_NAME_RETENTION_PERIOD, '2' );
		echo ' ';
        _e('weeks', 'wp-optimize');
		} else {
		_e('Clean all post revisions', 'wp-optimize');
		}
		?>
		</label>
		<br />
		<small>&nbsp;&nbsp;<?php _e(wpo_getInfo('revisions'), 'wp-optimize'); ?></small>
		</p>

		<p>
			<label>
			<input name="clean-autodraft" id="clean-autodraft" type="checkbox" value="true" <?php echo $wpo_user_selection['user-drafts'] == 'true' ? 'checked="checked"':''; ?> />
		<?php
	    if ( get_option( OPTION_NAME_RETENTION_ENABLED, 'false' ) == 'true' ) {
		_e('Clean auto draft posts which are older than ', 'wp-optimize');
		echo get_option( OPTION_NAME_RETENTION_PERIOD, '2' );
		echo ' ';
		_e('weeks', 'wp-optimize');
		} else {
		_e('Clean all auto draft posts and posts in trash', 'wp-optimize');
		}
	  ?>
			</label>

		<br />
		<small>&nbsp;&nbsp;<?php _e(wpo_getInfo('autodraft'), 'wp-optimize'); ?></small>
		</p>


		<p>
			<label>
			<input name="clean-comments" id="clean-comments" type="checkbox" value="true" <?php echo $wpo_user_selection['user-spams'] == 'true' ? 'checked="checked"':''; ?> />
		<?php
	    if ( get_option( OPTION_NAME_RETENTION_ENABLED, 'false' ) == 'true' ) {
		_e('Remove spam comments which are older than ', 'wp-optimize');
		echo get_option( OPTION_NAME_RETENTION_PERIOD, '2' );
		echo ' ';
		_e('weeks', 'wp-optimize');
		} else {
		_e('Remove spam comments and comments in trash', 'wp-optimize');
		}

		?>
		</label>
		<br />
		<small>&nbsp;&nbsp;<?php _e(wpo_getInfo('spam'), 'wp-optimize'); ?></small>
		</p>

		<p>
			<label>
		<input name="unapproved-comments" id="unapproved-comments" type="checkbox" value="true" <?php echo $wpo_user_selection['user-unapproved'] == 'true' ? 'checked="checked"':''; ?> />
		<?php
	    if ( get_option( OPTION_NAME_RETENTION_ENABLED, 'false' ) == 'true' ) {
		_e('Remove unapproved comments which are older than ', 'wp-optimize');
		echo get_option( OPTION_NAME_RETENTION_PERIOD, '2' );
		echo ' ';
		_e('weeks', 'wp-optimize');
		} else {
		_e('Remove unapproved comments', 'wp-optimize');
		}

		?>
		</label>
		<br />
		<small>&nbsp;&nbsp;<?php _e(wpo_getInfo('unapproved'), 'wp-optimize'); ?></small>
		</p>

		<p>
			<label>
			<input name="clean-transient" id="clean-transient" type="checkbox" value="" />
			<span style="color: red;">
			<?php _e('Remove transient options', 'wp-optimize'); ?>
			</span>
			</label>
		<br />
		<small>&nbsp;&nbsp;<?php _e(wpo_getInfo('transient_options'), 'wp-optimize'); ?></small>
		</p>

		<p>
			<label>
			<input name="clean-pingbacks" id="clean-pingbacks" type="checkbox" value="" />
			 <span style="color: red;">
			 <?php _e('Remove pingbacks', 'wp-optimize'); ?>
			 </span>
			</label>
		<br />
		<small>&nbsp;&nbsp;<?php _e(wpo_getInfo('pingbacks'), 'wp-optimize'); ?></small>
		</p>

		<p>
			<label>
			<input name="clean-trackbacks" id="clean-trackbacks" type="checkbox" value="" />
			 <span style="color: red;">
			 <?php _e('Remove trackbacks', 'wp-optimize'); ?>
			 </span>
			</label>
		<br />
		<small>&nbsp;&nbsp;<?php _e(wpo_getInfo('trackbacks'), 'wp-optimize'); ?></small>
		</p>

		<p>
			<?php
			echo '<small>';
			_e('Do not select RED marked items unless you really need to use them', 'wp-optimize');
			echo '</small>';
			?>
		</p>
<p>
    <a href="http://bit.ly/youtube-wordpress" target="_blank">
    <img src="<?php echo WPO_PLUGIN_URL ;?>embedplus.png" style="float: left;margin-right: 7px;"> <?php _e('Optimize your YouTube embedding in WordPress with this plugin', 'wp-optimize'); ?> &raquo;</a>
  </p>
		</div>
		</div>
	 </div>

	<div class="wpo_col wpo_span_1_of_3">
	<div class="postbox">
	<!-- <h3 class="hndle"><span>Actions</span></h3> -->
		<div class="inside">
		<h3><?php _e('Actions', 'wp-optimize'); ?></h3>
		<p>
			<label>

	 <?php
        echo '<input name="optimize-db" id="optimize-db" type="checkbox" value="true" ';
        echo $wpo_user_selection['user-optimize'] == 'true' ? 'checked="checked"':'';
        echo ' />';
        echo '<b>&nbsp;';
        _e('Optimize database tables', 'wp-optimize');
        echo '</b>';

	 ?>
			 </label>
		</p>

		<p><small>
                    <b><?php _e('Note:', 'wp-optimize'); ?></b> &nbsp;
			<?php

			_e('InnoDB tables will not be optimized', 'wp-optimize');

			?>

		</small></p>
		<p><small>
			<!-- <span style="text-align:center;"><a href="#" onClick="javascript:SetDefaults();"><?php _e('Select safe options', 'wp-optimize'); ?></a></span> -->
			<b><?php _e('Warning:', 'wp-optimize'); ?></b><br />
			<?php _e('Always make a backup of your DB when you upgrade to major versions', 'wp-optimize'); ?>

		</small></p>
		<p>

			<input class="wpo_primary_big" type="submit" id="wp-optimize" name="wp-optimize" value="<?php _e('PROCESS', 'wp-optimize'); ?>" />

                </p>

		<p>

                        <a class="button-primary" href="<?php echo WPO_PAYPAL ; ?>" title="<?php _e('Please donate! It really helps me keep improvising', 'wp-optimize'); ?>" target="_blank"><?php _e('Donate!', 'wp-optimize'); ?></a>
                        <a class="button-primary" href="http://wordpress.org/support/view/plugin-reviews/wp-optimize?rate=5#postform" target="_blank" title="<?php _e('Please give a proper rating:)', 'wp-optimize'); ?>">
                        <?php _e('Rating', 'wp-optimize'); ?>
                        </a>

		</p>

<h3><?php _e('Status log: ', 'wp-optimize'); ?></h3>

<?php
    $news_items = wpo_readFeed('http://ruhanirabin.github.io/WP-Optimize/feed.xml', 1);

    foreach ( $news_items as $item ) : ?>
    <p>
        <b> <a href="<?php echo $item->get_link(); ?>" title="<?php echo $item->get_title(); ?>" target="_blank"><?php echo $item->get_title(); ?></a> </b>
    </p>
    <?php endforeach; ?>

   <?php
	$lastopt = get_option(OPTION_NAME_LAST_OPT, 'Never');
	if ($lastopt !== 'Never'){
		echo '<i>';
		_e('Last automatic optimization was at', 'wp-optimize');
		echo ' ';
		echo '</i>';
		echo '<b>';
		echo '<font color="green">';
		echo $lastopt;
		echo '</font>';
		echo '</b>';
		echo '</i>';

	} else {
		echo '<i>';
		_e('There was no automatic optimization', 'wp-optimize');
		echo ' - ';
		//echo '<a href="?page=WP-Optimize&tab=wp_optimize_settings">';
		//_e('Check settings', 'wp-optimize');
		//echo '</a>';
		echo '</i>';
	}
   ?>
    <br />

	<?php
	if ( get_option( OPTION_NAME_SCHEDULE, 'false' ) == 'true' ) {
		echo '<b>';
		echo '<i>';
		echo '<font color="green">';
		_e('Scheduled cleaning enabled', 'wp-optimize');
		echo ', ';
		echo '</font>';
		echo '</i>';
		echo '</b>';
		if (wp_next_scheduled('wpo_cron_event2')) {
			 //$timestamp = wp_next_scheduled( 'wpo_cron_event2' );
			wpo_cron_activate();

			 $timestamp = wp_next_scheduled( 'wpo_cron_event2' );
			 $date = new DateTime("@$timestamp");
			echo '<i>';
			_e('Next schedule', 'wp-optimize');
			echo ' : ';
			echo '<font color="green">';
			//echo $date->format('l jS \of F Y') . "\n";
			//echo gmdate(get_option('date_format') . ' ' . get_option('time_format'), $timestamp + (get_option('gmt_offset')));
			echo gmdate(get_option('date_format') . ' ' . get_option('time_format'), $timestamp );
			echo '</i>';
			echo '</font>';
			echo '<i>';
			echo ' - ';
			echo '<a href="?page=WP-Optimize">';
			_e('Refresh', 'wp-optimize');
			echo '</a>';
			echo '</i>';
            //echo $timestamp;

		 }
	} else {
		echo '<b>';
		echo '<i>';
		_e('Scheduled cleaning disabled', 'wp-optimize');
		echo ' - ';
		echo '<a href="?page=WP-Optimize&tab=wp_optimize_settings">';
		_e('Check settings', 'wp-optimize');
		echo '</a>';
		echo '</i>';
		echo '</b>';
	}
		echo '<br />';

	if ( get_option( OPTION_NAME_RETENTION_ENABLED, 'false' ) == 'true' ) {
		echo '<i>';
		echo '<b>';
		echo '<font color="blue">';
		_e('Keeping last ', 'wp-optimize');
		echo get_option( OPTION_NAME_RETENTION_PERIOD, '2' );
        echo ' ';
		_e('weeks data', 'wp-optimize');
		echo '</font>';
		echo '</i>';
		echo '</b>';

	} else {
		echo '<i>';
		echo '<b>';
		_e('Not keeping recent data', 'wp-optimize');
		echo ' - ';
		echo '<a href="?page=WP-Optimize&tab=wp_optimize_settings">';
		_e('Check settings', 'wp-optimize');
		echo '</a>';
		echo '</i>';
		echo '</b>';
	}

	?>
	<br />

	<h4>
	<?php

	if (isset($_POST["optimize-db"])) {
		list ($part1, $part2) = wpo_getCurrentDBSize();
		_e('Current database size : ', 'wp-optimize');
		echo '<font color="blue">';
		echo $part1.'</font> ';

                echo ' <br />';
    		_e('You have saved', 'wp-optimize');
    		echo ' : ';
    		echo '<font color="blue">';
    		echo $part2.'</font> ';
        }
	else {
		list ($part1, $part2) = wpo_getCurrentDBSize();
 		_e('Current database size', 'wp-optimize');
		echo ' : ';
		echo '<font color="blue">';
		echo $part1.'</font> ';
                $this_value = $part2;

            if ($this_value > 0){
                echo ' <br />';
        		_e('You can save almost', 'wp-optimize');
        		echo ' : ';
        		echo '<font color="red">';
        		echo $part2.'</font> ';
            }
	}

	?>
	</h4>
	<?php


    $total_cleaned = get_option(OPTION_NAME_TOTAL_CLEANED);
    $total_cleaned_num = floatval($total_cleaned);

        if ($total_cleaned_num  > 0){
            echo '<h5>';
            _e('Total clean up overall','wp-optimize');
            echo ': ';
            echo '<font color="green">';
            echo wpo_format_size($total_cleaned);
            echo '</font>';
            echo '</h5>';


        }
	?>

		</div>
	</div>
	</div>
	<div class="wpo_col wpo_span_1_of_3">

		<p>
			<?php wpo_headerImage(); ?>
		</p>

		<p>
			<?php _e('Sponsor','wp-optimize')?></small><br><a href="http://j.mp/1ePlbvc" target="_blank"><img style="border:0px" src="<?php echo WPO_PLUGIN_URL ;?>elegantthemes_sm.png" width="310" height="auto" alt=""></a>
		</p>

	</div>
</div>

<!-- TODO: Need to make this checkbox selection thing persistent -->

<script type="text/javascript">
function SetDefaults() {
    document.getElementById("clean-revisions").checked = true;
    document.getElementById("clean-comments").checked = true;
    document.getElementById("clean-autodraft").checked = true;
    document.getElementById("optimize-db").checked = true;

    return false;
}
</script>


<!--<script>
SetDefaults();
</script>-->

<?php
if (isset($_POST["optimize-db"])) {
 		optimizeTablesQuick("yes");
		}
	else optimizeTablesQuick("no");
?>
