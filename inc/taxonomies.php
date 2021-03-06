<?php

/**
 * Check if the Series taxonomy is enabled
 *
 * @since 0.4
 * @return bool Whether or not the Series taxonomy option is enabled in the Theme Options > Advanced
 */
function largo_is_series_enabled() {
	$series_enabled = of_get_option('series_enabled');
	return !empty($series_enabled);
}

/**
 * Register the prominence and series custom taxonomies
 * Insert the default terms
 *
 * @uses  largo_is_series_enabled
 * @since 1.0
 */
function largo_custom_taxonomies() {
	if (!taxonomy_exists('prominence')) {
		register_taxonomy(
			'prominence',
			'post',
			array(
				'hierarchical'  => true,
				'labels'        => array(
					'name'              => _x( 'Post Prominence', 'taxonomy general name' ),
					'singular_name'     => _x( 'Post Prominence', 'taxonomy singular name' ),
					'search_items'      => __( 'Search Post Prominences' ),
					'all_items'         => __( 'All Post Prominences' ),
					'parent_item'       => __( 'Parent Post Prominence' ),
					'parent_item_colon' => __( 'Parent Post Prominence:' ),
					'edit_item'         => __( 'Edit Post Prominence' ),
					'view_item'         => __( 'View Post Prominence' ),
					'update_item'       => __( 'Update Post Prominence' ),
					'add_new_item'      => __( 'Add New Post Prominence' ),
					'new_item_name'     => __( 'New Post Prominence Name' ),
					'menu_name'         => __( 'Post Prominence' ),
				),
				'query_var'     => true,
				'rewrite'       => true,
			)
		);
	}

	$termsDefinitions = array(
		array(
			'name' => __('Sidebar Featured Widget', 'largo'),
			'description' => __('If you are using the Featured Posts widget in a sidebar, add this label to posts to determine which to display in the widget.', 'largo'),
			'slug' => 'sidebar-featured'
		),
		array(
			'name' => __('Footer Featured Widget', 'largo'),
			'description' => __('If you are using the Featured Posts widget in the footer, add this label to posts to determine which to display in the widget.', 'largo'),
			'slug' => 'footer-featured'
		),
		array(
			'name' => __('Featured in Category', 'largo'),
			'description' => __('This will allow you to designate a story to appear more prominently on category archive pages.', 'largo'),
			'slug' => 'category-featured'
		),
		array(
			'name' => __('Homepage Featured', 'largo'),
			'description' => __('Add this label to posts to display them in the featured area on the homepage.', 'largo'),
			'slug' => 'homepage-featured'
		)
	);

	if (largo_is_series_enabled()) {
		$termsDefinitions[] = array(
			'name' => __('Featured in Series', 'largo'),
			'description' => __('Select this option to allow this post to float to the top of any/all series landing pages sorting by Featured first.', 'largo'),
			'slug' => 'series-featured'
		);
	}

	$largoProminenceTerms = apply_filters('largo_prominence_terms', $termsDefinitions);

	$changed = false;
	$terms = get_terms('prominence', array(
		'hide_empty' => false,
		'fields' => 'all'
	));
	$names = array_map(function($arg) { return $arg->name; }, $terms);

	$term_ids = array();
	foreach ($largoProminenceTerms as $term ) {
		if (!in_array($term['name'], $names)) {
			wp_insert_term(
				$term['name'], 'prominence',
				array(
					'description' => $term['description'],
					'slug' => $term['slug']
				)
			);
			$changed = true;
		}
	}

	if ($changed)
		delete_option('prominence_children');

	do_action('largo_after_create_prominence_taxonomy', $largoProminenceTerms);

	if ( ! taxonomy_exists( 'series' ) ) {
		register_taxonomy(
			'series',
			'post',
			array(
				'hierarchical' => true,
				'labels' => array(
					'name' => _x( 'Series', 'taxonomy general name' ),
					'singular_name' => _x( 'Series', 'taxonomy singular name' ),
					'search_items' => __( 'Search Series' ),
					'all_items' => __( 'All Series' ),
					'parent_item' => __( 'Parent Series' ),
					'parent_item_colon' => __( 'Parent Series:' ),
					'edit_item' => __( 'Edit Series' ),
					'view_item' => __( 'View Series' ),
					'update_item' => __( 'Update Series' ),
					'add_new_item' => __( 'Add New Series' ),
					'new_item_name' => __( 'New Series Name' ),
					'menu_name' => __( 'Series' ),
				),
				'query_var' => true,
				'rewrite' => true,
			)
		);
	}
}
add_action( 'init', 'largo_custom_taxonomies' );


/**
 * Determines whether a post is in a series
 * Expects to be called from within The Loop.
 *
 * @uses global $post
 * @uses largo_is_series_enabled
 * @return bool
 * @since 1.0
 */
function largo_post_in_series( $post_id = NULL ) {
	if ( !largo_is_series_enabled() ) return false;
	global $post;
	$the_id = ($post_id) ? $post_id : $post->ID ;
	$features = get_the_terms( $the_id, 'series' );
	return ( $features ) ? true : false;
}

/**
 * Outputs custom taxonomy terms attached to a post
 *
 * @return array of terms
 * @since 1.0
 */
function largo_custom_taxonomy_terms( $post_id ) {
	$taxonomies = apply_filters( 'largo_custom_taxonomies', array( 'series' ) );
	$post_terms = array();
	foreach ( $taxonomies as $tax ) {
		if ( taxonomy_exists( $tax ) ) {
			$terms = get_the_terms( $post_id, $tax );
			if ( $terms )
				$post_terms = array_merge( $post_terms, $terms );
		}
	}
	return $post_terms;
}

/**
 * Output format for the series custom taxonomy at the bottom of single posts
 *
 * @param $term array the term we want to output
 * @since 1.0
 */
if ( ! function_exists( 'largo_term_to_label' ) ) {
	function largo_term_to_label( $term ) {
	    return sprintf( '<div class="series-label"><h5><a href="%1$s">%2$s</a><a class="rss-link" href="%3$s"></a></h5><p>%4$s</p></div>',
	    	get_term_link( $term, $term->taxonomy ),
	    	esc_attr( $term->name ),
	    	get_term_feed_link( $term->term_id, $term->taxonomy ),
	    	esc_attr( strip_tags ( $term->description )
	    ));
	}
}

/**
 * Helper function for getting posts in proper landing-page order for a series
 *
 * @uses largo_is_series_enabled
 * @param integer series term id
 * @param integer number of posts to fetch, defaults to all
 */
function largo_get_series_posts( $series_id, $number = -1 ) {

	// If series are not enabled, then there are no posts in a series.
	if ( !largo_is_series_enabled() ) return;

	// get the cf-tax-landing
	$args = array(
		'post_type' => 'cftl-tax-landing',
		'posts_per_page' => 1,
		'tax_query' => array( array(
			'taxonomy' => 'series',
			'field' => 'id',
			'terms' => $series_id
		)),
	);
	$landing = new WP_Query( $args );

	$series_args = array(
		'post_type' 		=> 'post',
		'tax_query' => array(
			array(
				'taxonomy' => 'series',
				'field' => 'id',
				'terms' => $series_id
			)
		),
		'order' 			=> 'DESC',
		'orderby' 		=> 'date',
		'posts_per_page' 	=> $number
	);

	if ( $landing->found_posts ) {
		$landing->next_post();
		$order = get_post_meta( $landing->post->ID, 'post_order', TRUE );
		switch ( $order ) {
			case 'ASC':
				$series_args['order'] = 'ASC';
				break;
			case 'custom':
				$series_args['orderby'] = 'series_custom';
				break;
			case 'featured, DESC':
			case 'featured, ASC':
				$series_args['orderby'] = $order;
				break;
		}
	}

	$series_posts = new WP_Query( $series_args );

	if ( $series_posts->found_posts ) return $series_posts;

	return false;

}

/**
 * Helper for getting posts in a category archive, excluding featured posts.
 */
function largo_category_archive_posts( $query ) {
	//don't muck with admin, non-categories, etc
	if ( !$query->is_category() || !$query->is_main_query() || is_admin() ) return;

	// get the featured posts
	$featured_posts = largo_get_featured_posts_in_category($query->get('category_name'));

	// get the IDs from the featured posts
	$featured_post_ids = array();
	foreach ( $featured_posts as $fpost )
		$featured_post_ids[] = $fpost->ID;

	$query->set('post__not_in', $featured_post_ids);
}
add_action('pre_get_posts', 'largo_category_archive_posts', 15);

/**
 * Get posts marked as "Featured in category" for a given category name.
 *
 * @param string $category_name the category to retrieve featured posts for.
 * @since 0.5
 */
function largo_get_featured_posts_in_category($category_name) {
	// get the featured posts
	$featured_posts = get_posts( array(
		'category_name' => $category_name,
		'numberposts' => 5,
		'post_status' => 'publish',
		'tax_query' => array(
			array(
				'taxonomy' => 'prominence',
				'field' => 'slug',
				'terms' => 'category-featured',
			)
		)
	));
	return $featured_posts;
}

/**
 * If the option in Advanced Options is unchecked, unregister the "Series" taxonomy
 *
 * @uses largo_is_series_enabled
 * @since 0.4
 */
function unregister_series_taxonomy() {
	if ( !largo_is_series_enabled() ) {
		register_taxonomy( 'series', array(), array('show_in_nav_menus' => false) );
	}
}
add_action( 'init', 'unregister_series_taxonomy', 999 );

/**
 * If the option in Advanced Options is unchecked, unregister the "Post Types" taxonomy
 *
 * @uses of_get_option
 * @since 0.4
 */
function unregister_post_types_taxonomy() {
	if ( of_get_option('post_types_enabled') == 0 ) {
		register_taxonomy( 'post-type', array(), array('show_in_nav_menus' => false) );
	}
}
add_action( 'init', 'unregister_post_types_taxonomy', 999 );
