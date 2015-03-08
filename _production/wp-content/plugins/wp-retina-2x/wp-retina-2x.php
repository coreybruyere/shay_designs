<?php
/*
Plugin Name: WP Retina 2x
Plugin URI: http://www.meow.fr
Description: Make your images crisp and beautiful on Retina (High-DPI) displays.
Version: 3.0.4
Author: Jordy Meow
Author URI: http://www.meow.fr

Dual licensed under the MIT and GPL licenses:
http://www.opensource.org/licenses/mit-license.php
http://www.gnu.org/licenses/gpl.html

Originally developed for two of my websites: 
- Totoro Times (http://www.totorotimes.com) 
- Haikyo (http://www.haikyo.org)
*/

/**
 *
 * @author      Jordy Meow  <http://www.meow.fr>
 * @package     Wordpress
 * @subpackage	Administration
 *
 */

$wr2x_version = '3.0.4';
$wr2x_retinajs = '1.3.0';
$wr2x_picturefill = '2.2.0.2014.02.03';
$wr2x_retina_image = '1.4.1';

add_action( 'admin_menu', 'wr2x_admin_menu' );
add_action( 'wp_enqueue_scripts', 'wr2x_wp_enqueue_scripts' );
add_action( 'admin_enqueue_scripts', 'wr2x_wp_enqueue_scripts' );
add_filter( 'wp_generate_attachment_metadata', 'wr2x_wp_generate_attachment_metadata' );
add_action( 'delete_attachment', 'wr2x_delete_attachment' );
add_filter( 'update_option', 'wr2x_update_option' );
add_filter( 'generate_rewrite_rules', 'wr2x_generate_rewrite_rules' );
add_action( 'init', 'wr2x_init' );

register_deactivation_hook( __FILE__, 'wr2x_deactivate' );
register_activation_hook( __FILE__, 'wr2x_activate' );

require('wr2x_settings.php');

if ( is_admin() ) {
	require('wr2x_ajax.php');
	require('jordy_meow_footer.php');
}

if ( wr2x_getoption( "ignore_mobile", "wr2x_advanced", false ) && !class_exists( 'Mobile_Detect' ) )
	require('inc/Mobile_Detect.php'); 

if ( !wr2x_getoption( "hide_retina_dashboard", "wr2x_advanced", false ) )
	require('wr2x_retina-dashboard.php');

if ( !wr2x_getoption( "hide_retina_column", "wr2x_advanced", false ) )
	require('wr2x_media-library.php');

function wr2x_init() {
	load_plugin_textdomain( 'wp-retina-2x', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	
	if ( is_admin() ) {
		wp_register_style( 'wr2x-admin-css', plugins_url( '/wr2x_admin.css', __FILE__ ) );
		wp_enqueue_style( 'wr2x-admin-css' );
		if ( !wr2x_getoption( "retina_admin", "wr2x_advanced", false ) )
			return;
	}

	$method = wr2x_getoption( "method", "wr2x_advanced", 'retina.js' );

	if ( $method == "Picturefill" ) {
		add_action( 'wp_head', 'wr2x_picture_buffer_start' );
		add_action( 'wp_footer', 'wr2x_picture_buffer_end' );
	}

	else if ( $method == 'HTML Rewrite' ) {
		$is_retina = false;
		if ( isset( $_COOKIE['devicePixelRatio'] ) ) {
			$is_retina = ceil( floatval( $_COOKIE['devicePixelRatio'] ) ) > 1;
			if ( wr2x_getoption( "ignore_mobile", "wr2x_advanced", false ) ) {
				$mobileDetect = new Mobile_Detect();
				$is_retina = !$mobileDetect->isMobile();
			}
		}
		if ( $is_retina || wr2x_is_debug() ) {
			add_action( 'wp_head', 'wr2x_buffer_start' );
			add_action( 'wp_footer', 'wr2x_buffer_end' );
		}
	}

}

/**
 *
 * PICTURE METHOD
 *
 */ 

function wr2x_picture_buffer_start () {
	ob_start( "wr2x_picture_rewrite" );
	wr2x_log( "* HTML REWRITE FOR PICTUREFILL" );
}

function wr2x_picture_buffer_end () {
	ob_end_flush();
}

// Replace the IMG tags by PICTURE tags with SRCSET
function wr2x_picture_rewrite( $buffer ) {
	if ( !isset( $buffer ) || trim( $buffer ) === '' )
		return $buffer;
	if ( !function_exists( "str_get_html" ) )
		require('inc/simple_html_dom.php');

	$nodes_count = 0;
	$nodes_replaced = 0;
	$html = str_get_html( $buffer );
	foreach( $html->find( 'img' ) as $element ) {
		$nodes_count++;
		$parent = $element->parent();
		if ( $parent->tag == "picture" ) {
			wr2x_log("The img tag is inside a picture tag already. Tag ignored.");
			continue;
		}
		else {
			$img_pathinfo = wr2x_get_pathinfo_from_image_src( $element->src );
			$filepath = trailingslashit( ABSPATH ) . $img_pathinfo;
			$potential_retina = wr2x_get_retina( $filepath );
			$from = substr( $element, 0 );
			if ( $potential_retina != null ) {
				$retina_url = wr2x_from_system_to_url( $potential_retina );
				$img_url = trailingslashit( get_site_url() ) . $img_pathinfo;
				$element->srcset =  "$img_url, $retina_url 2x";
				if ( !wr2x_is_pro() || !wr2x_getoption( "picturefill_keep_src", "wr2x_advanced", false ) ) {
					$element->src = null;
				}
				$to = $element;
				$buffer = str_replace( trim( $from, "</> "), trim($to, "</> "), $buffer );
				wr2x_log( "The img tag '$from' was rewritten to '$to'" );
				$nodes_replaced++;
			}
			else {
				wr2x_log( "The img tag was not rewritten. No retina for '" . $element->src . "'." );
			}
		}
	}
	wr2x_log( "$nodes_replaced/$nodes_count were replaced." );
	return $buffer;
}

/**
 *
 * HTML REWRITE METHOD
 *
 */

function wr2x_buffer_start () {
	ob_start( "wr2x_html_rewrite" );
	wr2x_log( "* HTML REWRITE" );
}

function wr2x_buffer_end () {
	ob_end_flush();
}

// Replace the images by retina images (if available)
function wr2x_html_rewrite( $buffer ) {
	if ( !isset( $buffer ) || trim( $buffer ) === '' )
		return $buffer;
	$nodes_count = 0;
	$nodes_replaced = 0;
	$doc = new DOMDocument();
	@$doc->loadHTML( $buffer ); // = ($doc->strictErrorChecking = false;)
	$imageTags = $doc->getElementsByTagName('img');
	foreach ( $imageTags as $tag ) {
		$nodes_count++;
		$img_pathinfo = wr2x_get_pathinfo_from_image_src( $tag->getAttribute('src') );
		$filepath = trailingslashit( ABSPATH ) . $img_pathinfo;
		$potential_retina = wr2x_get_retina( $filepath );
		if ( $potential_retina != null ) {
			$retina_pathinfo = ltrim( str_replace( ABSPATH, "", $potential_retina ), '/' );
			$buffer = str_replace( $img_pathinfo, $retina_pathinfo, $buffer );
			wr2x_log( "The img src '$img_pathinfo' was replaced by '$retina_pathinfo'" );
			$nodes_replaced++;
		}
		else {
			wr2x_log( "The file '$potential_retina' was not found. Tag not modified." );
		}
	}
	wr2x_log( "$nodes_replaced/$nodes_count were replaced." );
	return $buffer;
}

/**
 *
 * ISSUES CALCULATION AND FUNCTIONS
 *
 */ 

// Function written by jappievw
// http://wordpress.org/support/topic/cant-find-retina-file-with-custom-uploads-constant?replies=3#post-5078892
function wr2x_get_pathinfo_from_image_src($image_src) {
	$site_url = trailingslashit( site_url() );
	if ( strpos( $image_src, $site_url ) === 0 ) {
		return substr( $image_src, strlen( $site_url ) );
	}
	else {
		$img_info = parse_url( $image_src );
		return ltrim( $img_info['path'], '/' );
	}
}

// Compares two images dimensions (resolutions) against each while accepting an margin error
function wr2x_are_dimensions_ok( $width, $height, $retina_width, $retina_height ) {
	$w_margin = $width - $retina_width;
	$h_margin = $height - $retina_height;
	return ( $w_margin >= -2 && $h_margin >= -2 );
}

// UPDATE THE ISSUE STATUS OF THIS ATTACHMENT
function wr2x_update_issue_status( $attachmentId, $issues = null, $info = null ) {
	if ( wr2x_is_ignore( $attachmentId ) )
		return;
	if ( $issues == null )
		$issues = wr2x_get_issues();
	if ( $info == null )
		$info = wr2x_retina_info( $attachmentId );
	$consideredIssue = in_array( $attachmentId, $issues );
	$realIssue = wr2x_info_has_issues( $info );
	if ( $consideredIssue && !$realIssue )
		wr2x_remove_issue( $attachmentId );
	else if ( !$consideredIssue && $realIssue )
		wr2x_add_issue( $attachmentId );
	return $realIssue;
}
 
function wr2x_get_issues() {
	$issues = get_transient( 'wr2x_issues' );
	if ( !$issues || !is_array( $issues ) ) {
		$issues = array();
		set_transient( 'wr2x_issues', $issues );
	}
	return $issues;
}
 
// CHECK IF THE 'INFO' OBJECT CONTAINS ISSUE (RETURN TRUE OR FALSE)
function wr2x_info_has_issues( $info ) {
	foreach ( $info as $aindex => $aval ) {
		if ( is_array( $aval ) || $aval == 'PENDING' )
			return true;
	}
	return false;
}

function wr2x_calculate_issues() {
	global $wpdb;
	$postids = $wpdb->get_col( "
		SELECT p.ID FROM $wpdb->posts p
		WHERE post_status = 'inherit'
		AND post_type = 'attachment'" . wr2x_create_sql_if_wpml_original() . "
		AND ( post_mime_type = 'image/jpeg' OR
			post_mime_type = 'image/jpg' OR
			post_mime_type = 'image/png' OR
			post_mime_type = 'image/gif' )
	" );
	$issues = array();
	foreach ( $postids as $id ) {
		$info = wr2x_retina_info( $id );
		if ( wr2x_info_has_issues( $info ) )
			array_push( $issues, $id );
		
	}
	set_transient( 'wr2x_ignores', array() );
	set_transient( 'wr2x_issues', $issues );
}

function wr2x_add_issue( $attachmentId ) {
	if ( wr2x_is_ignore( $attachmentId ) )
		return;
	$issues = wr2x_get_issues();
	if ( !in_array( $attachmentId, $issues ) ) {
		array_push( $issues, $attachmentId );
		set_transient( 'wr2x_issues', $issues );
	}
	return $issues;
}

function wr2x_remove_issue( $attachmentId, $onlyIgnore = false ) {
	$issues = array_diff( wr2x_get_issues(), array( $attachmentId ) );
	set_transient( 'wr2x_issues', $issues );
	if ( !$onlyIgnore )
		wr2x_remove_ignore( $attachmentId );
	return $issues;
}

// IGNORE

function wr2x_get_ignores( $force = false ) {
	$ignores = get_transient( 'wr2x_ignores' );
	if ( !$ignores || !is_array( $ignores ) ) {
		$ignores = array();
		set_transient( 'wr2x_ignores', $ignores );
	}
	return $ignores;
}

function wr2x_is_ignore( $attachmentId ) {
	$ignores = wr2x_get_ignores();
	return in_array( $attachmentId, wr2x_get_ignores() );
}

function wr2x_remove_ignore( $attachmentId ) {
	$ignores = wr2x_get_ignores();
	$ignores = array_diff( $ignores, array( $attachmentId ) );
	set_transient( 'wr2x_ignores', $ignores );
	return $ignores;
}

function wr2x_add_ignore( $attachmentId ) {
	$ignores = wr2x_get_ignores();
	if ( !in_array( $attachmentId, $ignores ) ) {
		array_push( $ignores, $attachmentId );
		set_transient( 'wr2x_ignores', $ignores );
	}
	wr2x_remove_issue( $attachmentId, true );
	return $ignores;
}

/**
 *
 * INFORMATION ABOUT THE RETINA IMAGE IN HTML
 *
 */

function wpr2x_html_get_basic_retina_info_full( $attachmentId, $retina_info ) {
	if ( !wr2x_getoption( "full_size", "wr2x_basics", false ) ) {
		return __( "N/A", "wp-retina-2x" );
	}
	$status = ( isset( $retina_info ) && isset( $retina_info['full-size'] ) ) ? $retina_info['full-size'] : 'IGNORED';
	if ( $status == 'EXISTS' ) {
		$fullsize_file = get_attached_file( $attachmentId );
		$retina_file = wr2x_get_retina( $fullsize_file );
		$retina = wr2x_from_system_to_url( $retina_file );
		return '<img src="' . $retina . '" />';
	}
	else if ( is_array( $status ) ) {
		return __( "<i>Required</i>", "wp-retina-2x" );
	}
	else if ( $status == 'IGNORED' ) {
		return __( "N/A", "wp-retina-2x" );
	}
	return $status;
}

// Information for the 'Media Sizes Retina-ized' Column in the Retina Dashboard
function wpr2x_html_get_basic_retina_info( $attachmentId, $retina_info ) {
	$sizes = wr2x_get_active_image_sizes();
	$result = '<ul class="retina-info">';
	foreach ( $sizes as $i => $status ) {
		$status = ( isset( $retina_info ) && isset( $retina_info[$i] ) ) ? $retina_info[$i] : null;
		if ( is_array( $status ) )
			$result .= '<li class="retina-issue" title="' . $i . '"></li>';
		else if ( $status == 'EXISTS' )
			$result .= '<li class="retina-exists" title="' . $i . '"></li>';
		else if ( $status == 'PENDING' )
			$result .= '<li class="retina-pending" title="' . $i . '"></li>';
		else if ( $status == 'MISSING' )
			$result .= '<li class="retina-missing" title="' . $i . '"></li>';
		else if ( $status == 'IGNORED' )
			$result .= '<li class="retina-ignored" title="' . $i . '"></li>';
		else {
			error_log( "Retina: This status is not recognized: " . $status );
		}
	}
	$result .= '</ul>';
	return $result;
}

// Information for Details in the Retina Dashboard
function wpr2x_html_get_details_retina_info( $post, $retina_info ) {

	if ( !wr2x_is_pro() ) {
		return "PRO VERSION ONLY<br /><br />You can buy a serial from here: <a target='_blank' href='http://apps.meow.fr/wp-retina-2x/'>WP Retina 2x</a>.<br />Then add this serial in the settings. That's all! :)<br />Thanks a lot for your support.";
	}

	$sizes = wr2x_get_image_sizes();
	$total = 0; $possible = 0; $issue = 0; $ignored = 0; $retina = 0;

	$postinfo = get_post( $post, OBJECT );
	$meta = wp_get_attachment_metadata( $post );
	$fullsize_file = get_attached_file( $post );
	$pathinfo_system = pathinfo( $fullsize_file );
	$pathinfo = pathinfo( $meta['file'] );
	$uploads = wp_upload_dir();
	$basepath_url = trailingslashit( $uploads['baseurl'] ) . $pathinfo['dirname'];

	if ( wr2x_getoption( "full_size", "wr2x_basics", false ) ) {
		$sizes['full-size']['file'] = $meta['file'];
		$sizes['full-size']['width'] = $meta['width'];
		$sizes['full-size']['height'] = $meta['height'];
		$meta['sizes']['full-size']['file'] = $meta['file'];
		$meta['sizes']['full-size']['width'] = $meta['width'];
		$meta['sizes']['full-size']['height'] = $meta['height'];
	}
	// else
	// 	$sizes['full-size'] = 'IGNORED';

	$result = "<p>This screen displays all the image sizes set-up by your WordPress configuration with the Retina details.</p>";
	$result .= "<br /><a target='_blank' href='" . trailingslashit( $uploads['baseurl'] ) . $meta['file'] . "'><img src='" . trailingslashit( $uploads['baseurl'] ) . $meta['file'] . "' height='100px' style='float: left; margin-right: 10px;' /></a><div class='base-info'>";
	$result .= "Title: <b>" . ( $postinfo->post_title ? $postinfo->post_title : '<i>Untitled</i>' ) . "</b><br />";
	$result .= "Full-size: <b>" . $meta['width'] . "×" . $meta['height'] . "</b><br />";
	$result .= "Image URL: <a target='_blank' href='" . trailingslashit( $uploads['baseurl'] ) . $meta['file'] . "'>" . trailingslashit( $uploads['baseurl'] ) . $meta['file'] . "</a><br />";
	$result .= "Image Path: " . $fullsize_file . "<br />";
	$result .= "</div><div style='clear: both;'></div><br />";

	$result .= "<div class='scrollable-info'>";
	foreach ( $sizes as $i => $sizemeta ) {
		$total++;
			
		$normal_file_system = "";
		$retina_file_system = "";
		$normal_file = "";
		$retina_file = "";
		$width = "";
		$height = "";
		if ( !isset( $meta['sizes'] ) ) {
			$statusText  = "The metadata is broken! This is not related to the retina plugin. You should probably use a plugin to re-generate the missing metadata and images.";
			$status = "BROKEN";
		}
		else if ( !isset( $meta['sizes'][$i] ) ) {
			$statusText  = "The image size '$i' could not be found. You probably changed your image sizes but this specific image was not re-build. This is not related to the retina plugin. You should probably use a plugin to re-generate the missing metadata and images.";
			$status = "BROKEN";
		}
		else {
			$normal_file_system = trailingslashit( $pathinfo_system['dirname'] ) . $meta['sizes'][$i]['file'];
			$retina_file_system = wr2x_get_retina( $normal_file_system );
			$normal_file = trailingslashit( $basepath_url ) . $meta['sizes'][$i]['file'];
			$retina_file = wr2x_from_system_to_url( $retina_file_system );
			$status = ( isset( $retina_info ) && isset( $retina_info[$i] ) ) ? $retina_info[$i] : null;
			$width = $meta['sizes'][$i]['width'];
			$height = $meta['sizes'][$i]['height'];
		}

		$result .= "<h3>";

		// Status Icon
		if ( is_array( $status ) && $i == 'full-size' ) {
			$result .= '<div class="retina-status-icon retina-missing"></div>';
			$statusText = "A retina version of the Full Size is required and needs to be uploaded (minimum " . $status['width'] . "x" . $status['height'] . "). You can drag & drop this image in 'Full-Size Retina Upload'.";
		}
		else if ( is_array( $status ) ) {
			$result .= '<div class="retina-status-icon retina-issue"></div>';
			$statusText = "The Full Size image is too small. An image of at least " . $status['width'] . "x" . $status['height'] . " is required but your Full Size image is " . $meta['width'] . "×" . $meta['height'] . ".";
			$issue++;
		}
		else if ( $status == 'EXISTS' ) {
			$result .= '<div class="retina-status-icon retina-exists"></div>';
			$statusText = "The retina file exists.";
			$retina++;
		}
		else if ( $status == 'PENDING' ) {
			$result .= '<div class="retina-status-icon retina-pending"></div>';
			$statusText = "The retina image can be created. Please use the 'GENERATE' button.";
			$possible++;
		}
		else if ( $status == 'MISSING' ) {
			$result .= '<div class="retina-status-icon retina-missing"></div>';
			$statusText = "The image created by WordPress is missing. The plugin will only create a retina image if the base image exists.";
			$total--;
		}
		else if ( $status == 'IGNORED' ) {
			$result .= '<div class="retina-status-icon retina-ignored"></div>';
			$statusText = "This size is ignored by your retina settings.";
			$ignored++;
			$total--;
		}

		$result .= "Size: $i</h3><p>$statusText</p>";

		if ( !is_array( $status ) && $status !== 'IGNORED' ) {
			$result .= "<table><tr><th>Normal (" . $width . "×" . $height. ")</th><th>Retina 2x (" . $width * 2 . "×" . $height * 2 . ")</th></tr><tr><td><a target='_blank' href='$normal_file'><img src='$normal_file' width='100'></a></td><td><a target='_blank' href='$retina_file'><img src='$retina_file' width='100'></a></td></tr></table>";
			$result .= "<p><small>";
			$result .= "Image URL: <a target='_blank' href='$normal_file'>$normal_file</a><br />";
			$result .= "Retina URL: <a target='_blank' href='$retina_file'>$retina_file</a><br />";
			$result .= "Image Path: $normal_file_system<br />";
			$result .= "Retina Path: $retina_file_system<br />";
			$result .= "</small></p>";
		}
	}
	$result .= "</table>";
	$result .= "</div>";
	return $result;
}

/**
 *
 * WP RETINA 2X CORE
 *
 */


function wr2x_from_system_to_url( $file ) {
	if ( empty( $file ) )
		return "";
	$retina_pathinfo = ltrim( str_replace( ABSPATH, "", $file ), '/' );
	$url = trailingslashit( get_site_url() ) . $retina_pathinfo;
	return $url;
}

function wr2x_admin_menu() {
	add_options_page( 'Retina', 'Retina', 'manage_options', 'wr2x_settings', 'wr2x_settings_page' );
}

function wr2x_get_image_sizes() {
	$sizes = array();
	global $_wp_additional_image_sizes;
	foreach (get_intermediate_image_sizes() as $s) {
		$crop = false;
		if (isset($_wp_additional_image_sizes[$s])) {
			$width = intval($_wp_additional_image_sizes[$s]['width']);
			$height = intval($_wp_additional_image_sizes[$s]['height']);
			$crop = $_wp_additional_image_sizes[$s]['crop'];
		} else {
			$width = get_option( $s . '_size_w' );
			$height = get_option( $s . '_size_h' );
			$crop = get_option( $s . '_crop' );
		}
		$sizes[$s] = array( 'width' => $width, 'height' => $height, 'crop' => $crop );
	}
	return $sizes;
}

function wr2x_get_active_image_sizes() {
	$sizes = wr2x_get_image_sizes();
	$active_sizes = array();
	$ignore = wr2x_getoption( "ignore_sizes", "wr2x_basics", array() );
	foreach ( $sizes as $name => $attr ) {
		if ( !in_array( $name, $ignore ) ) {
			$active_sizes[$name] = $attr;
		}
	}
	return $active_sizes;
}

function wr2x_is_wpml_installed() {
	return function_exists( 'icl_object_id' ) && !class_exists( 'Polylang' );
}

// SQL Query if WPML with an AND to check if the p.ID (p is attachment) is indeed an original
// That is to limit the SQL that queries all the attachments
function wr2x_create_sql_if_wpml_original() {
	$whereIsOriginal = "";
	if ( wr2x_is_wpml_installed() ) {
		global $wpdb;
		global $sitepress;
		$tbl_wpml = $wpdb->prefix . "icl_translations";
		$language = $sitepress->get_default_language();
		$whereIsOriginal = " AND p.ID IN (SELECT element_id FROM $tbl_wpml WHERE element_type = 'post_attachment' AND language_code = '$language') ";
	}
	return $whereIsOriginal;
}

function wr2x_is_debug() {
	static $debug = -1;
	if ( $debug == -1 ) {
		$debug = wr2x_getoption( "debug", "wr2x_advanced", false );
	}
	return $debug && $debug == "on";
}

function wr2x_log( $data ) {
	if ( wr2x_is_debug() ) {
		$fh = fopen( trailingslashit( WP_PLUGIN_DIR ) . 'wp-retina-2x/wp-retina-2x.log', 'a' );
		$date = date( "Y-m-d H:i:s" );
		fwrite( $fh, "$date: {$data}\n" );
		fclose( $fh );
	}
}

// Based on http://wordpress.stackexchange.com/questions/6645/turn-a-url-into-an-attachment-post-id
function wr2x_get_attachment_id( $file ) {
    $query = array(
        'post_type' => 'attachment',
		'meta_query' => array(
			array(
				'key'		=> '_wp_attached_file',
				'value'		=> ltrim( $file, '/' )
			)
		)
    );
    $posts = get_posts( $query );
    foreach( $posts as $post )
		return $post->ID;
    return false;
}

// Return the retina extension followed by a dot
function wr2x_retina_extension() {
	return '@2x.';	
}

// Return the retina file if there is any
function wr2x_get_retina( $file ) {
	$pathinfo = pathinfo( $file ) ;
	$retina_file = trailingslashit( $pathinfo['dirname'] ) . $pathinfo['filename'] . wr2x_retina_extension() . $pathinfo['extension'];
	if ( file_exists( $retina_file ) ) {
		return $retina_file;
	}
	else {
		wr2x_log( "Retina file at '{$retina_file}' does not exist." );
		return null;
	}
}

function wr2x_is_image_meta( $meta ) {
	if ( !isset( $meta ) )
		return false;
	if ( !isset( $meta['sizes'] ) )
		return false;
	if ( !isset( $meta['width'], $meta['height'] ) ) {
		wr2x_log( "[WARN] No width and height in the metadata for #" . $id . "." );
		return false;
	}
	return true;
}

function wr2x_retina_info( $id ) {
	$result = array();
	$meta = wp_get_attachment_metadata( $id );
	if ( !wr2x_is_image_meta( $meta ) )
		return $result;
	$original_width = $meta['width'];
	$original_height = $meta['height'];
	$sizes = wr2x_get_image_sizes();
	$required_files = true;
	$originalfile = get_attached_file( $id );
	$pathinfo = pathinfo( $originalfile );
	$basepath = $pathinfo['dirname'];
	$ignore = wr2x_getoption( "ignore_sizes", "wr2x_basics", array() );

	// Full-Size (if required in the settings)
	if ( wr2x_getoption( "full_size", "wr2x_basics", false ) && wr2x_is_pro() ) {
		$retina_file = trailingslashit( $pathinfo['dirname'] ) . $pathinfo['filename'] . wr2x_retina_extension() . $pathinfo['extension'];
		if ( $retina_file && file_exists( $retina_file ) )
			$result['full-size'] = 'EXISTS';
		else if ( $retina_file )
			$result['full-size'] = array( 'width' => $original_width * 2, 'height' => $original_height * 2 );
	}

	if ( $sizes ) {
		foreach ($sizes as $name => $attr) {
			if ( in_array( $name, $ignore ) ) {
				$result[$name] = 'IGNORED';
				continue;
			}
			// Check if the file related to this size is present
			$pathinfo = null;
			$retina_file = null;
			
			if ( isset( $meta['sizes'][$name]['width'] ) && isset( $meta['sizes'][$name]['height']) && isset($meta['sizes'][$name]) && isset($meta['sizes'][$name]['file']) && file_exists( trailingslashit( $basepath ) . $meta['sizes'][$name]['file'] ) ) {
				$normal_file = trailingslashit( $basepath ) . $meta['sizes'][$name]['file'];
				$pathinfo = pathinfo( $normal_file ) ;
				$retina_file = trailingslashit( $pathinfo['dirname'] ) . $pathinfo['filename'] . wr2x_retina_extension() . $pathinfo['extension'];
			}
			// None of the file exist
			else {
				$result[$name] = 'MISSING';
				$required_files = false;
				continue;
			}
			
			// The retina file exists
			if ( $retina_file && file_exists( $retina_file ) ) {
				$result[$name] = 'EXISTS';
				continue;
			}
			// The size file exists
			else if ( $retina_file )
				$result[$name] = 'PENDING';
			
			// The retina file exists
			$required_width = $meta['sizes'][$name]['width'] * 2;
			$required_height = $meta['sizes'][$name]['height'] * 2;
			if ( !wr2x_are_dimensions_ok( $original_width, $original_height, $required_width, $required_height ) ) {
				$result[$name] = array( 'width' => $required_width, 'height' => $required_height );
			}
		}
	}

	return $result;
}
 
function wr2x_delete_attachment( $attach_id ) {
	$meta = wp_get_attachment_metadata( $attach_id );
	wr2x_delete_images( $meta );
	wr2x_remove_issue( $attach_id );
}
 
function wr2x_wp_generate_attachment_metadata( $meta ) {
	if (wr2x_getoption( "auto_generate", "wr2x_basics", true ) == true)
		if ( wr2x_is_image_meta( $meta ) )
			wr2x_generate_images( $meta );
    return $meta;
}

function wr2x_generate_images( $meta ) {
	require('wr2x_vt_resize.php');
	global $_wp_additional_image_sizes;
	$sizes = wr2x_get_image_sizes();
	if ( !isset( $meta['file'] ) )
		return;
	$originalfile = $meta['file'];
	$uploads = wp_upload_dir();
	$pathinfo = pathinfo( $originalfile );
	$original_basename = $pathinfo['basename'];
	$basepath = trailingslashit( $uploads['basedir'] ) . $pathinfo['dirname'];
	$ignore = wr2x_getoption( "ignore_sizes", "wr2x_basics", array() );
	$issue = false;
	$id = wr2x_get_attachment_id( $meta['file'] );
	
	wr2x_log("* GENERATE RETINA FOR ATTACHMENT '{$meta['file']}'");
	wr2x_log( "Full Size is {$original_basename}." );

	foreach ( $sizes as $name => $attr ) {
		if ( in_array( $name, $ignore ) ) {
			wr2x_log( "Retina for {$name} ignored (settings)." );
			continue;
		}
		// Is the file related to this size there?
		$pathinfo = null;
		$retina_file = null;
		
		if ( isset( $meta['sizes'][$name] ) && isset( $meta['sizes'][$name]['file'] ) ) {
			$normal_file = trailingslashit( $basepath ) . $meta['sizes'][$name]['file'];
			$pathinfo = pathinfo( $normal_file ) ;
			$retina_file = trailingslashit( $pathinfo['dirname'] ) . $pathinfo['filename'] . wr2x_retina_extension() . $pathinfo['extension'];
		}
		
		if ( $retina_file && file_exists( $retina_file ) ) {
			wr2x_log( "Base for {$name} is '{$normal_file }'." );
			wr2x_log( "Retina for {$name} already exists: '$retina_file'." );
			continue;
		}
		if ( $retina_file ) {
			$originalfile = trailingslashit( $pathinfo['dirname'] ) . $original_basename;
			
			if ( !file_exists( $originalfile ) ) {
				wr2x_log( "[ERROR] Original file '{$originalfile}' cannot be found." );
				return $meta;
			}

			// Maybe that new image is exactly the size of the original image.
			// In that case, let's make a copy of it.
			if ( $meta['sizes'][$name]['width'] * 2 == $meta['width'] && $meta['sizes'][$name]['height'] * 2 == $meta['height'] ) {
				copy ( $originalfile, $retina_file );
				wr2x_log( "Retina for {$name} created: '{$retina_file}' (as a copy of the full-size)." );
			}
			// Otherwise let's resize (if the original size is big enough).
			else if ( wr2x_are_dimensions_ok( $meta['width'], $meta['height'], $meta['sizes'][$name]['width'] * 2, $meta['sizes'][$name]['height'] * 2 ) ) {
				// Change proposed by Nicscott01, slighlty modified by Jordy (+isset)
				// (https://wordpress.org/support/topic/issue-with-crop-position?replies=4#post-6200271)
				$crop = isset( $_wp_additional_image_sizes[$name] ) ? $_wp_additional_image_sizes[$name]['crop'] : true;
				$customCrop = null;
				
				// Support for Manual Image Crop
				// If the size of the image was manually cropped, let's keep it.
				if ( class_exists( 'ManualImageCrop' ) && isset( $meta['micSelectedArea'] ) && isset( $meta['micSelectedArea'][$name] ) && isset( $meta['micSelectedArea'][$name]['scale'] ) ) {
					$customCrop = $meta['micSelectedArea'][$name];
				}
				$image = wr2x_vt_resize( $originalfile, $meta['sizes'][$name]['width'] * 2,
					$meta['sizes'][$name]['height'] * 2, $crop, $retina_file, $customCrop );
			}			
			if ( !file_exists( $retina_file ) ) {
				wr2x_log( "[ERROR] Retina for {$name} could not be created. Full Size is " . $meta['width'] . "x" . $meta['height'] . " but Retina requires a file of at least " . $meta['sizes'][$name]['width'] * 2 . "x" . $meta['sizes'][$name]['height'] * 2 . "." );
				$issue = true;
			}
			else {
				do_action( 'wr2x_retina_file_added', $id, $retina_file );
				wr2x_log( "Retina for {$name} created: '{$retina_file}'." );
			}
		} else {
			wr2x_log( "[ERROR] Base file '{$name}' cannot be found here: '{$normal_file}'." );
		}
	}
	
	// Checks attachment ID + issues
	if ( !$id )
		return $meta;
	if ( $issue )
		wr2x_add_issue( $id );
	else
		wr2x_remove_issue( $id );
   return $meta;
}

function wr2x_delete_images( $meta ) {
	if ( !wr2x_is_image_meta( $meta ) )
		return $meta;
	$sizes = $meta['sizes'];
	if ( !$sizes || !is_array( $sizes ) )
		return $meta;
	wr2x_log("* DELETE RETINA FOR ATTACHMENT '{$meta['file']}'");
	$originalfile = $meta['file'];
	$id = wr2x_get_attachment_id( $originalfile );
	$pathinfo = pathinfo( $originalfile );
	$uploads = wp_upload_dir();
	$basepath = trailingslashit( $uploads['basedir'] ) . $pathinfo['dirname'];
	foreach ( $sizes as $name => $attr ) {
		$pathinfo = pathinfo( $attr['file'] );
		$retina_file = $pathinfo['filename'] . wr2x_retina_extension() . $pathinfo['extension'];
		if ( file_exists( trailingslashit( $basepath ) . $retina_file ) ) {
			$fullpath = trailingslashit( $basepath ) . $retina_file;
			unlink( $fullpath );
			do_action( 'wr2x_retina_file_removed', $id, $retina_file );
			wr2x_log("Deleted '$fullpath'.");
		}
	}
    return $meta;
}

function wr2x_activate() {
	global $wp_rewrite;
	$wp_rewrite->flush_rules();
}

function wr2x_deactivate() {
	remove_filter( 'generate_rewrite_rules', 'wr2x_generate_rewrite_rules' );
	global $wp_rewrite;
	$wp_rewrite->flush_rules();
}

/**
 *
 * PRO 
 * Come on, it's only 5$ :'(
 *
 */

function wr2x_is_pro() {
	$validated = get_transient( 'wr2x_validated' );
	if ( !$validated ) {
		$subscr_id = get_option( 'wr2x_pro_serial', "" );
		if ( !empty( $subscr_id ) )
			return wr2x_validate_pro( wr2x_getoption( "subscr_id", "wr2x_pro", array() ) );
		return false;
	}
	return true;
}

function wr2x_validate_pro( $subscr_id ) {
	if ( empty( $subscr_id ) ) {
		delete_option( 'wr2x_pro_serial', "" );
		delete_option( 'wr2x_pro_status', "" );
		return false;
	}
	require_once ABSPATH . WPINC . '/class-IXR.php';
	$client = new IXR_Client( 'http://apps.meow.fr/xmlrpc.php' );
	if ( !$client->query( 'meow_sales.auth', $subscr_id, 'retina', get_site_url() ) ) { 
		update_option( 'wr2x_pro_serial', "" );
		update_option( 'wr2x_pro_status', "A network error: " . $client->getErrorMessage() );
		set_transient( 'wr2x_validated', false, 0 );
		return false;
	}
	$post = $client->getResponse();
	if ( !$post['success'] ) {
		if ( $post['message_code'] == "NO_SUBSCRIPTION" ) {
			$status = __( "Your serial does not seem right." );
		}
		else if ( $post['message_code'] == "NOT_ACTIVE" ) {
			$status = __( "Your subscription is not active." );
		}
		else if ( $post['message_code'] == "TOO_MANY_URLS" ) {
			$status = __( "Too many URLs are linked to your subscription." );
		}
		else {
			$status = "There is a problem with your subscription.";
		}
		update_option( 'wr2x_pro_serial', "" );
		update_option( 'wr2x_pro_status', $status );
		set_transient( 'wr2x_validated', false, 0 );
		return false;
	}
	set_transient( 'wr2x_validated', $subscr_id, 3600 * 24 * 100 );
	update_option( 'wr2x_pro_serial', $subscr_id );
	update_option( 'wr2x_pro_status', __( "Your subscription is enabled." ) );
	return true;
}

/**
 *
 * LOAD SCRIPTS IF REQUIRED
 *
 */

function wr2x_wp_enqueue_scripts () {
	global $wr2x_version, $wr2x_retinajs, $wr2x_retina_image, $wr2x_picturefill;
	$method = wr2x_getoption( "method", "wr2x_advanced", 'retina.js' );
	
	if ( is_admin() && !wr2x_getoption( "retina_admin", "wr2x_advanced", false ) )
			return;

	// Picturefill
	if ( $method == "Picturefill" ) {
		if ( wr2x_is_debug() )
			wp_enqueue_script( 'wr2x-debug', plugins_url( '/js/debug.js', __FILE__ ), array(), $wr2x_version, false );
		if ( !wr2x_getoption( "picturefill_noscript", "wr2x_advanced", false ) )
			wp_enqueue_script( 'picturefill', plugins_url( '/js/picturefill.min.js', __FILE__ ), array(), $wr2x_picturefill, true );
		return;
	}

	// Debug + HTML Rewrite = No JS!
	if ( wr2x_is_debug() && $method == "HTML Rewrite" ) {
		return;
	}

	// Debug mode, we force the devicePixelRatio to be Retina
	if ( wr2x_is_debug() )
		wp_enqueue_script( 'wr2x-debug', plugins_url( '/js/debug.js', __FILE__ ), array(), $wr2x_version, false );
	// Not Debug Mode + Ignore Mobile
	else if ( wr2x_getoption( "ignore_mobile", "wr2x_advanced", false ) ) {
		$mobileDetect = new Mobile_Detect();
		if ( $mobileDetect->isMobile() )
			return;
	}

	// Retina-Images and HTML Rewrite both need the devicePixelRatio cookie on the server-side
	if ( $method == "Retina-Images" || $method == "HTML Rewrite" )
		wp_enqueue_script( 'retina-images', plugins_url( '/js/retina-cookie.js', __FILE__ ), array(), $wr2x_retina_image, false );
	
	// Retina.js only needs itself
	if ($method == "retina.js")
		wp_enqueue_script( 'retinajs', plugins_url( '/js/retina.js', __FILE__ ), array(), $wr2x_retinajs, true );
}

?>