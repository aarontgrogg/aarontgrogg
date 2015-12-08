<?php if(! defined('WSS_PLUGIN_PREFIX')) return;
/**
 * Class WsdLiveTraffic
 */
class WsdLiveTraffic
{
    private function __construct(){}
    private function __clone(){}

    final public static function clearEvents()
    {
        global $wpdb;
        $settings = WsdPlugin::getSettings();
        $keepMaxEntries = (int)$settings['keepNumEntriesLiveTraffic'];

        if($keepMaxEntries < 1){
            $query = "TRUNCATE ".WsdPlugin::getTableName(WSS_PLUGIN_LIVE_TRAFFIC_TABLE_NAME);
            $wpdb->query($query);
            return;
        }

        $optData = get_option('WSS_PLUGIN_ENTRIES_LIVE_TRAFFIC');
        if(empty($optData)){
            return;
        }

        $numEntries = $wpdb->get_var("SELECT COUNT(entryId) FROM ".WsdPlugin::getTableName(WSS_PLUGIN_LIVE_TRAFFIC_TABLE_NAME));

        if($numEntries <> $keepMaxEntries){
            update_option('WSS_PLUGIN_ENTRIES_LIVE_TRAFFIC', $numEntries);
        }

        if(intval($optData) <= $keepMaxEntries){
            return;
        }

        $tableName = WsdPlugin::getTableName(WSS_PLUGIN_LIVE_TRAFFIC_TABLE_NAME);

        $querySelect = "SELECT min(t.entryTime)
                            FROM
                            (
                                SELECT
                                    entryTime
                                FROM
                                    ".$tableName."
                                ORDER BY
                                    entryTime DESC
                                LIMIT ".$keepMaxEntries."
                            ) AS t";


        $deleteFromTime = $wpdb->get_var($querySelect);

        $queryDelete = "DELETE FROM ".$tableName." WHERE entryTime < %s";
        $result = $wpdb->query($wpdb->prepare($queryDelete,$deleteFromTime));

        if(false === $result){
            return;
        }
        // update option
        $numEntries = $wpdb->get_var("SELECT COUNT(entryId) FROM ".WsdPlugin::getTableName(WSS_PLUGIN_LIVE_TRAFFIC_TABLE_NAME));
        update_option('WSS_PLUGIN_ENTRIES_LIVE_TRAFFIC', $numEntries);
    }

    final public static function registerHit()
    {
        if(is_admin()){ return; }

        global $wpdb;

        $url = self::getRequestedUrl();

        if(self::isUrlExcluded($url)){ return; }

        $ip = self::getIP();
        $referrer = self::getReferrer();
        $ua = self::getUserAgent();

        $query = $wpdb->prepare("INSERT INTO ".WsdPlugin::getTableName(WSS_PLUGIN_LIVE_TRAFFIC_TABLE_NAME)." (entryTime, entryIp, entryReferrer, entryUA, entryRequestedUrl)
                            VALUES(CURRENT_TIMESTAMP, %s, %s, %s, %s)", $ip, $referrer, $ua, $url);
        if(false === @$wpdb->query($query)){
            return;
        }

        $numEvents = 0;
        $optData = get_option('WSS_PLUGIN_ENTRIES_LIVE_TRAFFIC');
        if(empty($optData)){
            add_option('WSS_PLUGIN_ENTRIES_LIVE_TRAFFIC', $numEvents);
        }
        else { $numEvents = intval($optData); }

        update_option('WSS_PLUGIN_ENTRIES_LIVE_TRAFFIC', $numEvents + 1);
    }

    final public static function getIP()
    {
        $ip = null;
        if ( isset($_SERVER["REMOTE_ADDR"]) ) { $ip = $_SERVER["REMOTE_ADDR"]; }
        else if ( isset($_SERVER["HTTP_X_FORWARDED_FOR"]) ) { $ip = $_SERVER["HTTP_X_FORWARDED_FOR"]; }
        else if ( isset($_SERVER["HTTP_CLIENT_IP"]) ) { $ip = $_SERVER["HTTP_CLIENT_IP"]; }
        if(! is_null($ip) && self::isValidIp($ip)){ return $ip; }
        return 'unknown';
    }

    final public static function getReferrer() { return (empty($_SERVER['HTTP_REFERER']) ? '' : htmlentities($_SERVER['HTTP_REFERER'],ENT_QUOTES)); }

    final public static function getUserAgent() { return (empty($_SERVER['HTTP_USER_AGENT']) ? '' : htmlentities($_SERVER['HTTP_USER_AGENT'],ENT_QUOTES)); }

    final public static function isValidIp($ip){
        if(preg_match('/^(\d+)\.(\d+)\.(\d+)\.(\d+)$/', $ip, $m)){
            if(
                $m[0] >= 0 && $m[0] <= 255 &&
                $m[1] >= 0 && $m[1] <= 255 &&
                $m[2] >= 0 && $m[2] <= 255 &&
                $m[3] >= 0 && $m[3] <= 255
            ){
                return true;
            }
        }
        return false;
    }
    final public static function getRequestedUrl(){
        if(isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST']){
            $host = $_SERVER['HTTP_HOST']; }
        else {
            $host = $_SERVER['SERVER_NAME'];
        }
        $url = (@$_SERVER['HTTPS'] ? 'https' : 'http') . '://' . $host . $_SERVER['REQUEST_URI'];
        return htmlentities($url,ENT_QUOTES);
    }

    /**
     * @param int $maxEntries If $maxEntries is 0 it means to load all entries, otherwise it will limit the select to that number
     * @return mixed
     */
    final public static function getTrafficData($maxEntries = 0)
    {
        global $wpdb;
        if(empty($maxEntries)){
            return $wpdb->get_results("SELECT entryId,entryTime,entryIp,entryReferrer,entryUA,entryRequestedUrl FROM ".WsdPlugin::getTableName(WSS_PLUGIN_LIVE_TRAFFIC_TABLE_NAME)." ORDER BY entryId DESC");
        }
        else { return $wpdb->get_results("SELECT entryId,entryTime,entryIp,entryReferrer,entryUA,entryRequestedUrl FROM ".WsdPlugin::getTableName(WSS_PLUGIN_LIVE_TRAFFIC_TABLE_NAME)." ORDER BY entryId DESC LIMIT 0, ".$maxEntries);}
    }

    final public static function getLastID()
    {
        global $wpdb;
        return $wpdb->get_var("SELECT entryId FROM ".WsdPlugin::getTableName(WSS_PLUGIN_LIVE_TRAFFIC_TABLE_NAME)." ORDER BY entryId DESC");
    }

    final public static function ajaxGetTrafficData($maxEntries = 0)
    {
        if ( !wp_verify_nonce( $_REQUEST['nonce'], "wsdTrafficScan_nonce")) { exit(__('Invalid request - nonce')); }

        if ( !isset( $_REQUEST['lastID'])) { exit(__('Invalid request - lastID')); }

        if ( !isset( $_REQUEST['forceLoad'])) { exit(__('Invalid request - forceload')); }

        if ( isset( $_REQUEST['maxEntries'])) { $maxEntries = intval($_REQUEST['maxEntries']); }

        $result = array();
        $forceLoad = (bool)$_REQUEST['forceLoad'];

        // no changes yet
        if(! $forceLoad)
        {
            if($_REQUEST['lastID'] == self::getLastID())
            {
                $result['type'] = 'success';
                $result['data'] = '';
                $result = json_encode($result);
                exit($result);
            }
        }

        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            $result['type'] = 'success';
            $result['data'] = '';
            $data = self::getTrafficData($maxEntries);
            if(empty($data)){ $result['data'] = '<tr><td><p style="margin: 5px 5px; font-weight: bold; color: #cc0000">No data yet.</p></td></tr>'; }
            else {
                $title= sprintf('title="%s"',__('Opens in a new tab'));
                foreach($data as $entry)
                {
                    $result['data'] .= '<tr><td class="wsd-scan-entry">';
                    $result['data'] .= '<div>';
                    if(empty($entry->entryReferrer)){
                        $ref = '';
                    }
                    else {
                        $url = htmlentities(urldecode($entry->entryReferrer),ENT_QUOTES);
                        $ref = __('coming from').' <span class="w-entry"><a href="'.$url.'" target="_blank" '.$title.'>'. $url . '</a></span>';
                    }

                    //@todo: add geo-location + flag

                    $result['data'] .= '<p><span class="w-ip">'.$entry->entryIp . '</span> ';
                    $rurl = urldecode($entry->entryRequestedUrl);
                    $result['data'] .= $ref.' '.__('requested').' <span class="w-entry"><a href="'.$rurl.'" target="_blank" '.$title.'>'.htmlentities($rurl,ENT_QUOTES).'</a></span></p>';
                    $result['data'] .= '<p><strong>'.__('Date').'</strong>: <span class="w-date">'.$entry->entryTime.'</span></p>';
                    $result['data'] .= '<p><strong>'.__('Agent').'</strong>: <span class="w-ua">'.htmlentities($entry->entryUA,ENT_QUOTES).'</span></p>';
                    $result['data'] .= '</div>';
                    $result['data'] .= '</td></tr>';
                }
            }
            $result = json_encode($result);
            exit($result);
        }
        exit('Invalid request!');
    }


    /**
     * @param $url
     * @return bool
     * Exclude urls
     */
    private static function isUrlExcluded($url)
    {
        if(false !==(strpos($url, 'wp-cron.php?doing_wp_cron'))) { return true; }
        return false;
    }
}
