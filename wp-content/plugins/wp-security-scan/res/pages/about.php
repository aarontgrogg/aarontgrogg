<?php if(! WsdUtil::canLoad()) { return; } ?>
<div class="wrap wsdplugin_content">
    <h2><?php echo WSS_PLUGIN_NAME.' - '.__('About');?></h2>

    <div class="metabox-holder">

        <div style="width:60%; float: left;" class="postbox">
            <h3 class="hndle" style="cursor: default;"><span><?php echo __('About'). ' '.WSS_PLUGIN_NAME .' plugin';?></span></h3>
            <div class="inside acx-section-box">
                <p>
                    <?php echo WSS_PLUGIN_NAME.' plug-in beefs up the security of your WordPress installation by removing error information on login pages, adds index.php to plugin directories, hides the WordPress version and much more.'; ?>
                </p>
                <div class="acx-section-box">
                    <ul class="acx-common-list">
                        <li><span class="acx-icon-alert-success"><?php echo __('Removes error-information on login-page.');?></span></li>
                        <li><span class="acx-icon-alert-success"><?php echo __('Adds index.php to the wp-content, wp-content/plugins, wp-content/themes and wp-content/uploads directories to prevent directory listings.');?></span></li>
                        <li><span class="acx-icon-alert-success"><?php echo __('Removes the wp-version from everyone but administrators.');?></span></li>
                        <li><span class="acx-icon-alert-success"><?php echo __('Removes Really Simple Discovery meta tag.');?></span></li>
                        <li><span class="acx-icon-alert-success"><?php echo __('Removes Windows Live Writer meta tag.');?></span></li>
                        <li><span class="acx-icon-alert-success"><?php echo __('Removes core update information for non-admins.');?></span></li>
                        <li><span class="acx-icon-alert-success"><?php echo __('Removes plugin-update information for non-admins.');?></span></li>
                        <li><span class="acx-icon-alert-success"><?php echo __('Removes theme-update information for non-admins (only WP 2.8 and higher).');?></span></li>
                        <li><span class="acx-icon-alert-success"><?php echo __('Removes version on URLs from scripts and stylesheets.');?></span></li>
                        <li><span class="acx-icon-alert-success"><?php echo __('Provides various information after scanning your WordPress blog.');?></span></li>
                        <li><span class="acx-icon-alert-success"><?php echo __('Provides file permissions security checks.');?></span></li>
                        <li><span class="acx-icon-alert-success"><?php echo __('Provides a Live Traffic tool to monitor your website activity in real time.');?></span></li>
                        <li><span class="acx-icon-alert-success"><?php echo __('Provides a tool for changing the database prefix.');?></span></li>
                        <li><span class="acx-icon-alert-success"><?php echo __('Provides a tool to easily backup your WordPress database.');?></span></li>
                        <li><span class="acx-icon-alert-success"><?php echo __('Turns off database error reporting (if enabled).');?></span></li>
                        <li><span class="acx-icon-alert-success"><?php echo __('Turns off PHP error reporting.');?></span></li>
                    </ul>
                </div>
            </div>
        </div>

        <div style="width:38%; float: right;" class="postbox">
            <h3 class="hndle" style="cursor: default;"><span><?php echo __('Get involved');?></span></h3>
            <div class="inside acx-section-box">
                <ul class="acx-common-list">
                    <li>
                        <span><a href="//www.acunetix.com/blog/" target="_blank"><?php echo __('Acunetix blog');?></a></span>
                    </li>
                    <li>
                        <span><a href="//twitter.com/acunetix" target="_blank"><?php echo __('Acunetix on Twitter');?></a></span>
                    </li>
                    <li>
                        <span><a href="//www.facebook.com/Acunetix" target="_blank"><?php echo __('Acunetix on Facebook');?></a></span>
                    </li>
                    <li>
                        <span><a href="//wordpress.org/support/plugin/wp-security-scan" target="_blank"><?php echo __('Support');?></a></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

</div>