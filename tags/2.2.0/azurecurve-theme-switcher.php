<?php
/*
Plugin Name: azurecurve Theme Switcher
Plugin URI: http://development.azurecurve.co.uk/plugins/theme-switcher/
Description: Allows users to easily switch themes (ideal for allowing light/dark mode).
Version: 2.2.0
Author: azurecurve
Author URI: http://development.azurecurve.co.uk/

Text Domain: azc_ts
Domain Path: /languages

Forked from original Theme Switcher in order to bring code up to current standards.

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.

The full copy of the GNU General Public License is available here: http://www.gnu.org/licenses/gpl.txt

*/

//include menu
require_once( dirname(  __FILE__ ) . '/includes/menu.php');

class azc_ts_ThemeSwitcherWidget extends WP_Widget {
	function azc_ts_ThemeSwitcherWidget()
	{
		return $this->WP_Widget('azc-ts-theme-switcher-widget', 'azurecurve Theme Switcher', array('description' => __('A widget with options for switching themes.', 'azc_ts')));
	}

	function widget($args, $instance)
	{
		global $theme_switcher;
		$title = empty( $instance['title'] ) ? 'Theme Switcher' : $instance['title'];
		echo $args['before_widget'];
		echo $args['before_title'] . $title . $args['after_title'];
		echo $theme_switcher->theme_switcher_markup($instance['displaytype'], $instance);
		echo $args['after_widget'];
	}

	function update($new_instance, $old_instance) 
	{
		return $new_instance;
	}

	function form($instance) 
	{
		$type = $instance['displaytype'];
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>">
				<span><?php _e('Title:', 'azc_ts'); ?></span>
				<input type="text" name="<?php echo $this->get_field_name('title'); ?>" id="<?php echo $this->get_field_id('title'); ?>" value="<?php echo esc_attr($instance['title']); ?>" />
			</label>
		</p>
			
		<p><label for="<?php echo $this->get_field_id('displaytype'); ?>"><?php _e('Display themes as:', 'azc_ts'); ?></label></p>
		<p>
			<span><input type="radio" name="<?php echo $this->get_field_name('displaytype'); ?>" value="list" <?php
				if ( 'list' == $type ) {
					echo ' checked="checked"';
				}
			?> /> <?php _e('List', 'azc_ts'); ?></span>
			<span><input type="radio" name="<?php echo $this->get_field_name('displaytype'); ?>" value="dropdown" <?php 
				if ( 'dropdown' == $type ) {
					echo ' checked="checked"';
				}
			?>/> <?php _e('Dropdown', 'azc_ts'); ?></span>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('prefix'); ?>">
				<span><?php _e('Ignore themes with prefix:', 'azc_ts'); ?></span>
				<input type="text" name="<?php echo $this->get_field_name('prefix'); ?>" id="<?php echo $this->get_field_id('prefix'); ?>" value="<?php echo esc_attr($instance['prefix']); ?>" />
			</label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('with'); ?>">
				<span><?php _e('Only include themes with:', 'azc_ts'); ?></span>
				<input type="text" name="<?php echo $this->get_field_name('with'); ?>" id="<?php echo $this->get_field_id('with'); ?>" value="<?php echo esc_attr($instance['with']); ?>" />
			</label>
		</p>
		<?php
	}
}

class azc_ts_ThemeSwitcher {

	function azc_ts_ThemeSwitcher()
	{
		add_action('init', array(&$this, 'set_theme_cookie'));
		add_action('widgets_init', array(&$this, 'event_widgets_init'));
		
		add_filter('stylesheet', array(&$this, 'get_stylesheet'));
		add_filter('template', array(&$this, 'get_template'));
	}

	function event_widgets_init()
	{
		register_widget('azc_ts_ThemeSwitcherWidget');
	}
	
	function get_stylesheet($stylesheet = '') {
		$themename = $this->get_theme();

		if (empty($themename)) {
			return $stylesheet;
		}

		$themes = wp_get_themes();
		foreach ( $themes as $stylesheetdata => $theme_data ) {
			if($themename == $theme_data->get('Name')){
				$theme = wp_get_theme($theme_data->get_stylesheet());
			}
		}	
		
		if (empty($theme)) {
			return $stylesheet;
		}


		// Don't let people peek at unpublished themes.
		if (isset($theme['Status']) && $theme['Status'] != 'publish')
			return $stylesheet;	
		return $theme['Stylesheet'];
	}

	function get_template($template) {
		$themename = $this->get_theme();

		if (empty($themename)) {
			return $template;
		}

		$themes = wp_get_themes();
		foreach ( $themes as $stylesheetdata => $theme_data ) {
			if($themename == $theme_data->get('Name')){
				$theme = wp_get_theme($theme_data->get_stylesheet);
			}
		}
		
		if ( empty( $theme ) ) {
			return $template;
		}

		// Don't let people peek at unpublished themes.
		if (isset($theme['Status']) && $theme['Status'] != 'publish')
			return $template;		

		return $theme['Template'];
	}

	function get_theme() {
		if ( ! empty($_COOKIE["wptheme" . COOKIEHASH] ) ) {
			return $_COOKIE["wptheme" . COOKIEHASH];
		} else {
			return '';
		}
	}

	function set_theme_cookie() {
		load_plugin_textdomain('azc_ts');
		$expire = time() + 30000000;
		if ( ! empty($_GET["wptheme"] ) ) {
			setcookie(
				"wptheme" . COOKIEHASH,
				stripslashes($_GET["wptheme"]),
				$expire,
				COOKIEPATH
			);
			$redirect = remove_query_arg('wptheme');
			wp_redirect($redirect);
			exit;
		}
	}
	
	function theme_switcher_markup($style = "text", $instance = array()) {
		if ( ! $theme_data = wp_cache_get('themes-data', 'azc_ts') ) {
			$themes = (array) wp_get_themes( array( 'allowed' => 'site' ) );
			if ( function_exists('is_site_admin') ) {
				$allowed_themes = (array) get_site_option( 'allowedthemes' );
				foreach( $themes as $key => $theme ) {
				    if( isset( $allowed_themes[ wp_specialchars( $theme[ 'Stylesheet' ] ) ] ) == false ) {
						unset( $themes[ $key ] );
				    }
				}
			}

			$default_theme = wp_get_theme();

			$theme_data = array();
			foreach ((array) $themes as $theme_name => $data) {
				// Skip unpublished themes.
				if (empty($theme_name) || isset($themes[$theme_name]['Status']) && $themes[$theme_name]['Status'] != 'publish'){
				}else{
					if (substr($themes[$theme_name]['Name'],0,strlen($themes[$theme_name]['Name'])) != $instance['prefix']){
						if (strlen($instance['with']) == 0 or stripos($themes[$theme_name]['Name'], $instance['with']) !== false){
							$theme_data[str_replace('?',$_SERVER["REQUEST_URI"].'?',add_query_arg('wptheme', $themes[$theme_name]['Name'], get_option('home')))] = $data['Name'];
						}
					}
				}
			}
			
			asort($theme_data);

			wp_cache_set('themes-data', $theme_data, 'azc_ts');
		}
	
		$ts .= '';
		if ( $style == 'dropdown' ) {
			$ts .= '<div style="width: 90%; margin: auto; ">';
			$ts .= '<select style="width: 100%;" name="themeswitcher" onchange="location.href=this.options[this.selectedIndex].value;">';
		}else{
			$ts .= '<ul id="themeswitcher">';
		}

		foreach ($theme_data as $url => $theme_name) {
			if (
				! empty($_COOKIE["wptheme" . COOKIEHASH]) && $_COOKIE["wptheme" . COOKIEHASH] == $theme_name ||
				empty($_COOKIE["wptheme" . COOKIEHASH]) && ($theme_name == $default_theme)
			) {
				$pattern = 'dropdown' == $style ? '<option value="%1$s" selected="selected">%2$s</option>' : '<li>%2$s</li>';
			} else {
				$pattern = 'dropdown' == $style ? '<option value="%1$s">%2$s</option>' : '<li><a href="%1$s">%2$s</a></li>';
			}				
			$ts .= sprintf($pattern,
				esc_attr($url),
				esc_html($theme_name)
			);

		}

		if ( 'dropdown' == $style ) {
			$ts .= '</select>';
			$ts .= '</div>';
		}else{
			$ts .= '</ul>';
		}
		return $ts;
	}
}

$theme_switcher = new azc_ts_ThemeSwitcher();

function azc_ts_theme_switcher($type = '')
{
	global $theme_switcher;
	echo $theme_switcher->theme_switcher_markup($type);
}


// azurecurve menu
function azc_create_ts_plugin_menu() {
	global $admin_page_hooks;
    
	add_submenu_page( "azc-plugin-menus"
						,"Theme Switcher"
						,"Theme Switcher"
						,'manage_options'
						,"azc-ts"
						,"azc_ts_settings" );
}
add_action("admin_menu", "azc_create_ts_plugin_menu");

function azc_ts_settings() {
	if (!current_user_can('manage_options')) {
		$error = new WP_Error('not_found', __('You do not have sufficient permissions to access this page.' , 'azc_siw'), array('response' => '200'));
		if(is_wp_error($error)){
			wp_die($error, '', $error->get_error_data());
		}
    }
	?>
	<div id="azc-t-general" class="wrap">
			<h2>azurecurve Theme Switcher</h2>
			<p>
				<?php _e('This plugin allows users to switch themese on the front-end on a per user setting. Configure plugin via the included widget.', 'azc_siw'); ?>
			</p>
	</div>
	
<?php
}

?>