<?php
    /*$pro = new Pronostico_Anual();
    $periodos = $pro->quarter_duration( 21, 5, 2020 );
    echo $periodos['first'] . '<br>';
    echo $periodos['second'] . '<br>';
    echo $periodos['third'];*/

?>


<?php get_header(); ?>

	<main role="main">
	<!-- section -->
	<section>

	<?php if (have_posts()): while (have_posts()) : the_post(); ?>

		<!-- article -->
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

			<!-- post thumbnail -->
			<?php if ( has_post_thumbnail()) : // Check if Thumbnail exists ?>
				<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>">
					<?php the_post_thumbnail(); // Fullsize image for the single post ?>
				</a>
			<?php endif; ?>
			<!-- /post thumbnail -->

			<!-- post title -->
			<?php if ( get_the_content() == '' ) : ?>
			<h1>
				<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a>
			</h1>
			<?php endif; ?>
			<!-- /post title -->

			<?php the_content(); // Dynamic Content ?>

			<?php edit_post_link(); // Always handy to have Edit Post Links available ?>

			<div class="pro-footer">
				<a href="" class="btn btn--big btn--save-pro" title="Guardar como contentido definitivo">Generar Pronostico</a>
				<?php if ( get_the_content() != '' ) { ?>
				<a href="" class="btn btn--big btn--reset-pro" title="Volver a generar pronostico">Resetear Pronostico</a>
				<a href="<?php echo get_the_permalink() .'?pdf='. get_the_ID(); ?>" class="btn btn--big" title="generar documento PDF">PDF</a>
				<?php } ?>
			</div>				

		</article>
		<!-- /article -->

	<?php endwhile; ?>

	<?php else: ?>

		<!-- article -->
		<article>

			<h1><?php _e( 'Sorry, nothing to display.', 'html5blank' ); ?></h1>

		</article>
		<!-- /article -->

	<?php endif; ?>

	</section>
	<!-- /section -->
	</main>

<?php get_sidebar(); ?>

<div class="editor-box">
	<div class="editor-box__body">
		<div class="editor-box__nav">
			<span class="btn editor-box--close">Cerrar</span>
		</div>
		<input type="text" name="inter-title" placeholder="Título" >
		<?php
		$yourtext = '';
		$id = "interpretationArea";
		$name = 'interpretationArea';
		$textarea = esc_textarea( stripslashes( $yourtext ) );
		$settings = array('tinymce' => true, 'textarea_name' => "interpretationArea");
		wp_editor($textarea, $id, $settings);
		echo $textarea;
		?>
		<a href="#" class="btn save-content" >Guardar</a>
	</div>
</div>

<?php get_footer(); ?>
