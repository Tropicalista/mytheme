<?php

/**
 * Redirect sedi inpdap category to page
 */
add_action( 'pre_get_posts', 'wpse_disable_sedi' );
function wpse_disable_sedi( $query )
{
	if ( is_category( 'sedi' ) ){
		wp_redirect( home_url() . '/sedi-inpdap/', 301 );
		exit;
	}

}

/**
 * Remove 'hentry' from post_class()
 */
function ja_remove_hentry( $class ) {
	$class = array_diff( $class, array( 'hentry' ) );	
	return $class;
}
add_filter( 'post_class', 'ja_remove_hentry' );