<?php
/*

**************************************************************************

Plugin Name:  Site Analytics Widget
Plugin URI:   http://www.arefly.com/site-analytics-widget/
Description:  Add a Widget of your Website's Analytics. 在網站上增加一個「網站統計」小工具
Version:      1.0.1
Author:       Arefly
Author URI:   http://www.arefly.com/
Text Domain:  site-analytics-widget
Domain Path:  /lang/

**************************************************************************

	Copyright 2014  Arefly  (email : eflyjason@gmail.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as 
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

**************************************************************************/

define("SITE_ANALYTICS_WIDGET_PLUGIN_URL", plugin_dir_url( __FILE__ ));
define("SITE_ANALYTICS_WIDGET_FULL_DIR", plugin_dir_path( __FILE__ ));
define("SITE_ANALYTICS_WIDGET_TEXT_DOMAIN", "site-analytics-widget");

/* Plugin Localize */
function site_analytics_widget_load_plugin_textdomain() {
	load_plugin_textdomain(SITE_ANALYTICS_WIDGET_TEXT_DOMAIN, false, dirname(plugin_basename( __FILE__ )).'/lang/');
}
add_action('plugins_loaded', 'site_analytics_widget_load_plugin_textdomain');

class site_analytics_widget extends WP_Widget {
	// Controller
	function __construct(){
		$widget_ops = array('classname' => 'site_analytics_widget_class', 'description' => __("Your Website's Analytics.", SITE_ANALYTICS_WIDGET_TEXT_DOMAIN));
		$control_ops = array('width' => 400, 'height' => 300);
		parent::WP_Widget(false, $name = __('Site Analytics', SITE_ANALYTICS_WIDGET_TEXT_DOMAIN), $widget_ops, $control_ops);
	}

	// Constructor
	function wp_my_plugin(){
		parent::WP_Widget(false, $name = __('Site Analytics', SITE_ANALYTICS_WIDGET_TEXT_DOMAIN) );
	}

	// Display widget
	function widget($args, $instance){
		global $wpdb;
		extract($args);
		// These are the widget options
		$title = apply_filters('widget_title', $instance['title']);
		$website_create_date = $instance['website_create_date'];
		echo $before_widget;
		// Display the widget
		echo '<div class="'.SITE_ANALYTICS_WIDGET_TEXT_DOMAIN.'">';

		// Check if title is set
		if(empty($title)){
			$title = __("Site Analytics", SITE_ANALYTICS_WIDGET_TEXT_DOMAIN);
		}
		echo $before_title . $title . $after_title;
?>
<ul>
	<li><?php printf(__('Total Posts: %s', SITE_ANALYTICS_WIDGET_TEXT_DOMAIN), wp_count_posts()->publish); ?></li>
	<li><?php printf(__('Total Comments: %s', SITE_ANALYTICS_WIDGET_TEXT_DOMAIN), $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->comments where comment_author!='".(get_option('swt_user'))."'")); ?></li>
	<li><?php printf(__('Total Tags: %s', SITE_ANALYTICS_WIDGET_TEXT_DOMAIN), $count_tags = wp_count_terms('post_tag')); ?></li>
	<li><?php printf(__('Total Links: %s', SITE_ANALYTICS_WIDGET_TEXT_DOMAIN), $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->links WHERE link_visible = 'Y'")); ?></li>
	<li><?php printf(__('Website Create Date: %s', SITE_ANALYTICS_WIDGET_TEXT_DOMAIN), $website_create_date); ?></li>
	<li><?php printf(__('Website Running For: %s Days', SITE_ANALYTICS_WIDGET_TEXT_DOMAIN), floor((time()-strtotime($website_create_date))/86400)); ?></li>
	<li><?php printf(__('Last Update: %s', SITE_ANALYTICS_WIDGET_TEXT_DOMAIN), date('Y-n-j', strtotime($wpdb->get_results("SELECT MAX(post_modified) AS MAX_m FROM $wpdb->posts WHERE (post_type = 'post' OR post_type = 'page') AND (post_status = 'publish' OR post_status = 'private')")[0]->MAX_m))) ?></li>
</ul>
</div>
<?php
		echo $after_widget;
	}

	// Update widget
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		// Fields
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['website_create_date'] = strip_tags($new_instance['website_create_date']);
		return $instance;
	}

	// Widget form creation
	function form($instance) {
		// Check values
		if($instance) {
			$title = esc_attr($instance['title']);
			$website_create_date = esc_attr($instance['website_create_date']);
		} else {
			$title = '';
			$website_create_date = '2013-12-25';
		}
?>
<p>
<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget Title', SITE_ANALYTICS_WIDGET_TEXT_DOMAIN); ?></label>
<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
</p>

<p>
<label for="<?php echo $this->get_field_id('website_create_date'); ?>"><?php _e("Website Create Date (Format: YYYY-MM-DD)", SITE_ANALYTICS_WIDGET_TEXT_DOMAIN); ?></label>
<input class="widefat" id="<?php echo $this->get_field_id('website_create_date'); ?>" name="<?php echo $this->get_field_name('website_create_date'); ?>" type="text" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" required="required" value="<?php echo $website_create_date; ?>" />
</p>
<?php
	}
}

function site_analytics_register_widgets(){
	register_widget('site_analytics_widget');
}
add_action('widgets_init', 'site_analytics_register_widgets');