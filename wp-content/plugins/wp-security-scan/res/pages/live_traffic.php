<?php if(! WsdUtil::canLoad()) { return; } ?>
<?php if(! WsdUtil::isAdministrator()) { return; } ?>
<?php
// flag for getting data
$forceLoad = 1;
$refreshRates = array(0, 5, 10, 15, 20, 25, 30);
$settings = WsdPlugin::getSettings();
?>
<?php
$rm = strtoupper($_SERVER['REQUEST_METHOD']);
if('POST' == $rm)
{
    // check nonce
    if(isset($_POST['wsdplugin_update_settings_field'])){
        if(!wp_verify_nonce($_POST['wsdplugin_update_settings_field'],'wsdplugin_update_settings')){
            exit('Invalid request.');
        }
    }
    else {exit('Invalid request.');}

    function wsdPluginValidateSettingsForm($settings, $refreshRates)
    {
        if(isset($_POST['max_number_live_traffic']) && isset($_POST['refreshRateOption']))
        {
            // validate input $_POST['max_number_live_traffic']
            $keepNumEntriesLiveTraffic = intval($_POST['max_number_live_traffic']);
            if($keepNumEntriesLiveTraffic == 0){
                $keepNumEntriesLiveTraffic = 0;
            }
            elseif(! preg_match("/[0-9]{1,5}/", $keepNumEntriesLiveTraffic)){
                $keepNumEntriesLiveTraffic = 500;
            }

            // validate input $_POST['refreshRateOption']
            $liveTrafficRefreshRateAjax = intval($_POST['refreshRateOption']);
            if(! in_array($liveTrafficRefreshRateAjax, $refreshRates)){
                $liveTrafficRefreshRateAjax = 10;
            }
            elseif($_POST['refreshRateOption'] == 0){
                $liveTrafficRefreshRateAjax = 0;
            }
            elseif(! preg_match("/[0-9]{1,2}/", $liveTrafficRefreshRateAjax)){
                $liveTrafficRefreshRateAjax = 10;
            }

            $settings['keepNumEntriesLiveTraffic'] = $keepNumEntriesLiveTraffic;
            $settings['liveTrafficRefreshRateAjax'] = $liveTrafficRefreshRateAjax;

            // update settings
            update_option(WSS_PLUGIN_SETTINGS_OPTION_NAME, $settings);

            return $settings;
        }
        else { exit('Invalid request.');  }
    }


    // check form
    if(isset($_POST['updateSettingsButton']))
    {
        if(isset($_POST['max_number_live_traffic']) && isset($_POST['refreshRateOption']))
        {
            $settings = wsdPluginValidateSettingsForm($settings, $refreshRates);
        }
        else { exit('Invalid request.');  }
    }
    elseif(isset($_POST['deleteEntriesButton'])){
        global $wpdb;
        $query = "TRUNCATE ".WsdPlugin::getTableName(WSS_PLUGIN_LIVE_TRAFFIC_TABLE_NAME);
        $wpdb->query($query);
        $settings = wsdPluginValidateSettingsForm($settings, $refreshRates);
    }
    else { exit('Invalid request.');  }
}


$keepNumEntriesLiveTraffic = (isset($settings['keepNumEntriesLiveTraffic']) ? $settings['keepNumEntriesLiveTraffic'] : 500);
$liveTrafficRefreshRateAjax = (isset($settings['liveTrafficRefreshRateAjax']) ? $settings['liveTrafficRefreshRateAjax'] : 10);
?>
<style type="text/css">
    .wsd-clear { float: none; clear: both; height: 0; width: 100%; margin: 0 0; padding: 0 0;}
    .wsdTrafficScan { }
    .wsd-scan-entry { border-bottom: solid 1px #000; }
    .wsd-scan-entry p { margin: 0 0; padding: 0 0;  font-size: 100%; }
    .wsd-scan-entry p .w-entry { color: #21759B; }
    .wsd-scan-entry p .w-ip { color: #d00000; }
    .wsd-scan-entry p .w-ua { color: #808080; }
    .wsd-scan-entry p .w-date { color: #006600; }
    #loaderWrapper {
        float: right;
        margin: 0 5px 0 0;
        padding: 0 0;
        overflow: hidden; min-height: 1px;
        height: 20px;
    }
    #loaderWrapper span img { float: left;display:block; margin-top:4px; }
    #loaderWrapper span span { float: left;display:block;padding-top: 0;margin-left:10px;color:#000000; }
    .wsdPluginFieldsetSettingsExpanded { background: #F9F9F9; border: solid 1px #DFDFDF; padding: 0 0; }
    .wsdPluginFieldsetSettingsCollapsed { background: transparent; border: none; border-top: solid 1px #DFDFDF; padding: 0 0; }
    #settingsLegend {
        background: linear-gradient(to top, #ECECEC, #F9F9F9) repeat scroll 0 0 #F1F1F1;
        border: solid 1px #DFDFDF; padding: 2px 5px; margin-left: 5px; cursor: pointer;
    }
    #settingsContent { padding: 0 0; margin: 2px 0 5px 10px; line-height: normal; }
    #max_number_live_traffic { margin-top: -5px; padding-top: 0; padding-bottom: 0; width: 50px; }
    .btn {
        -moz-box-sizing: border-box;
        border-radius: 3px 3px 3px 3px;
        border-style: solid;
        border-width: 1px;
        cursor: pointer;
        display: inline-block;
        font-size: 12px;
        height: 24px;
        line-height: 23px;
        margin: 0;
        padding: 0 10px 1px;
        text-decoration: none;
        white-space: nowrap;
    }
    .btn-danger {
        color: #ffffff;
        text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
        background-color: #da4f49;
        background-image: -moz-linear-gradient(top, #ee5f5b, #bd362f);
        background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#ee5f5b), to(#bd362f));
        background-image: -webkit-linear-gradient(top, #ee5f5b, #bd362f);
        background-image: -o-linear-gradient(top, #ee5f5b, #bd362f);
        background-image: linear-gradient(to bottom, #ee5f5b, #bd362f);
        background-repeat: repeat-x;
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffee5f5b', endColorstr='#ffbd362f', GradientType=0);
        border-color: #bd362f #bd362f #802420;
        border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
        *background-color: #bd362f;
        /* Darken IE7 buttons by default so they stand out more given they won't have borders */
        filter: progid:DXImageTransform.Microsoft.gradient(enabled = false);
    }
    .btn-inverse {
        color: #ffffff;
        text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
        background-color: #363636;
        background-image: -moz-linear-gradient(top, #444444, #222222);
        background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#444444), to(#222222));
        background-image: -webkit-linear-gradient(top, #444444, #222222);
        background-image: -o-linear-gradient(top, #444444, #222222);
        background-image: linear-gradient(to bottom, #444444, #222222);
        background-repeat: repeat-x;
        filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ff444444', endColorstr='#ff222222', GradientType=0);
        border-color: #222222 #222222 #000000;
        border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
        *background-color: #222222;
        /* Darken IE7 buttons by default so they stand out more given they won't have borders */
        filter: progid:DXImageTransform.Microsoft.gradient(enabled = false);
    }
</style>
<div class="wrap wsdplugin_content">
    <h2><?php echo WSS_PLUGIN_NAME.' - '. __('Live Traffic');?></h2>

    <p class="clear"></p>

    <div style="padding: 0 0;">
        <form method="post">
            <?php wp_nonce_field('wsdplugin_update_settings','wsdplugin_update_settings_field'); ?>
            <fieldset class="wsdPluginFieldsetSettingsExpanded">
                <legend id="settingsLegend">Settings</legend>
                <p id="settingsContent">
                    <label for="max_number_live_traffic">Maximum number of entries to store for Live Traffic:</label>
                    <input type="text" name="max_number_live_traffic" id="max_number_live_traffic" value="<?php echo $keepNumEntriesLiveTraffic;?>" maxlength="5"/>

                    <label for="refreshRateOption" style="margin-left: 20px;">Refresh rate</label>
                    <select name="refreshRateOption" id="refreshRateOption">
                        <?php
                        foreach($refreshRates as $rate){
                            $selected = ($rate == $liveTrafficRefreshRateAjax ? 'selected="selected"' : '');
                            if($rate == 0){
                                echo '<option value="'.$rate.'" '.$selected.'>Never</option>';
                            }
                            else {  echo '<option value="'.$rate.'" '.$selected.'>'.$rate.' seconds</option>'; }
                        }
                        ?>
                    </select>
                    <input type="submit" value="Update settings" class="btn button-primary" name="updateSettingsButton" style="margin-left: 20px;"/>
                    <?php if($liveTrafficRefreshRateAjax == 0) :?>
                        <input type="button" value="Refresh" class="btn btn-inverse" style="margin-left: 20px;" onclick="javascript: window.location.href=document.URL;"/>
                    <?php endif;?>
                    <input type="submit" value="Delete all" class="btn btn-danger" name="deleteEntriesButton" style="margin-left: 20px;"/>
                </p>
            </fieldset>
        </form>
    </div>


    <p class="clear"></p>
    <table id="wsdTrafficScanTable" cellspacing="0"
           class="wp-list-table widefat wsdTrafficScan"
           data-nonce="<?php echo wp_create_nonce("wsdTrafficScan_nonce");?>"
           data-lastid="<?php echo WsdLiveTraffic::getLastID();?>">
        <thead style="height: 20px;">
        <tr>
            <th class="manage-column column-cb" scope="col">
                <span style="display: block; float: left; font-weight: 800;">Live activity</span>
                <p id="loaderWrapper"></p>
            </th>
        </tr>
        </thead>
        <tbody id="the-list"></tbody>
    </table>
</div>
<script type="text/javascript">
    <?php if($forceLoad) echo 'var wsdAjaxForceLoad = 1;'; else echo 'var wsdAjaxForceLoad = 0;'?>
    (function($){
        $(document).ready( function()
        {
            function _createLoader($){
                var imgPath = "<?php echo WsdUtil::imageUrl('ajax-loader.gif') ?>";
                var text = "<?php echo __('Loading data...');?>";
                return $('<span id="ajaxLoaderRemove"><img src="'+imgPath+'" title="'+text+'" alt="'+text+'"/><span>'+text+'</span></span>');
            }
            function _showLoader($parentElement, $loader){ $parentElement.append($loader); }
            function _hideLoader(stringId) { $('#'+stringId).remove(); }

            var loader = _createLoader($);

            var $table = $("#wsdTrafficScanTable"),
                nonce = $table.attr("data-nonce"),
                $tbody = $('#the-list', $table)
                ,lastID = $table.attr("data-lastid")
                ,loaderWrapper = $('#loaderWrapper');

            function wsdpluginLoadData()
            {
                _showLoader(loaderWrapper, loader);
                $.ajax({
                    type : "post",
                    dataType : "json",
                    cache: false,
                    url : "<?php echo admin_url( 'admin-ajax.php' );?>",
                    data : {'action': "ajaxGetTrafficData", 'nonce': nonce, 'lastID' : lastID, 'forceLoad': wsdAjaxForceLoad, 'maxEntries' : <?php echo $keepNumEntriesLiveTraffic;?>},
                    success: function(response) {
                        _hideLoader('ajaxLoaderRemove');
                        if(response.type == "success") {
                            if(response.data.length > 20){ $tbody.html(response.data); }
                        }
                        else { alert("An error occurred while trying to load data. Please try again in a few seconds."); }
                    }
                });
                wsdAjaxForceLoad = 0;
            }

            wsdpluginLoadData();
            <?php //if refresh rate is == 0 -> disable
              if($liveTrafficRefreshRateAjax > 0) :
            ?>
                window.setInterval(function(){wsdpluginLoadData(); }, <?php echo $liveTrafficRefreshRateAjax * 1000 ?>);
            <?php endif; ?>

            // settings
            var settingsLegend = $('#settingsLegend');
            var settingsContent = $('#settingsContent');
            settingsLegend.toggle(
                function(){
                    settingsContent.hide();
                    settingsLegend.parent().removeClass('wsdPluginFieldsetSettingsExpanded').addClass('wsdPluginFieldsetSettingsCollapsed');
                },
                function(){
                    settingsLegend.parent().removeClass('wsdPluginFieldsetSettingsCollapsed').addClass('wsdPluginFieldsetSettingsExpanded');
                    settingsContent.show();
                }
            );
       });
    })(jQuery);
</script>