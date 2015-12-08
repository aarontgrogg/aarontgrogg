<?php if(! WsdUtil::canLoad()) { return; } ?>
<?php wp_enqueue_style('wsdplugin_css_blog', WsdUtil::cssUrl('blog.css'), array(), '1.0'); ?>

<div class="wrap wsdplugin_content">
    <div id="pageblog" class="wrap wsdplugin_content">

    <div id="header">
        <div class="wrapper">
            <div id="logo"><a href="http://www.acunetix.com" target="_blank">Acunetix</a></div>
            <div id="main-nav">
                <a class="twitter" target="_blank" href="http://twitter.com/acunetix" title="<?php echo __('Follow us on Twitter');?>">Twitter</a>
                <a class="facebook" target="_blank" href="http://www.facebook.com/Acunetix" title="<?php echo __('Follow us on Facebook');?>">Facebook</a>
            </div>
        </div>
    </div>
    <div id="content">
        <ul id="feed-list">
            <?php
            /**
             * @internal
             * @param int $maxItems
             * @return string
             */
            function wsdplugin_GetFeedData($maxItems = 10)
            {
                $rss = fetch_feed(WSS_PLUGIN_BLOG_FEED);
                $out = '';
                if (is_wp_error( $rss ) ) { return '<li>'.__('An error has occurred while trying to load the rss feed!').'</li>'; }
                else{
                    // Limit to $maxItems entries.
                    $maxitems = $rss->get_item_quantity($maxItems);

                    // Build an array of all the items,
                    $rss_items = $rss->get_items(0, $maxitems);

                    if ($maxitems == 0){ $out.= '<li>'.__('There are no entries for this feed!').'</li>'; }
                    else {
                        foreach ( $rss_items as $item ) :
                            $url = esc_url($item->get_permalink());
                            $out.= '<li>';
                            $out.= '<h4><a href="'.$url.'" target="_blank" title="'.__('Posted on ').$item->get_date('F j, Y | g:i a').'">';
                            $out.= esc_html( $item->get_title() );
                            $out.= '</a></h4>';
                            $out.= '<div>' . $item->get_description() . '</div>';
                            $out.= '</li>';
                        endforeach;
                    }
                    $out .= '<div style="border-top: solid 1px #ccc; margin-top: 4px; padding: 2px 0;">';
                    $out .= '<p style="margin: 5px 0 0 0; padding: 0 0; line-height: normal; overflow: hidden;">';
                    $out .= '<a href="http://feeds.acunetix.com/acunetixwebapplicationsecurityblog"
                                    style="float: left; display: block; width: 50%; text-align: right; margin-left: 30px;
                                    padding-right: 22px; background: url(\'rss.png\') no-repeat right center;"
                                    target="_blank">'.__('Follow us on RSS').'</a>';
                    $out .= '</p>';
                    $out .= '</div>';
                }
                return $out;
            }
            /**
             * @internal
             * @param $optName
             * @param $optData
             */
            function wsdplugin_updateRssFeedOption($optName, $optData)
            {
                $obj = new stdClass();
                $obj->expires = time() + (24*60*60);
                $obj->data = $optData;
                update_option($optName, $obj);
            }
            /**
             * @public
             * @param $optName
             * @param $getMaxRssEntries
             */
            function wsdplugin_handleDisplayRssData($optName, $getMaxRssEntries)
            {
                $data = wsdplugin_GetFeedData($getMaxRssEntries);
                wsdplugin_updateRssFeedOption($optName, $data);
                echo str_ireplace("url('rss.png')", "url('".WsdUtil::imageUrl('rss.png')."')", $data);
            }

            $optName = 'WSD-FEED-DATA';
            $getMaxRssEntries = 10;

            //@ check cache
            $optData = get_option($optName);

            if(empty($optData)) { wsdplugin_handleDisplayRssData($optName, $getMaxRssEntries); }
            else{
                // check cache expiry date
                if (is_object($optData)) {
                    $lastUpdateTime = @$optData->expires;
                    // invalid cache: UPDATE
                    if (empty($lastUpdateTime)) { wsdplugin_handleDisplayRssData($optName, $getMaxRssEntries); }
                    else {
                        $nextUpdateTime = $lastUpdateTime+(24*60*60);
                        if ($nextUpdateTime >= $lastUpdateTime){
                            $data = @$optData->data;
                            if (empty($data)) { wsdplugin_handleDisplayRssData($optName, $getMaxRssEntries); }
                            // still a valid cache: DISPLAY
                            else { echo str_ireplace("url('rss.png')", "url('".WsdUtil::imageUrl('rss.png')."')", $data); }
                        }
                        else { wsdplugin_handleDisplayRssData($optName, $getMaxRssEntries); }
                    }
                }
                else { wsdplugin_handleDisplayRssData($optName, $getMaxRssEntries); }
            }
            ?>
        </ul>
    </div>
</div>
</div>