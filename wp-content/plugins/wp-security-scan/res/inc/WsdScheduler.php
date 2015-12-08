<?php if(! defined('WSS_PLUGIN_PREFIX')) return;
/**
 * Class WsdScheduler
 * Provides common methods to register action with wp-cron
 */
class WsdScheduler
{
    /**
     * @var array
     * Holds all the registered cron tasks so to provide an easy way to
     * unregister them all upon deactivation of the plugin
     */
    private static $_cronTasks = array();

    /**
     * Register a cron task
     * @param string $cronActionName The name of the action that will be registered with wp-cron
     * @param string $callback The function to register with wp-cron
     * @param string $interval can only be one of the following: hourly, daily and twicedaily if no other custom intervals are registered. Defaults to daily
     * @return void
     */
    public static function registerCronTask($cronActionName, $callback, $interval = 'daily')
    {
        if(! is_callable($callback)) { return; }

        // if cron disabled -> run callback
        if(! self::canRegisterCronTask()){
            self::registerTask($callback);
            return;
        }
        $interval = strtolower($interval);
        if(empty($interval)){ $interval = 'daily'; }
        else{
            // check to see if the time interval is valid
            $timeIntervals = wp_get_schedules();
            if(! array_key_exists($interval, $timeIntervals)){
                $interval = 'daily';
            }
        }
        // avoid duplicate crons
        add_action($cronActionName, $callback);
        if ( ! wp_next_scheduled($cronActionName) ) {
            wp_schedule_event( time(), $interval, $cronActionName );
            array_push(self::$_cronTasks, $cronActionName);
        }
    }

    public static function unregisterCronTask($cronActionName){
        wp_clear_scheduled_hook($cronActionName);
        if(! empty(self::$_cronTasks)){
            if(isset(self::$_cronTasks[$cronActionName])){
                unset(self::$_cronTasks[$cronActionName]);
            }
        }
    }

    public static function unregisterCronTasks(){
        if(! empty(self::$_cronTasks)){
            foreach (self::$_cronTasks as $task) {
                wp_clear_scheduled_hook($task);
            }
            self::$_cronTasks = array();
        }
    }

    /**
     * Check to see whether or not cron is enabled in WordPress
     * @return bool
     */
    public static function canRegisterCronTask(){ return ((defined('DISABLE_WP_CRON') && 'DISABLE_WP_CRON') ? false : true); }

    /**
     * Register a task
     * @param string $callback The callback to register
     * @param string $wpActionName Optional. If provided it must be a valid action name to hook the $callback to. If omitted, then the $callback will just be executed.
     * @return void
     */
    public static function registerTask($callback, $wpActionName = '') {
        if(! empty($wpActionName)){
            add_action($wpActionName, $callback);
        }
        else {
            if(is_callable($callback)){
                call_user_func($callback);
            }
        }
    }

    /**
     * Execute all methods of a class that are prefixed with $onlyWithPrefix (if provided)
     * @param $className The name of the class
     * @param string $onlyWithPrefix Optional. The prefix to look up in the methods' name
     * @return void
     */
    public static function registerClassTasks($className, $onlyWithPrefix = '')
    {
        $_class = new ReflectionClass($className);
        $methods = $_class->getMethods();
        if(! empty($methods)){
            $pLength = strlen($onlyWithPrefix);
            foreach($methods as $_method){
                $method = $_method->name;
                // only certain methods
                if($pLength > 0){
                    $search = substr($method, 0, $pLength);
                    if(strcasecmp($search,$onlyWithPrefix) == 0){
                        call_user_func(array($className, $method));
                    }
                }
                else { call_user_func(array($className, $method)); }
            }
        }
    }

}