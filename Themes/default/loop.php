		
		<div id="list">
			<?php while (have_posts()) : the_post(); ?>
				<div class="list <?php if( get_post_type() == 'post' ) {echo "post";} ?>">
					<div class="content">
						<?php if( get_post_type() == 'questions' ) : ?>
							<h2>
								<i class="fa fa-question-circle" aria-hidden="true"></i> 
								<a href="<?php the_permalink() ?>" rel="bookmark">
									<?php the_title(); ?>
								</a>
							</h2>
						<?php else : ?>
							<h2>
								<i class="fa fa-eye" aria-hidden="true"></i> 
								<a href="<?php the_permalink() ?>" rel="bookmark">
									<?php the_title(); ?>
								</a>
							</h2>
						<?php endif; ?>
						<div class="excerpt">
							<?php echo mb_substr(get_the_excerpt(), 0 ,300, "UTF-8"); ?>
							<span class="read"><a href="<?php the_permalink() ?>"><?php _e('Read more &raquo;','mytheme'); ?></a></span>
						</div>
					</div>
				</div>
			<?php endwhile; ?>
		</div>
		