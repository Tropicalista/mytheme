<?php

/**
 * Remove 'hentry' from post_class()
 */
function ja_remove_hentry( $class ) {
	$class = array_diff( $class, array( 'hentry' ) );	
	return $class;
}
add_filter( 'post_class', 'ja_remove_hentry' );

/**
 * Add the TinyMCE VisualBlocks Plugin.
 *
 * @param array $plugins An array of all plugins.
 * @return array
 */
function my_custom_plugins( $plugins ) {
     $plugins['toc'] = get_stylesheet_directory_uri() . '/js/toc/plugin.min.js';
     return $plugins;
}
add_filter( 'mce_external_plugins', 'my_custom_plugins' );

function my_mce_buttons_2( $buttons ) {	
	/**
	 * Add in a core button that's disabled by default
	 */
	$buttons[] = 'toc';

	return $buttons;
}
add_filter( 'mce_buttons_2', 'my_mce_buttons_2' );

/*
* Enqueue cookie consent files
*/
function add_cookie_consent(){
	wp_enqueue_style( 'cookie-consent-css', get_stylesheet_directory_uri() . '/css/cookies.css' );
	wp_enqueue_script( 'cookie-consent-js', 'https://cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.0.6/cookieconsent.min.js', null, null, true );
    wp_enqueue_script( 'cookie-js', get_stylesheet_directory_uri() . '/js/cookie.js', array(), '', true ); 
}
add_action( 'wp_enqueue_scripts', 'add_cookie_consent' );
