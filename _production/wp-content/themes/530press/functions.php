<?php
/**
 * Roots includes
 *
 * The $roots_includes array determines the code library included in your theme.
 * Add or remove files to the array as needed. Supports child theme overrides.
 *
 * Please note that missing files will produce a fatal error.
 *
 * @link https://github.com/roots/roots/pull/1042
 */
$roots_includes = array(
  'lib/functions/utils.php',           // Utility functions
  'lib/functions/init.php',            // Initial theme setup and constants
  'lib/functions/wrapper.php',         // Theme wrapper class
  'lib/functions/sidebar.php',         // Sidebar class
  'lib/functions/config.php',          // Configuration
  'lib/functions/activation.php',      // Theme activation
  'lib/functions/titles.php',          // Page titles
  'lib/functions/nav.php',             // Custom nav modifications
  'lib/functions/gallery.php',         // Custom [gallery] modifications
  'lib/functions/comments.php',        // Custom comments modifications
  'lib/functions/scripts.php',         // Scripts and stylesheets
  'lib/functions/extras.php',          // Custom functions
  'lib/functions/pagination.php',      // Custom Pagination
  'lib/functions/pip.php',              // WooCommerce related functions
  'lib/functions/woo.php'              // WooCommerce related functions

);

foreach ($roots_includes as $file) {
  if (!$filepath = locate_template($file)) {
    trigger_error(sprintf(__('Error locating %s for inclusion', 'roots'), $file), E_USER_ERROR);
  }

  require_once $filepath;
}
unset($file, $filepath);
