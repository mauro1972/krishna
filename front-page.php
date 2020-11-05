<?php
    
    $my_query = ramya_front_page_listing();
    //wp_reset_postdata();
	
    get_header(); 
?>

	<main role="main">
		<!-- section -->
		<section>

		<h1><?php the_title(); ?></h1>
        <?php if ( $my_query->have_posts() ) : while ( $my_query->have_posts() ) : $my_query->the_post(); ?>

            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

        <?php endwhile; ?>

		<?php else: ?>

			<!-- article -->
			<article>

				<h2><?php _e( 'Sorry, nothing to display.', 'html5blank' ); ?></h2>

			</article>
			<!-- /article -->

		<?php endif; ?>

		</section>
		<!-- /section -->
	</main>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
