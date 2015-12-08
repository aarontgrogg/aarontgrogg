<?php if(! WsdUtil::canLoad()) { return; } ?>
<?php if(! WsdUtil::isAdministrator()) { return; } ?>
<?php
$rm = strtoupper($_SERVER['REQUEST_METHOD']);
//#! get settings
$settings = WsdPlugin::getSettings();

$rssWidgetData = get_option('WSD-RSS-WGT-DISPLAY');
$rssWidgetChecked = false;
if(!empty($rssWidgetData) && ($rssWidgetData == 'yes')){
    $rssWidgetChecked = true;
}

if('POST' == $rm)
{
    // check nonce
    if(isset($_POST['wsdplugin_update_settings_field'])){
        if(!wp_verify_nonce($_POST['wsdplugin_update_settings_field'],'wsdplugin_update_settings')){
            wp_die(__('Invalid request.'));
        }
    }
    else {wp_die(__('Invalid request.'));}

    //#! UPDATE SETTINGS
    if(isset($_POST['updateSettingsButton']))
    {
        // validate and save settings
        $postData = $_POST['chk_options'];
        parse_str($postData, $checkboxes);

        if(empty($checkboxes)){
            foreach($settings as &$entry){
                $entry['value'] = 0;
            }
        }
        else {
            foreach($checkboxes as $method => $value){
                $settings[$method]['value'] = $value;
            }
        }

        // rss widget
        if(empty($_POST['chk_rss_wgt_display'])){
            // hide
            $rssWidgetChecked = false;
            update_option('WSD-RSS-WGT-DISPLAY', 'no');
        }
        else {
            // show
            $rssWidgetChecked = true;
            update_option('WSD-RSS-WGT-DISPLAY', 'yes');
        }

        update_option(WSS_PLUGIN_SETTINGS_OPTION_NAME, $settings);
        $settings = get_option(WSS_PLUGIN_SETTINGS_OPTION_NAME);
    }
    //#! DELETE OPTIONS
    elseif(isset($_POST['deleteRssDataButton']))
    {
        delete_option('wsd_feed_data');
    }
}
?>
<div class="wrap wsdplugin_content">
    <h2><?php echo WSS_PLUGIN_NAME.' - '. __('Settings');?></h2>

    <p class="clear"></p>
    <div style="clear: both; display: block;">
        <div class="metabox-holder">
            <div class="inner-sidebar1 postbox">
                <h3 class="hndle" style="cursor: default;"><span><?php echo __('Settings');?></span></h3>
                <div class="inside acx-section-box" style="padding-left:0;">
                    <form method="post">
                        <?php wp_nonce_field('wsdplugin_update_settings','wsdplugin_update_settings_field'); ?>
                        <?php
                            $i = 0;
                            foreach($settings as $k => $entry){
                                if(is_array($entry))
                                {
                                    $chkID = "chk-$i";
                                    echo '<div class="acx-section-box wsdplugin-overflow">';
                                    echo '<label for="'.$chkID.'" class="wsdplugin-overflow">';
                                    echo '<span class="chk-settings wsdplugin_checkbox'.($entry['value'] ? ' wsdplugin_checkbox-active' : '').'" id="'.$chkID.'" data-bind="'.$entry['name'].'"><a>&nbsp;</a></span>';
                                    echo '<span>'.$entry['desc'].'</span>';
                                    echo '</label>';
                                    echo '</div>';
                                    $i++;
                                }
                            }
                        ?>
                        <?php
                        // the rss dashboard widget
                        // all the below settings must have present the chk-extra class
                        echo '<div class="acx-section-box wsdplugin-overflow">';
                            echo '<label for="wsd_feed_data" class="wsdplugin-overflow">';
                            echo '<span class="chk-extra wsdplugin_checkbox'.($rssWidgetChecked ? ' wsdplugin_checkbox-active' : '').'" id="wsd_feed_data"><a>&nbsp;</a></span>';
                            echo '<span>'.__('Show the RSS widget in the dashboard').'</span>';
                            echo '</label>';
                        echo '</div>';
                        ?>

                        <input type="hidden" name="chk_options" id="chk_options" />
                        <input type="hidden" name="chk_rss_wgt_display" id="chk_rss_wgt_display" />
                        <div class="acx-section-box wsdplugin-overflow">
                            <input type="button" id="_resetButton" class="button button-secondary" style="width: 70px;"/>
                            <input type="submit" value="Update settings" class="button button-primary" name="updateSettingsButton"/>
                            <input type="submit" value="Delete rss data" class="button button-primary" name="deleteRssDataButton"/>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
<script type="text/javascript">
    jQuery(document).ready(function($){
        var resetButton = $('#_resetButton');
        var oneChecked = false;
        var checkboxes = $('.wsdplugin_checkbox');
        var entriesLiveTrafficInput = $('#max_number_live_traffic');

        // update state + bind click listeners
        $.each(checkboxes, function(i,v){
            var self = $(v);
            if(self.hasClass('wsdplugin_checkbox-active')){
                oneChecked = true;
            }
            self.parent('label').on('click', function(){
                if(self.hasClass('wsdplugin_checkbox-active')){
                    self.removeClass('wsdplugin_checkbox-active');
                }
                else { self.addClass('wsdplugin_checkbox-active'); }
            });
        });

        // update reset button
        if(oneChecked){ resetButton.val('Clear all'); }
        else { resetButton.val('Select all'); }

        resetButton.click(function(){
            $(this).text(function(i, text){
                if($(this).val() == 'Clear all'){
                    $.each(checkboxes,function(i,v){
                        $(v).removeClass('wsdplugin_checkbox-active');
                    });
                    $(this).val('Select all');
                }
                else {
                    $.each(checkboxes,function(i,v){
                        $(v).addClass('wsdplugin_checkbox-active',true);
                    });
                    $(this).val('Clear all');
                }
            });
        });

        $('form').submit(function(){
            $('#chk_options').val('');
            var data = $('.chk-settings').map(function(){
                var self = $(this);
                return {name: self.attr('data-bind'), value: self.hasClass('wsdplugin_checkbox-active')?1:0};
            }).get();
            $('#chk_options').val($.param(data));
            $('#chk_rss_wgt_display').val($('.chk-extra').hasClass('wsdplugin_checkbox-active')?1:0);
        });
    });
</script>
