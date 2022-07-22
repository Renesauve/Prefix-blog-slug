<?php
/*
* Plugin Name: Prefix Blog Slug
* Plugin URI: https://github.com/Jambaree/link-to-repo-here
* Description: Adds a /blog/ prefix before default posts
* Version: 0.1
* Author: Rene Sauve
* Author URI: your GH profile url here
*/

function create_new_url_querystring() {
    add_rewrite_rule(
        'blog/([^/]*)$',
        'index.php?name=$matches[1]',
        'top'
    );

    add_rewrite_tag('%blog%','([^/]*)');
}
add_action('init', 'create_new_url_querystring', 999 );


/**
 * Modify post link
 * This will print /blog/post-name instead of /post-name
 */
function append_query_string( $url, $post, $leavename ) {

	if ( $post->post_type != 'post' )
        	return $url;
	
	
	if ( false !== strpos( $url, '%postname%' ) ) {
        	$slug = '%postname%';
	}
	elseif ( $post->post_name ) {
        	$slug = $post->post_name;
	}
	else {
		$slug = sanitize_title( $post->post_title );
	}
    
	$url = home_url( user_trailingslashit( 'blog/'. $slug ) );

	return $url;
}
add_filter( 'post_link', 'append_query_string', 10, 3 );


/**
 * Redirect all posts to new url
 * If you get error 'Too many redirects' or 'Redirect loop', then delete everything below
 */
function redirect_old_urls() {

	if ( is_singular('post') ) {
		global $post;

		if ( strpos( $_SERVER['REQUEST_URI'], '/blog/') === false) {
		   wp_redirect( home_url( user_trailingslashit( "blog/$post->post_name" ) ), 301 );
		   exit();
		}
	}
}
add_filter( 'template_redirect', 'redirect_old_urls' );