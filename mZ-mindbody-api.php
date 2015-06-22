<?php
/**
Plugin Name: Advanced mZoo Mindbody Interface - Schedule, Events, Staff Display
Description: Interface Wordpress with MindbodyOnline data with Bootstrap Responsive Layout
Version: 1.7.5
Author: mZoo.org
Author URI: http://www.mZoo.org/
Plugin URI: http://www.mzoo.org/mz-mindbody-wp

Based on API written by Devin Crossman.
*/

//define plugin path and directory
define( 'MZ_MINDBODY_SCHEDULE_DIR', plugin_dir_path( __FILE__ ) );
define( 'MZ_MINDBODY_SCHEDULE_URL', plugin_dir_url( __FILE__ ) );

//register activation and deactivation hooks
register_activation_hook(__FILE__, 'mZ_mindbody_schedule_activation');
register_deactivation_hook(__FILE__, 'mZ_mindbody_schedule_deactivation');

load_plugin_textdomain('mz-mindbody-api',false,'mz-mindbody-schedule/languages');

function mZ_mindbody_schedule_activation() {
	//Don't know if there's anything we need to do here.
}

function mZ_mindbody_schedule_deactivation() {
	// actions to perform once on plugin deactivation go here
}

//register uninstaller
register_uninstall_hook(__FILE__, 'mZ_mindbody_schedule_uninstall');

function mZ_mindbody_schedule_uninstall(){
	//actions to perform once on plugin uninstall go here
	delete_option('mz_mindbody_options');
}

function mz_mbo_enqueue($hook) {
    if ( 'settings_page_mz-mindbody-api/mZ-mindbody-api' != $hook ) {
        return;
    }
    wp_register_style( 'mz_mbo_admin_css', plugin_dir_url( __FILE__ ) . 'css/mbo_style_admin.css', false, '1.0.0' );
        wp_enqueue_style( 'mz_mbo_admin_css' );

}
add_action( 'admin_enqueue_scripts', 'mz_mbo_enqueue' );

//TODO Deal with conflict when $mb class get's called twice
add_action('widgets_init', 'mZ_mindbody_schedule_register_widget');

function mZ_mindbody_schedule_register_widget() {
    register_widget( 'mZ_Mindbody_day_schedule');
}

if (!function_exists( 'mZ_latest_jquery' )){
	function mZ_latest_jquery(){
		//	Use latest jQuery release
		if( !is_admin() ){
			wp_deregister_script('jquery');
			wp_register_script('jquery', ("http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"), false, '');
			wp_enqueue_script('jquery');
		}
	}
	add_action('wp_enqueue_scripts', 'mZ_latest_jquery');
}

class mZ_Mindbody_day_schedule extends WP_Widget {

    function mZ_Mindbody_day_schedule() {
        $widget_ops = array(
            'classname' => 'mZ_Mindbody_day_schedule_class',
            'description' => 'Display class schedule for current day.'
            );
        $this->WP_Widget('mZ_Mindbody_day_schedule', 'Today\'s MindBody Schedule',
                            $widget_ops );
    } 
    
    function form($instance){
        $defaults = array('title' => 'Today\'s Classes');
        $instance = wp_parse_args( (array) $instance, $defaults);
        $title = $instance['title'];
        ?>
           <p>Title: <input class="widefat" name="<?php echo $this->get_field_name( 'title' ); ?>"  
           type="text" value="<?php echo esc_attr( $title ); ?>" /></p>
        <?php
    }
    
    //save the widget settings
    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['title'] = strip_tags( $new_instance['title'] );
        return $instance;
    }
    
    function widget($args, $instance){
        extract($args);
        echo $before_widget;
        $title = apply_filters( 'widget_title', $instance['title'] );
        $arguments['type'] = 'day';
        if (!empty($title) ) 
            { echo $before_title . $title . $after_title; };
            echo(mZ_mindbody_show_schedule($arguments, $account=0));
        echo $after_widget;
    }
}

//Force page protocol to match current
$protocol = isset( $_SERVER["HTTPS"]) ? 'https://' : 'http://';

//Advanced Includes
	foreach ( glob( plugin_dir_path( __FILE__ )."advanced/*.php" ) as $file )
        include_once $file;
    
if ( is_admin() )
{     
    //Sections
	require_once MZ_MINDBODY_SCHEDULE_DIR .'lib/sections.php';
}
else
{// non-admin enqueues, actions, and filters

    add_action('init', 'myStartSession', 1);
    add_action('wp_logout', 'myEndSession');
    add_action('wp_login', 'myEndSession');

    function myStartSession() {
    	if ((function_exists('session_status') && session_status() !== PHP_SESSION_ACTIVE) || !session_id()) {
			  session_start();
			}
    }

    function myEndSession() {
        session_destroy ();
    }
	
  
	function load_jquery() {
		wp_enqueue_script( 'jquery' );
	}
	add_action( 'wp_enqueue_script', 'load_jquery' );
	
	include_once(dirname( __FILE__ ) . '/mindbody-php-api/MB_API.php');

	foreach ( glob( plugin_dir_path( __FILE__ )."inc/*.php" ) as $file )
        include_once $file;

	add_shortcode('mz-mindbody-show-schedule', 'mZ_mindbody_show_schedule' );
	add_shortcode('mz-mindbody-show-events', 'mZ_mindbody_show_events' );
	add_shortcode('mz-mindbody-staff-list', 'mZ_mindbody_staff_listing' );
	add_shortcode('mz-mindbody-login', 'mZ_mindbody_login' );
	add_shortcode('mz-mindbody-logout', 'mZ_mindbody_logout' );
	add_shortcode('mz-mindbody-activation', 'mZ_mindbody_activation' );
	add_shortcode('mz-mindbody-add-to-classes', 'mz_mindbody_add_to_classes' );
}//EOF Not Admin

if (phpversion() >= 5.3) {
    include_once('php_variants/sort_newer.php');
    }else{
    include_once('php_variants/sort_older.php');
    }
    
	//Functions
	require_once MZ_MINDBODY_SCHEDULE_DIR .'lib/functions.php';?>
