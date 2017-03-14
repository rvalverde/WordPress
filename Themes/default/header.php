<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
	<meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
	<meta content="IE=Edge" http-equiv="X-UA-Compatible">
	<title><?php wp_title('&laquo;', true, 'right'); ?> <?php bloginfo('name'); ?></title>

	<link rel="stylesheet" href="https://opensource.keycdn.com/fontawesome/4.7.0/font-awesome.min.css" crossorigin="anonymous">
	<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

	<!-- @start header -->
		<header id="header">
			<div class="width">
				<div class="header">
					<div class="logo">
						<a href="<?php echo get_option('home'); ?>/">
							<img alt="<?php echo get_option('blogname'); ?>" src="<?php bloginfo('stylesheet_directory'); ?>/images/logo.png" />
						</a>
					</div>
					<?php if(!is_home()) : ?>
						<div class="search">
							<span class="icon">
								<i class="fa fa-chevron-left" aria-hidden="true"></i>
							</span>
							<form role="search" method="get" id="searchform" action="<?php echo home_url( '/' ); ?>">
								<input type="text" value="<?php the_search_query() ?>" name="s" id="s" />
								<button type="submit" id="searchsubmit"><i class="fa fa-search"></i></button>
							</form>
						</div>
					<?php endif; ?>
					<div class="nav">
						<?php global $current_user;  if ( is_user_logged_in() ) : ?>
							<ul>
								<li>
									<a href="<?php echo get_option('home') . "/author/" . $current_user->user_nicename; ?>">
										<i class="fa fa-user-circle" aria-hidden="true"></i> 
										<?php echo $current_user->display_name ?>
									</a>
								</li>
								<li>
									<a href="<?php echo get_option('home'); ?>/publish/">
										<i class="fa fa-edit" aria-hidden="true"></i> 
										<?php _e('Publish','mytheme'); ?>
									</a>
								</li>
								<li>
									<a href="<?php echo wp_logout_url(home_url()); ?>">
										<i class="fa fa-sign-out" aria-hidden="true"></i> 
										<span><?php _e('Log Out','mytheme'); ?></span>
									</a>
								</li>
							</ul>
						<?php else: ?>
							<ul>
								<li class="login"><a href="<?php echo get_option('home'); ?>/login/"><?php _e('Log In','mytheme'); ?></a></li>
								<li class="register"><a href="<?php echo get_option('home'); ?>/signup/">¡<?php _e('Sign Up','mytheme'); ?>!</a></li>
							</ul>
						<?php endif; ?>
					</div>
					<div class="responsive">
						<ul>
							<li class="search"><i class="fa fa-search" aria-hidden="true"></i></li>
							<li class="bars"><i class="fa fa-bars" aria-hidden="true"></i></li>
						</ul>
					</div>
				</div>
			</div>
		</header><!-- @end header -->