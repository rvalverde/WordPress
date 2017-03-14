<?php get_header(); ?>

	<div id="content">
		<?php if (have_posts()) : ?>
			<div class="block">
				<?php if (is_category()) : ?>
					<h1><?php echo single_cat_title('', false); ?></h1>
				<?php elseif (is_tag()) : ?>
					<h1>Significado de Soñar con <?php echo single_tag_title('', false); ?></h1>
				<?php elseif (is_day()) : ?>
					<h1>Día <?php echo get_the_time('F jS, Y'); ?></h1>
				<?php elseif (is_month()) : ?>
					<h1>Mes <?php echo get_the_time('F, Y'); ?></h1>
				<?php elseif (is_year()) : ?>
					<h1>Año <?php echo get_the_time('Y'); ?></h1>
				<?php elseif (is_author()) : ?>
					<h1>Autor <?php echo get_the_author(); ?></h1>
				<?php elseif (isset($_GET['paged']) && !empty($_GET['paged'])) : ?>
					<h1><?php _e('Blog Archives'); ?></h1>
				<?php elseif (is_tax()) : ?>
					<h1><?php echo get_queried_object()->name; ?></h1>
				<?php else : ?>
					<h1><?php echo post_type_archive_title(); ?></h1>
				<?php endif; ?>
				<?php get_template_part(loop); ?>
				<?php if (function_exists('pagenavi')) { pagenavi(); } ?>
			</div>
		<?php else : ?>
		  <?php get_search_form(); ?>
		<?php endif; ?>
	</div>

<?php get_sidebar(); ?>
<?php get_footer(); ?>