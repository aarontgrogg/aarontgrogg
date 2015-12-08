<?php if(! WsdUtil::canLoad()) { return; } ?>
<?php if(! WsdUtil::isAdministrator()) { return; } ?>
<div class="wrap wsdplugin_content">
    <h2><?php echo WSS_PLUGIN_NAME.' - '. __('Database Tools');?></h2>

    <p class="clear"></p>
    <div style="clear: both; display: block;">
        <?php /*[ DATABASE BACKUP ]*/ ?>
        <div class="metabox-holder" style="overflow: hidden;">

            <?php
            /*
             * DATABASE BACKUP TOOL
             * ================================================================
             */
            ?>
            <div id="bckdb" style="float:left; width:49%;" class="inner-sidebar1 postbox">
                <h3 class="hndle" style="cursor: default;"><span><?php echo __('Backup Database');?></span></h3>
                <div class="inside">
                    <?php
                    echo WsdUtil::loadTemplate('box-database-backup');
                    ?>
                </div>
            </div>

            <?php
            /*
             * DATABASE BACKUPS
             * ================================================================
             */
            ?>
            <div style="float:right;width:49%;" class="inner-sidebar1 postbox">
                <h3 class="hndle" style="cursor: default;"><span><?php echo __('Database Backup Files');?></span></h3>
                <div class="inside">
                    <?php
                    echo WsdUtil::loadTemplate('box-available-backups');
                    ?>
                </div>
            </div>
        </div>

        <p class="clear"></p>
        <div class="metabox-holder" style="width:99.8%; padding-top: 0;">
            <?php
            /*
             * CHANGE DATABASE PREFIX TOOL
             * ================================================================
             */
            ?>
            <div id="cdtp" class="postbox">
                <h3 class="hndle" style="cursor: default;"><span><?php echo __('Change Database Prefix');?></span></h3>
                <div class="inside">
                    <?php
                    echo WsdUtil::loadTemplate('box-database-change-prefix');
                    ?>
                </div>
            </div>
        </div>

    </div>
</div>
