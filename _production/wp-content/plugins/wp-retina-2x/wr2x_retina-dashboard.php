<?php

add_action( 'admin_menu', 'wr2x_admin_menu_dashboard' );

/**
 *
 * RETINA DASHBOARD
 *
 */

function wr2x_admin_menu_dashboard () {
	$refresh = isset ( $_GET[ 'refresh' ] ) ? $_GET[ 'refresh' ] : 0;
	$ignore = isset ( $_GET[ 'ignore' ] ) ? $_GET[ 'ignore' ] : false;
	if ( $ignore )
		wr2x_add_ignore( $ignore );
	if ( $refresh )
		wr2x_calculate_issues();
	$flagged = count( wr2x_get_issues() );
	$warning_title = __( "Retina images", 'wp-retina-2x' );
	$menu_label = sprintf( __( 'Retina %s' ), "<span class='update-plugins count-$flagged' title='$warning_title'><span class='update-count'>" . number_format_i18n( $flagged ) . "</span></span>" );
	add_media_page( 'Retina', $menu_label, 'manage_options', 'wp-retina-2x', 'wpr2x_wp_retina_2x' ); 
}

function wpr2x_wp_retina_2x() {
	$view = isset ( $_GET[ 'view' ] ) ? $_GET[ 'view' ] : 'issues';
	$paged = isset ( $_GET[ 'paged' ] ) ? $_GET[ 'paged' ] : 1;
	$s = isset ( $_GET[ 's' ] ) ? $_GET[ 's' ] : null;
	$issues = $count = 0;
	$posts_per_page = 15; // TODO: HOW TO GET THE NUMBER OF MEDIA PER PAGES? IT IS NOT get_option('posts_per_page');
	$issues = wr2x_get_issues();
	$ignored = wr2x_get_ignores();
	
	?>
	<div class='wrap'>
	<?php jordy_meow_donation(true); ?>
	<div id="icon-upload" class="icon32"><br></div>
	<h2>Retina Dashboard <?php by_jordy_meow(); ?></h2>

	<?php 
	
	if ( $view == 'issues' ) {
		global $wpdb;
		$totalcount = $wpdb->get_var( $wpdb->prepare( "
			SELECT COUNT(*)
			FROM $wpdb->posts p
			WHERE post_status = 'inherit'
			AND post_type = 'attachment'" . wr2x_create_sql_if_wpml_original() . "
			AND post_title LIKE %s
			AND ( post_mime_type = 'image/jpeg' OR
			post_mime_type = 'image/png' OR
			post_mime_type = 'image/gif' )
		", '%' . $s . '%' ) );
		$postin = count( $issues ) < 1 ? array( -1 ) : $issues;
		$query = new WP_Query( 
			array( 
				'post_status' => 'inherit',
				'post_type' => 'attachment',
				'post__in' => $postin,
				'paged' => $paged,
				'posts_per_page' => $posts_per_page,
				's' => $s
			)
		);
	} 
	else if ( $view == 'ignored' ) {
		global $wpdb;
		$totalcount = $wpdb->get_var( $wpdb->prepare( "
			SELECT COUNT(*)
			FROM $wpdb->posts p
			WHERE post_status = 'inherit'
			AND post_type = 'attachment'" . wr2x_create_sql_if_wpml_original() . "
			AND post_title LIKE %s
			AND ( post_mime_type = 'image/jpeg' OR
			post_mime_type = 'image/jpg' OR
			post_mime_type = 'image/png' OR
			post_mime_type = 'image/gif' )
		", '%' . $s . '%' ) );
		$postin = count( $ignored ) < 1 ? array( -1 ) : $ignored;
		$query = new WP_Query( 
			array( 
				'post_status' => 'inherit',
				'post_type' => 'attachment',
				'post__in' => $postin,
				'paged' => $paged,
				'posts_per_page' => $posts_per_page,
				's' => $s
			)
		);
	} 
	else {
		$query = new WP_Query( 
			array( 
				'post_status' => 'inherit',
				'post_type' => 'attachment',
				'post_mime_type' => 'image/jpeg,image/gif,image/jpg,image/png',
				'paged' => $paged,
				'posts_per_page' => $posts_per_page,
				's' => $s
			)
		);

		//$s
		$totalcount = $query->found_posts;
	}

	$issues_count = count( $issues );

	// If 'search', then we need to clean-up the issues count
	if ( $s && $issues_count > 0 ) {
		global $wpdb;
		$issues_count = $wpdb->get_var( $wpdb->prepare( "
			SELECT COUNT(*)
			FROM $wpdb->posts p
			WHERE id IN ( " . implode( ',', $issues ) . " )" . wr2x_create_sql_if_wpml_original() . "
			AND post_title LIKE %s
		", '%' . $s . '%' ) );
	}

	$results = array();
	$count = $query->found_posts;
	$pagescount = $query->max_num_pages;
	foreach ( $query->posts as $post ) {
		$info = wr2x_retina_info( $post->ID );
		array_push( $results, array( 'post' => $post, 'info' => $info ) );		
	}
	?>

	<style>
		.widefat td {
			padding: 5px 6px 0px 6px;
		}

		.widefat td .button {
			margin-right: 2px;
		}

		.widefat td .button:last-child {
			margin-right: 0px;
		}

		.subsubsub #icl_subsubsub, .subsubsub br {
			display: none;
		}
	</style>

	<div style='background: #FFF; padding: 5px; border-radius: 4px; height: 28px; box-shadow: 0px 0px 6px #C2C2C2;'>
		
		<!-- GENERATE ALL -->
		<a id='wr2x_generate_button_all' onclick='wr2x_generate_all()' class='button-primary' style='float: left;'><img style='position: relative; top: 3px; left: -2px; margin-right: 3px; width: 16px; height: 16px;' src='<?php echo trailingslashit( WP_PLUGIN_URL ) . trailingslashit( 'wp-retina-2x/img'); ?>photo-album--plus.png' /><?php _e("Generate", 'wp-retina-2x'); ?></a>
		
		<!-- SEARCH -->
		<form id="posts-filter" action="upload.php" method="get">
			<p class="search-box" style='margin-left: 5px; float: left;'>
				<input type="search" name="s" value="<?php echo $s ? $s : ""; ?>">
				<input type="hidden" name="page" value="wp-retina-2x">
				<input type="hidden" name="view" value="<?php echo $view; ?>">
				<input type="hidden" name="paged" value="<?php echo $paged; ?>">
				<input type="submit" class="button" value="Search">
			</p>
		</form>

		<!-- REMOVE BUTTON ALL -->
		<a id='wr2x_remove_button_all' onclick='wr2x_delete_all()' class='button button-red' style='float: right;'><img style='position: relative; top: 3px; left: -2px; margin-right: 3px; width: 16px; height: 16px;' src='<?php echo trailingslashit( WP_PLUGIN_URL ) . trailingslashit( 'wp-retina-2x/img'); ?>burn.png' /><?php _e("Delete all @2x", 'wp-retina-2x'); ?></a>

		<!-- REFRESH -->
		<a id='wr2x_refresh' href='?page=wp-retina-2x&view=issues&refresh=true' class='button-primary' style='float: right; margin-right: 5px;'><img style='position: relative; top: 3px; left: -2px; margin-right: 3px; width: 16px; height: 16px;' src='<?php echo trailingslashit( WP_PLUGIN_URL ) . trailingslashit( 'wp-retina-2x/img'); ?>refresh.png' /><?php _e("Refresh issues", 'wp-retina-2x'); ?></a>

		<!-- PROGRESS -->
		<span style='margin-left: 12px; font-size: 15px; top: 5px; position: relative; color: #747474;' id='wr2x_progression'></span>
		
	</div>

	<?php 
		if (isset ( $_GET[ 'refresh' ] ) ? $_GET[ 'refresh' ] : 0) {
			echo "<div class='updated' style='margin-top: 20px;'><p>";
			_e( "Issues has been refreshed.", 'wp-retina-2x' );
			echo "</p></div>";
		}
	?>
	
	<p><?php _e("You can upload/replace the images by drag & drop on the grid.", 'wp-retina-2x'); ?></p>

	<div id='wr2x-pages'>
	<?php
	echo paginate_links(array(  
	  'base' => '?page=wp-retina-2x&s=' . urlencode($s) . '&view=' . $view . '%_%',
      'current' => $paged,
      'format' => '&paged=%#%',
      'total' => $pagescount,
      'prev_next' => false
    ));  
	?>
	</div>
	
	<ul class="subsubsub">
		<li class="all"><a <?php if ( $view == 'all' ) echo "class='current'"; ?> href='?page=wp-retina-2x&s=<?php echo $s; ?>&view=all'><?php _e( "All", 'wp-retina-2x' ); ?></a><span class="count">(<?php echo $totalcount; ?>)</span></li> |
		<li class="all"><a <?php if ( $view == 'issues' ) echo "class='current'"; ?> href='?page=wp-retina-2x&s=<?php echo $s; ?>&view=issues'><?php _e( "Issues", 'wp-retina-2x' ); ?></a><span class="count">(<?php echo $issues_count; ?>)</span></li> |
		<li class="all"><a <?php if ( $view == 'ignored' ) echo "class='current'"; ?> href='?page=wp-retina-2x&s=<?php echo $s; ?>&view=ignored'><?php _e( "Ignored", 'wp-retina-2x' ); ?></a><span class="count">(<?php echo count( $ignored ); ?>)</span></li>
	</ul>
	<table class='wp-list-table widefat fixed media'>
		<thead><tr>
			<?php
			echo "<th style='width: 64px;'></th>";
			echo "<th style='font-size: 11px; font-family: Verdana; width: 200px;'>" . __( "Base image", 'wp-retina-2x' ) . "</th>";

			echo "<th style='font-size: 11px; font-family: Verdana;'>" . __( "Retina for Media sizes", 'wp-retina-2x' ) . "</th>";
			echo "<th style='font-size: 11px; font-family: Verdana;'>" . __( "Retina for Base image", 'wp-retina-2x' ) . "</th>";
			/*
			
			foreach ($sizes as $name => $attr) {
				if ( !in_array( $name, $ignore_cols ) )
					echo "<th style='width: 80px; font-size: 11px; font-family: Verdana;' class='manage-column'>" . $name . "</th>";
			}
			*/
			
			echo "<th style='font-size: 11px; font-family: Verdana; width: 152px;'>" . __( "Actions", 'wp-retina-2x' ) . "</th>";
			?>
		</tr></thead>
		<tbody>
			<?php
			foreach ($results as $index => $attr) {
				$post = $attr['post'];
				$retina_info = $attr['info'];
				$meta = wp_get_attachment_metadata( $post->ID );
				// Let's clean the issues status
				if ( $view != 'issues' ) {
					wr2x_update_issue_status( $post->ID, $issues, $info );
				}
				if ( isset( $meta ) && isset( $meta['width'] ) ) {
					$original_width = $meta['width'];
					$original_height = $meta['height'];
				}
				
				$attachmentsrc = wp_get_attachment_image_src( $post->ID, 'thumbnail' );
				echo "<tr class='wr2x-file-row' postId='" . $post->ID . "'>";
				echo "<td class='wr2x-image'><img style='max-width: 64px; max-height: 64px;' src='" . $attachmentsrc[0] . "' /></td>";
				echo "<td class='wr2x-title'><a style='position: relative; top: -2px;' href='media.php?attachment_id=" . $post->ID . "&action=edit'>" . 
					$post->post_title . '<br />' .
					"<span style='font-size: 9px; line-height: 10px; display: block;'>" . $original_width . "×" . $original_height . "</span>";
					"</a></td>";

					// Status of the retina for this image
				echo "<td id='wr2x-info-$post->ID' class='wr2x-info'>";
				echo wpr2x_html_get_basic_retina_info( $post, $info );
				echo "</td>";

				// Retina for Base Image
				echo "<td><em>Coming soon.</em></td>";

				// Actions
				echo "<td><a style='position: relative; top: 0px;' onclick='wr2x_generate(" . $post->ID . ", true)' id='wr2x_generate_button_" . $post->ID . "' class='button button-primary'>" . __( "GENERATE", 'wp-retina-2x' ) . "</a>";
				if ( !wr2x_is_ignore( $post->ID ) ) {
					echo "<a style='position: relative; top: 0px;' href='?page=wp-retina-2x&view=" . $view . "&paged=" . $paged . "&ignore=" . $post->ID . "' id='wr2x_generate_button_" . $post->ID . "' class='button button-primary'>" . __( "IGNORE", 'wp-retina-2x' ) . "</a>";
				}			
				echo "</td>";
				echo "</tr>";
			}
			?>
		</tbody>
	</table>
	</div>

	<?php
	jordy_meow_footer();
}
?>