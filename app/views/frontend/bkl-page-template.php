<?php

namespace eqhby\bkl;

get_header();
?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main">

		<?php
		while ( have_posts() ) :
			the_post();

			?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<header class="entry-header">
					<h1 class="entry-title"><?php the_title(); ?></h1>
				</header><!-- .entry-header -->

				<div class="entry-content">
					<?php Helper::get_controller('Frontend')->init(); ?>
				</div><!-- .entry-content -->
			</article>
			<?php

		endwhile; // End of the loop.
		?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_sidebar();
get_footer();
