<?php
/**
 * Template Name: Homepage EDD Shop
 *
 * This template displays a product grid, a featured product
 * section, testimonials and a call-to-action banner.
 * Settings for this template can be found in Appearance ->
 * Customize -> Homepage Settings while viewing this page template.
 *
 * @package Checkout
 * @since Checkout 1.0
 */
get_header(); ?>

		<div id="main" class="site-main homepage-template">
			<div id="primary" class="content-area">
				<div id="content" class="site-content" role="main">

					<?php do_action( 'checkout_homepage_above_content' ); ?>

					<!-- If there is post content, show it -->
					<?php
					if( have_posts() ) : while( have_posts() ) : the_post();
						if( get_the_content() ) {
							echo '<div class="homepage-post-content">';
								get_template_part( 'partials/content-standard' );
							echo '</div>';
						}
					endwhile; endif;
					?>


					<?php do_action( 'checkout_homepage_above_download' ); ?>


					<?php
					// Get number of downloads from customizer
					$download_count = get_option( 'checkout_homepage_download_count', '6' );
					if ( '0' != $download_count ) {

						$args = array(
							'post_type'      => 'download',
							'posts_per_page' => $download_count,
						);
						$project_query = new WP_Query ( $args );
						if ( $project_query -> have_posts() ) :
						?>

							<div itemscope class="portfolio-wrapper download-wrapper">

								<?php while ( $project_query -> have_posts() ) : $project_query -> the_post();

									get_template_part( 'partials/content-download-thumbs' );

								endwhile; ?>

							</div><!-- .portfolio-wrapper -->

						<?php
						endif;
						wp_reset_query();
					}

					do_action( 'checkout_homepage_above_split' ); ?>


					<!-- Featured EDD products -->
					<?php
						$featured_tag = get_theme_mod( 'checkout_edd_tag_select', '' );
						if ( '' != $featured_tag ) {

							$featured_tag = get_theme_mod( 'checkout_edd_tag_select' );

							$featured_args = array(
								'post_type'      => 'download',
								'posts_per_page' => 6,
								'no_found_rows'  => true,
								'tax_query'      => array(
									array(
										'taxonomy' => 'download_tag',
										'field'    => 'slug',
										'terms'    => $featured_tag
									),
								),
							);
							$featured_query = new WP_Query ( $featured_args );
							if ( $featured_query -> have_posts() ) :
						?>
							<section class="split-section">
								<div class="slide-navs"></div>
								<ul class="rslides product-slides">
									<?php while ( $featured_query -> have_posts() ) : $featured_query -> the_post();
										get_template_part( 'partials/content-featured-product' );
									endwhile; ?>
								</ul>

								<ul id="product-pager">
									<?php while ( $featured_query -> have_posts() ) : $featured_query -> the_post(); ?>

											<li><a class="paging-thumb" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>" rel="bookmark">
												<?php if ( has_post_thumbnail() ) {
													the_post_thumbnail( 'paging-thumb' );
												} else { ?>
													<img src="<?php echo get_template_directory_uri(); ?>/images/default-thumb.jpg" alt="placeholder" />
												<?php } ?>
											</a></li>

									<?php endwhile; ?>
								</ul>
							</section><!-- .split-section -->

						<?php endif; ?>
						<?php wp_reset_query(); ?>
					<?php } // If featured_tag exists ?>


					<!-- Get the testimonial section -->
					<?php get_template_part( 'partials/content-testimonial-section' ); ?>
				</div><!-- #content .site-content -->
			</div><!-- #primary .content-area -->
		</div><!-- #main .site-main -->

		<!-- Get the call-to-action section -->
		<?php get_template_part( 'partials/content-cta-section' ); ?>

<?php get_footer(); ?>