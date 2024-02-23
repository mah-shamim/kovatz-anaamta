<?php
/**
 * The template for displaying featured posts on the front page
 *
 * @package WEN_Travel
 */
$number          = get_theme_mod( 'wen_travel_featured_content_number', 3 );
$post_list       = array();
$no_of_post      = 0;

$args = array(
	'post_type'           => 'post',
	'ignore_sticky_posts' => 1, // ignore sticky posts.
);

// Get valid number of posts.
$args['post_type'] = 'page';

for ( $i = 1; $i <= $number; $i++ ) {
	$wen_travel_post_id = '';

	$wen_travel_post_id = get_theme_mod( 'wen_travel_featured_content_page_' . $i );

	if ( $wen_travel_post_id && '' !== $wen_travel_post_id ) {
		$post_list = array_merge( $post_list, array( $wen_travel_post_id ) );

		$no_of_post++;
	}
}

$args['post__in'] = $post_list;
$args['orderby']  = 'post__in';

if ( ! $no_of_post ) {
	return;
}

$args['posts_per_page'] = $no_of_post;

$loop = new WP_Query( $args );

while ( $loop->have_posts() ) :
	
	$loop->the_post();

	?>
	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<div class="hentry-inner">
			<div class="post-thumbnail">
				<a class="cover-link" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
					<?php if( has_post_thumbnail() ) : ?>
						<img src="<?php the_post_thumbnail_url( 'wen-travel-featured-content' ); ?>">
					<?php else: ?>
						<img src="<?php echo esc_url( trailingslashit( esc_url( get_template_directory_uri() ) ) . 'images/no-thumb-508x338.jpg' ); ?>">
					<?php endif; ?>
				</a>
				<header class="entry-header">					
					<?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">','</a></h2>' );  

					if ( 'post' === get_post_type() || 'featured-content' === get_post_type() ) : ?>
					<div class="entry-meta">
						<?php wen_travel_by_line(); ?>

						<?php wen_travel_cat_list(); ?>
					</div><!-- .entry-meta -->
					<?php endif; ?>
				</header>
			</div>

			<div class="entry-container">
				<?php the_excerpt(); ?>
			</div><!-- .entry-content -->
		</div><!-- .hentry-inner -->
	</article>
<?php
endwhile;

wp_reset_postdata();
