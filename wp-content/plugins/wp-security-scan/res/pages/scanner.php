<?php if(! WsdUtil::canLoad()) { return; } ?>
<?php if(! WsdUtil::isAdministrator()) { return; } ?>
<div class="wrap wsdplugin_content">
    <h2><?php echo WSS_PLUGIN_NAME.' - '. __('WordPress Scanner');?></h2>

    <p class="clear"></p>
    <p style="margin-left: 15px;">
        <?php echo __("This page displays various information after scanning your WordPress website"); ?>:
    </p>

    <div class="metabox-holder">

        <div style="width:49.8%; float: left;" class="postbox">
            <h3 class="hndle" style="cursor: default;"><span><?php echo __('Server Report');?></span></h3>
            <div class="inside acx-section-box">
                <?php
                echo WsdUtil::loadTemplate('box-server-results');
                ?>
            </div>
        </div>
        <div style="width:49.8%; float: right;" class="postbox">
            <h3 class="hndle" style="cursor: default;"><span><?php echo __('WordPress Scan Report');?></span></h3>
            <div class="inside acx-section-box">
                <?php
                echo WsdUtil::loadTemplate('box-scan-results-wp');
                ?>
            </div>
        </div>

        <div style="width:99.8%; clear: both;" class="inner-sidebar1 postbox">
            <h3 class="hndle" style="cursor: default;"><span><?php echo __('File Scan Report');?></span></h3>
            <div class="inside">
                <?php
                echo WsdUtil::loadTemplate('box-scan-results-file');
                ?>
            </div>
        </div>
    </div>
</div>