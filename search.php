<?php
/**
 * The template for displaying Search Results pages.
 */
get_header();
?>

<div id="content" class="stories search-results span8" role="main">
	<?php if ( have_posts() ) {
		get_search_form(); ?>

		<h3 class="recent-posts clearfix">
			<?php
				printf( __('Your search for <span class="search-term">%s</span> returned ', 'largo'), get_search_query() );
				printf( _n( '%s result', '%s results', $wp_query->found_posts ), number_format_i18n( $wp_query->found_posts ) );
				printf( '<a class="rss-link" href="%1$s"><i class="icon-rss"></i></a>', get_search_feed_link() );
			?>
		</h3>

		<?php
			while ( have_posts() ) : the_post();
				if ( get_post_type( $post ) == 'argolinks' ) {
					get_template_part( 'partials/content', 'argolinks' );
				} else {
					get_template_part( 'partials/content', 'search' );
				}
			endwhile;
    		largo_content_nav( 'nav-below' );
    	} else {
			get_template_part( 'partials/content', 'not-found' );
		}
    ?>
</div><!--#content-->

<?php get_sidebar(); ?>
<?php get_footer();
