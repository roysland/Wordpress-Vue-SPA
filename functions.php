<?php

function rt_rest_theme_scripts() {

	wp_enqueue_style( 'style', get_stylesheet_uri() );

	$base_url  = esc_url_raw( home_url() );
	$base_path = rtrim( parse_url( $base_url, PHP_URL_PATH ), '/' );

	if ( defined( 'RT_VUE_DEV' ) && RT_VUE_DEV ) {

		wp_enqueue_script( 'rest-theme-vue', 'http://localhost:8080/dist/build.js', array( 'jquery' ), '1.0.0', true );

	} else {

		wp_enqueue_script( 'rest-theme-vue', get_template_directory_uri() . '/dist/build.js', array( 'jquery' ), '1.0.0', true );

	}

	wp_localize_script( 'rest-theme-vue', 'rtwp', array(
		'root'      => esc_url_raw( rest_url() ),
		'base_url'  => $base_url,
		'base_path' => $base_path ? $base_path . '/' : '/',
		'nonce'     => wp_create_nonce( 'wp_rest' ),
		'site_name' => get_bloginfo( 'name' ),
	) );
}

add_action( 'wp_enqueue_scripts', 'rt_rest_theme_scripts' );

if ( function_exists( 'register_nav_menus' ) ) {

	register_nav_menus(
		array(
		'primary-menu' => __( 'Primary Menu' ),
		'secondary-menu' => __( 'Secondary Menu' ),
		)
	);

}

add_filter( 'excerpt_more', '__return_false' );

add_action( 'after_setup_theme', 'rt_theme_setup' );

function rt_theme_setup() {

	add_theme_support( 'post-thumbnails' );

}

function rt_custom_rewrite_rule() {

	global $wp_rewrite;

	$wp_rewrite->front               = $wp_rewrite->root;

	$wp_rewrite->set_permalink_structure( 'blog/%postname%/' );

	$wp_rewrite->page_structure      = $wp_rewrite->root . 'page/%pagename%/';

	$wp_rewrite->author_base         = 'author';
	$wp_rewrite->author_structure    = '/' . $wp_rewrite->author_base . '/%author%';

	$wp_rewrite->set_category_base( 'category' );
	$wp_rewrite->set_tag_base( 'tag' );

	$wp_rewrite->add_rule( '^blog$', 'index.php', 'top' );

}
add_action( 'init', 'rt_custom_rewrite_rule' );

//Forcing permalink structure
add_action( 'permalink_structure_changed', 'rt_forcee_perma_struct', 10, 2 );

function rt_forcee_perma_struct( $old, $new ) {

	update_option( 'permalink_structure', 'blog/%postname%' );

}
// Polyfill for wp_title()
add_filter( 'wp_title','rt_vue_title', 10, 3 );

function rt_vue_title( $title, $sep, $seplocation ) {

	if ( false !== strpos( $title, __( 'Page not found' ) ) ) {

		$replacement = ucwords( str_replace( '/', ' ', $_SERVER['REQUEST_URI'] ) );
		$title       = str_replace( __( 'Page not found' ), $replacement, $title );

	}

	return $title;
}

// Extend rest response
add_action( 'rest_api_init', 'rt_extend_rest_post_response' );

function rt_extend_rest_post_response() {

	// Add featured image
	register_rest_field( 'post',
		'featured_image_src', //NAME OF THE NEW FIELD TO BE ADDED - you can call this anything
		array(
			'get_callback'    => 'get_image_src',
			'update_callback' => null,
			'schema'          => null,
			 )
	);

	register_rest_field( 'post',
		'cat_name', //NAME OF THE NEW FIELD TO BE ADDED - you can call this anything
		array(
			'get_callback'    => 'rt_get_cat_name',
			'update_callback' => null,
			'schema'          => null,
			 )
	);

	register_rest_field( 'post',
		'tag_name',
		array(
			'get_callback'    => 'rt_get_tag_name',
			'update_callback' => null,
			'schema'          => null,
			)
	);

	/* PageUP clinic things */
	register_rest_field('mjm-clinic-service', 'featured_image', array(
		'get_callback' => 'get_image_src',
		'update_callback' => null,
		'schema' => null
	));

	register_rest_field('mjm-clinic-service', 'service_category', array(
		'get_callback' => 'get_service_category',
		'update_callback' => null,
		'schema' => null
	));

	register_rest_route('pageup/', 'get-by-slug', array(
		'methods' => 'GET',
		'callback' => 'get_content_by_slug',
		'args' => array(
			'slug' => array(
				'required' => false
			)
		)
	));
}

function get_content_by_slug (WP_REST_REQUEST $request) {
	$slug = $request['slug'];
	/* $return = get_page_by_path($slug, ARRAY_A);
	$response = new WP_REST_Response($return); */
	$query = new WP_Query(array(
		'post_type' => 'mjm-clinic-service',
		'name' => $slug
	));
	//$response = new WP_REST_Response($query);
	
	return $query->post;
}

function get_service_category ($object, $field_name, $request) {
	$categories = array();
	foreach ($object['mjm_clinic_service_category'] as $term_id) {
		$term = get_term($term_id);
		$categories[] = array(
			'term_id' => $term->term_id,
			'name' => $term->name,
			'slug' => $term->slug,
			'description' => $term->description
		);
	}

	return $categories;
}

// Get featured image
function get_image_src( $object, $field_name, $request ) {

	$feat_img_array['full'] = wp_get_attachment_image_src( $object['featured_media'], 'full', false );
	$feat_img_array['thumbnail'] = wp_get_attachment_image_src( $object['featured_media'], 'thumbnail', false );
	$feat_img_array['srcset'] = wp_get_attachment_image_srcset( $object['featured_media'] );
	$image = is_array( $feat_img_array ) ? $feat_img_array : 'false';
	return $image;

}

function rt_get_cat_name( $object, $field_name, $request ) {

	$cats = $object['categories'];
	$res = [];
	$ob = [];
	foreach ( $cats as $x ) {
		$cat_id = (int) $x;
		$cat = get_category( $cat_id );
		if ( is_wp_error( $cat ) ) {
			$res[] = '';
		} else {
			$ob['name'] = isset( $cat->name ) ? $cat->name : '';
			$ob['id']   = isset( $cat->term_id ) ? $cat->term_id : '';
			$ob['slug'] = isset( $cat->slug ) ? $cat->slug : '';
			$res[] = $ob;
		}
	}
	return $res;

}

function rt_get_tag_name( $object, $field_name, $request ) {

	$tags = $object['tags'];
	$res = [];
	$ob = [];

	foreach ( $tags as $x ) {
		$tag_id = (int) $x;
		$tag = get_tag( $tag_id );
		if ( is_wp_error( $tag ) ) {
			$res[] = '';
		} else {
			$ob['name'] = isset( $tag->name ) ? $tag->name : '';
			$ob['id'] = isset( $tag->term_id ) ? $tag->term_id : '';
			$ob['slug'] = isset( $tag->slug ) ? $tag->slug : '';
			$res[] = $ob;
		}
	}
	return $res;
}
show_admin_bar( false );
/* function wpsd_add_book_args() {
	global $wp_post_types;

	$wp_post_types['mjm-clinic-service']->show_in_rest = true;
	$wp_post_types['mjm-clinic-service']->rest_base = 'services';
	$wp_post_types['mjm-clinic-service']->rest_controller_class = 'WP_REST_Posts_Controller';
}
add_action( 'init', 'wpsd_add_book_args', 30 ); */