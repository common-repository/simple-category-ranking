<?php
/**
 * Plugin Name: Simple Category Ranking
 * Plugin URI: https://ruana.co.jp/simple-category-ranking
 * Description: This is a better way to show category ranking.
 * Version: 1.2.0
 * Author: Ruana LLC
 * Author URI: https://ruana.co.jp/
 * Text Domain: simple-category-ranking
 *
 * Copyright 2018 Ruana LLC (email : info@ruana.co.jp)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * Initialize plugin and all settings
 */
function scr_init() {
	require_once( dirname( __FILE__ ) . '/includes/functions.php' );

	/**
	 * Publisher
	 */
	require_once( dirname( __FILE__ ) . '/includes/class-simple-category-ranking.php' );

	add_action( 'widgets_init', array('SimpleCategoryRanking','register_widget'));
	add_action('wp_head',array('SimpleCategoryRanking','set_post_views'));
	add_action( 'wp_enqueue_scripts', 'scr_load_plugin_css' );

}

add_action( 'plugins_loaded', 'scr_init' );


function scr_load_plugin_css() {
    $plugin_url = plugin_dir_url( __FILE__ );
    wp_enqueue_style( 'scr-style', $plugin_url . 'css/scr-style.css' );
}
