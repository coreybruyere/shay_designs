<?php
/**
 * WordPress Bootstrap Pagination
 */

function wp_bootstrap_pagination( $args = array() ) {
    
    $defaults = array(
        'range'           => 4,
        'custom_query'    => FALSE,
        'previous_string' => __( '<div class="pagination__prev"></div>', 'Roots' ),
        'next_string'     => __( '<div class="pagination__next"></div>', 'Roots' ),
        'before_output'   => '<nav role="menu" aria-label="pagination"><ul class="pagination">',
        'after_output'    => '</ul></nav>'
    );
    
    $args = wp_parse_args( 
        $args, 
        apply_filters( 'wp_bootstrap_pagination_defaults', $defaults )
    );
    
    $args['range'] = (int) $args['range'] - 1;
    if ( !$args['custom_query'] )
        $args['custom_query'] = @$GLOBALS['wp_query'];
    $count = (int) $args['custom_query']->max_num_pages;
    $page  = intval( get_query_var( 'paged' ) );
    $ceil  = ceil( $args['range'] / 2 );
    
    if ( $count <= 1 )
        return FALSE;
    
    if ( !$page )
        $page = 1;
    
    if ( $count > $args['range'] ) {
        if ( $page <= $args['range'] ) {
            $min = 1;
            $max = $args['range'] + 1;
        } elseif ( $page >= ($count - $ceil) ) {
            $min = $count - $args['range'];
            $max = $count;
        } elseif ( $page >= $args['range'] && $page < ($count - $ceil) ) {
            $min = $page - $ceil;
            $max = $page + $ceil;
        }
    } else {
        $min = 1;
        $max = $count;
    }
    
    $echo = '';
    $previous = intval($page) - 1;
    $previous = esc_attr( get_pagenum_link($previous) );
    
    $firstpage = esc_attr( get_pagenum_link(1) );
    if ( $firstpage && (1 != $page) )
        $echo .= '<li class="pagination__cap -first"><a href="' . $firstpage . '">' . __( 'First', 'Roots' ) . '</a></li>';

    if ( $previous && (1 != $page) )
        $echo .= '<li class="pagination__item"><a href="' . $previous . '"class="pagination__icon" title="' . __( 'Previous', 'Roots') . '"><svg viewBox="0 0 32 32" aria-hidden="true"><g filter=""><use xlink:href="#arrow-left"></use></g></svg></a></li>';
    
    if ( !empty($min) && !empty($max) ) {
        for( $i = $min; $i <= $max; $i++ ) {
            if ($page == $i) {
                $echo .= '<li class="pagination__item -active"><span class="active">' . str_pad( (int)$i, 2, '0', STR_PAD_LEFT ) . '</span></li>';
            } else {
                $echo .= sprintf( '<li class="pagination__item"><a href="%s">%002d</a></li>', esc_attr( get_pagenum_link($i) ), $i );
            }
        }
    }
    
    $next = intval($page) + 1;
    $next = esc_attr( get_pagenum_link($next) );
    if ($next && ($count != $page) )
        $echo .= '<li class="pagination__item"><a href="' . $next . '"class="pagination__icon" title="' . __( 'Next', 'Roots') . '"><svg viewBox="0 0 32 32" aria-hidden="true"><g filter=""><use xlink:href="#arrow-right"></use></g></svg></a></li>';
    
    $lastpage = esc_attr( get_pagenum_link($count) );
    if ( $lastpage ) {
        $echo .= '<li class="pagination__cap -last"><a href="' . $lastpage . '">' . __( 'Last', 'Roots' ) . '</a></li>';
    }

    if ( isset($echo) )
        echo $args['before_output'] . $echo . $args['after_output'];
}


function customize_wp_bootstrap_pagination($args) {

    $args['previous_string'] = 'previous';
    $args['next_string'] = 'next';

    return $args;
}
add_filter('wp_bootstrap_pagination_defaults', 'customize_wp_bootstrap_pagination');