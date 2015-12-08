<?php if(! WsdUtil::canLoad()) { return; }
/*
 * Displays the File scan results info
 */
	global $acxFileList;
?>
<?php
	$acx_isPostBack = false;
	$acx_message = '';

	//@ IF POSTBACK
    if(! WsdUtil::isWinOs()){
        if ($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            // check nonce
            if(isset($_POST['wsdplugin_update_paths_field'])){
                if(!wp_verify_nonce($_POST['wsdplugin_update_paths_field'],'wsdplugin_update_paths')){
                    wp_die(__('Invalid request.'));
                }
            }
            else {wp_die(__('Invalid request.'));}

            $acx_isPostBack = true;

            $result = WsdUtil::changeFilePermissions($acxFileList);

            if (empty($result)) {
                $acx_message = __('No changes applied. You are running PHP on a Windows server thus chmod cannot be used');
            }
            else { $acx_message = __('Successful changes').': '.$result['success'].', '.__('Failed').': '.$result['failed']; }
        }
	}
?>
<?php
//@ Check the files
if (empty($acxFileList)) {
	echo __('There are currently no files set for scanning!');
}
else
{
    echo '<form method="post">';
    wp_nonce_field('wsdplugin_update_paths','wsdplugin_update_paths_field');
	echo '<table class="widefat acx-table" cellpadding="0" cellspacing="0">';
		echo '<thead class="widget-top">';
			echo '<tr>';
                echo '<td></td>';
				echo '<td><strong>',__('Name'),'</strong></td>';
				echo '<td><strong>',__('Path'),'</strong></td>';
				echo '<td><strong>',__('Current permissions'),'</strong></td>';
				echo '<td><strong>',__('Suggested permissions'),'</strong></td>';
			echo '</tr>';
		echo '</thead>';
		echo '<tbody>';
	foreach($acxFileList as $fileName => $v)
	{
		$filePath = $v['filePath'];
		$p = WsdUtil::getFilePermissions($filePath);
		$sp = $v['suggestedPermissions'];

		$cssClass = ((octdec($p) == octdec($sp)) ? 'success' : 'error');

		echo '<tr>';
            echo '<td class="td_'.$cssClass.'"></td>';
			echo '<td>',$fileName,'</td>';
			echo '<td>',(empty($filePath) ? 'Not Found' : WsdUtil::normalizePath($filePath)),'</td>';
            //@ Current
			if ($p > octdec('0')) {
				echo '<td>',$p,'</td>';
			}
			else { echo '<td>',__('Not Found'),'</td>'; }

            //@ Suggested
            if (file_exists($filePath))
            {
                echo '<td>',$sp,'</td>';
            }
            else
            {
                if (is_file($filePath)) {
                    echo '<td>0644</td>';
                }
                elseif (is_dir($filePath)) { echo '<td class="center">0755</td>'; }
                else {
                    echo '<td>',$sp,'</td>';
                }
            }
		echo '</tr>';
	}
		echo '</tbody>';
	echo '</table>';

    if(! WsdUtil::isWinOs()){
        echo '<div class="wsdplugin-overflow"><p style="text-align: right; clear: both; margin: 7px 0 0 0;" class="wsdplugin-overflow">';
        //@@ Display action result
        if ($acx_isPostBack && !empty($acx_message)){
            echo '<p class="acx-info-box" style="float: left; width: 70%; margin: 0 0; padding-top: 3px; padding-bottom: 3px;">'.$acx_message.'</p>';
        }
        echo '<input type="submit" value="Apply suggested permissions" class="button-primary" style="float: right;" /></div>';
    }
    echo '</form>';

    echo '<p class="acx-info-box" style="margin: 7px 0 7px 0;">';
        echo __('Our suggested permissions are still secure but more permissive in order not to break some servers\' setups.
            If your existent file permissions are more restrictive, ex: 0750 instead of the suggested 0755 then you have no reason to
            change it to the suggested 0755 permissions.');
    echo '</p>';
}
?>