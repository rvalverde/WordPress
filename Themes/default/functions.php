<?php

	function replace_content($text) {
		$alt = get_the_author_meta( 'display_name' );
		$text = str_replace('alt=\'\'', 'alt=\'Avatar de '.$alt.'\' title=\'Gravatar de '.$alt.'\'',$text);
		return $text;
	}
	add_filter('get_avatar','replace_content');

	add_filter( 'the_posts', function( $posts, $q ) {
	    if( $q->is_main_query() && $q->is_search() ) 
	    {
	        usort( $posts, function( $a, $b ){
	            /**
	             * Sort by post type. If the post type between two posts are the same
	             * sort by post date. Make sure you change your post types according to 
	             * your specific post types. This is my post types on my test site
	             */
	            $post_types = [
	                'post' 			=> 1,
	                'questions'		=> 2
	            ];              
	            if ( $post_types[$a->post_type] != $post_types[$b->post_type] ) {
	                return $post_types[$a->post_type] - $post_types[$b->post_type];
	            } else {
	                return $a->post_date < $b->post_date; // Change to > if you need oldest posts first
	            }
	        });
	    }
	    return $posts;
	}, 10, 2 );

	add_filter('get_comment_author', 'wpse31694_comment_author_display_name');
	function wpse31694_comment_author_display_name($author) {
	    global $comment;
	    if (!empty($comment->user_id)){
	        $user=get_userdata($comment->user_id);
	        $author=$user->display_name;    
	    }

	    return $author;
	}

	function timeAgo($timestamp, $granularity=2, $format='Y-m-d H:i:s'){
		$difference = time() - $timestamp;
		if($difference < 0) return '0 seconds ago';
		elseif($difference < 864000){
				$periods = array('week' => 604800,'day' => 86400,'hr' => 3600,'min' => 60,'sec' => 1);
				$output = '';
				foreach($periods as $key => $value){
						if($difference >= $value){
								$time = round($difference / $value);
								$difference %= $value;
								$output .= ($output ? ' ' : '').$time.' ';
								$output .= (($time > 1 && $key == 'day') ? $key.'s' : $key);
								$granularity--;
						}
						if($granularity == 0) break;
				}
				return ($output ? $output : '0 seconds').' ago';
		}
		else return date($format, $timestamp);
	}

	function disable_wp_emojicons() {
		// all actions related to emojis
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );

		// filter to remove TinyMCE emojis
		add_filter( 'tiny_mce_plugins', 'disable_emojicons_tinymce' );
	}
	add_action( 'init', 'disable_wp_emojicons' );

	function disable_emojicons_tinymce( $plugins ) {
		if ( is_array( $plugins ) ) {
			return array_diff( $plugins, array( 'wpemoji' ) );
		} else {
			return array();
		}
	}
	
	add_filter( 'emoji_svg_url', '__return_false' );

	add_action( 'wp_print_styles', 'tj_deregister_yarpp_header_styles' );
	function tj_deregister_yarpp_header_styles() {
	   wp_dequeue_style('yarppWidgetCss');
	   // Next line is required if the related.css is loaded in header when disabled in footer.
	   wp_deregister_style('yarppRelatedCss'); 
	}

	add_action( 'wp_footer', 'tj_deregister_yarpp_footer_styles' );
	function tj_deregister_yarpp_footer_styles() {
	   wp_dequeue_style('yarppRelatedCss');
	}

	function custom_excerpt_length( $length ) {
        return 25;
    }
    add_filter( 'excerpt_length', 'custom_excerpt_length', 999 );

    function new_excerpt_more( $more ) {
	    return '...';
	}
	add_filter('excerpt_more', 'new_excerpt_more');

	add_action('after_setup_theme', 'my_theme_setup');
	function my_theme_setup(){
	    load_theme_textdomain('mytheme', get_template_directory() . '/languages');
	}

	add_action( 'admin_init', 'disable_autosave' );
	function disable_autosave() {
		wp_deregister_script( 'autosave' );
	}

	add_theme_support( 'post-thumbnails' );

	function redirect_register() {
		if (is_page("signup") && is_user_logged_in()) {
			wp_redirect( home_url() );
		}
	}
	add_action( 'template_redirect', 'redirect_register' );

	function redirect_login() {
		if (is_page("login") && is_user_logged_in()) {
			wp_redirect( home_url() );
		}
	}
	add_action( 'template_redirect', 'redirect_login' );

	function sdac_custom_profile_url( $url, $user_id, $scheme ) {
		$url = site_url( '/edit-profile/' );
		return $url;
	}
	add_filter( 'edit_profile_url', 'sdac_custom_profile_url', 10, 3 );

	function add_extra_user_column($columns) { //Add CPT Column for events and remove default posts column
		unset($columns['posts']);
		return array_merge( $columns, 
		array('foo' => __('Posts')) );
	}
	add_filter('manage_users_columns' , 'add_extra_user_column');

	function add_post_type_column( $value, $column_name, $id ) { //Print event_type value
		if( $column_name == 'foo' ) {
			global $wpdb;

			$count = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(ID) FROM $wpdb->posts WHERE post_type IN ('questions') AND post_status = 'publish' AND post_author = %d", $id ));

			if ( $count > 0 ) {
				$r = "<a href='edit.php?author=$id'>";
				$r .= $count;
				$r .= '</a>';
			} else {
				$r = 0;
			}

			return $r;
		}
	}
	add_filter( 'manage_users_custom_column', 'add_post_type_column', 10, 3 );

	// Change default WordPress email address
	add_filter('wp_mail_from', 'new_mail_from');
	add_filter('wp_mail_from_name', 'new_mail_from_name');
	 
	function new_mail_from($old) {
		return 'info@yousuenos.com';
	}

	function new_mail_from_name($old) {
		return 'YouSuenos';
	}

	function wpa_cpt_tags( $query ) {
		if ( $query->is_tag() && $query->is_main_query() ) {
			$query->set( 'post_type', array( 'post', 'questions' ) );
		}
	}
	add_action( 'pre_get_posts', 'wpa_cpt_tags' );

	if( function_exists('acf_add_options_page') ) {
		acf_add_options_page('SEO');
		//acf_add_options_page('Settings');
	}

	function my_add_frontend_scripts() {
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-core');
	}
	add_action('wp_enqueue_scripts', 'my_add_frontend_scripts');
	
	function new_content( $content ) {
		if( get_post_type() == 'questions' ) {
			$content = get_field( 'descripcion' );
		}
		return $content;
	}
	add_filter( 'the_content', 'new_content' );

	add_filter('the_title', 'new_title', 10, 2);
	function new_title($title) {
		if( get_post_type() == 'post' ) {
			$title = __( 'Dream with', 'mytheme' ) . ' ' . $title;
		} elseif ( get_post_type() == 'questions' ) {
			$title = __( 'What does dream about', 'mytheme' ) . ' ' . $title . '?';
		}
		return $title;
	}

	function tgm_io_cpt_search( $query ) {
		if ( $query->is_search ) {
			$query->set( 'post_type', array( 'post', 'questions' ) );
		}
		return $query;
	}
	add_filter( 'pre_get_posts', 'tgm_io_cpt_search' );

	function redirect_publish() {
		global $post;
		//if (!is_single()) return;
		if (is_page("publish") && !is_user_logged_in()) {
			auth_redirect();
		}
	}
	add_action( 'template_redirect', 'redirect_publish' );

	function my_acf_prepare_field( $field ) {
		$field['label'] = __( 'Title', 'mytheme' );
		return $field;
	}
	add_filter('acf/prepare_field/name=_post_title', 'my_acf_prepare_field');

	//change WordPress permalink
	function cws_nice_search_redirect() {
		global $wp_rewrite;
		if ( !isset( $wp_rewrite ) || !is_object( $wp_rewrite ) || !$wp_rewrite->using_permalinks() )
			return;

		$search_base = $wp_rewrite->search_base;
		if ( is_search() && !is_admin() && strpos( $_SERVER['REQUEST_URI'], "/{$search_base}/" ) === false ) {
			wp_redirect( home_url( "/{$search_base}/" . urlencode( get_query_var( 's' ) ) ) );
			exit();
		}
	}
	add_action( 'template_redirect', 'cws_nice_search_redirect' );

	function pagenavi($pages = '', $range = 2) {  
		$showitems = ($range * 2)+1;  
	 
		global $paged;
		if(empty($paged)) $paged = 1;
	 
		if($pages == '') {
			global $wp_query;
			$pages = $wp_query->max_num_pages;
			if(!$pages) {
				 $pages = 1;
			}
		}
	 
		if(1 != $pages) {
			echo "<div class='pagination'>";
			if($paged > 2 && $paged > $range+1 && $showitems < $pages) echo "<a href='".get_pagenum_link(1)."'>&laquo;</a>";
			if($paged > 1 && $showitems < $pages) echo "<a href='".get_pagenum_link($paged - 1)."'>&lsaquo;</a>";
	 
			for ($i=1; $i <= $pages; $i++) {
				if (1 != $pages &&( !($i >= $paged+$range+1 || $i <= $paged-$range-1) || $pages <= $showitems )) {
					 echo ($paged == $i)? "<span class='current'>".$i."</span>":"<a href='".get_pagenum_link($i)."' class='inactive' >".$i."</a>";
				}
			}
	 
			if ($paged < $pages && $showitems < $pages) echo "<a href='".get_pagenum_link($paged + 1)."'>&rsaquo;</a>";  
			if ($paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages) echo "<a href='".get_pagenum_link($pages)."'>&raquo;</a>";
			echo "</div>\n";
		}
	}
	add_action('init', 'alphaindex_alpha_tax');

	function alphaindex_alpha_tax() {
		register_taxonomy(
			'alphabet',
			'post',
			array(
				'label' => __( 'Dictionary' ),
				'show_ui' => true,
				'query_var' => true,
				'show_admin_column' => true,
				'rewrite' => array( 'slug' => 'alphabet' ),
				'hierarchical' => true,
			)
		);
	}

	add_action( 'pre_get_posts', 'mysite_custom_archives' );
	function mysite_custom_archives ( $query ) {
		if( is_archive() && !is_post_type_archive('questions') ) {
			$query->set( 'order', 'ASC' );
			$query->set( 'orderby', 'title' );
		}
		return $query;
	}

	function author_custom_archives ( $query ) {
		if ( $query->is_author() && $query->is_main_query() ) {
			$query->set( 'post_type', array( 'questions' ) );
		}
		return $query;
	}
	add_action( 'pre_get_posts', 'author_custom_archives' );

	function preview_publish_posts($query) { 
		//if( is_user_logged_in() && is_author(get_current_user_id()) ) {
		if( is_user_logged_in() && is_author() ) {
			$query->set('post_status', array('publish', 'pending'));
		}
		return $query;
	}

	add_filter('pre_get_posts', 'preview_publish_posts');

	function attachment_redirect() {
		global $post;
		if ( is_attachment() ) {
			if( $post->post_parent )
				wp_redirect( get_permalink($post->post_parent), 301 );
			else
				wp_redirect( home_url(), 301 );
			exit;
		}
	}
	add_action( 'template_redirect', 'attachment_redirect', 1 );
	
	add_filter('admin_footer_text', 'remove_footer_admin');
	add_filter('show_admin_bar', '__return_false');
	
	function remove_footer_admin () {
		echo "Diseño y desarrollo by <a href='http://www.raulvalverde.com/' target='_blank'>Raúl Valverde</a>";
	}
	
	remove_action('wp_head', 'wp_generator');
	
	function new_dashboard_widget_function() {
		echo "Bienvenid@<br /><br />";
		echo "";
	} 

	function new_add_dashboard_widgets() {
		wp_add_dashboard_widget('example_dashboard_widget', 'Escritorio', 'new_dashboard_widget_function');
	}
	
	add_action('wp_dashboard_setup', 'new_add_dashboard_widgets' );
	
	function my_custom_login_url() {
		return get_option('home');
	}
	add_action( 'login_headerurl', 'my_custom_login_url' );
	
	function my_custom_login_logo() {
		echo '<style type="text/css">
				h1 a {
					background-image:url('.get_bloginfo('template_directory').'/images/logo-login.png) !important;
					height : 89px;
					margin-left : -30px;
					width : 360px;
				}
			</style>';
	}
	
	add_action('login_head', 'my_custom_login_logo');
	
	function my_custom_logo() {
		echo '<style type="text/css">
				
				#menu-dashboard .wp-submenu li:nth-child(3n), 
				
				#jetpack_summary_widget, 
				#activity-widget, 
				#toplevel_page_jetpack,
				#header-logo {
					display : none;
				}
				
				#wphead h1 {
					padding-top : 11px;
				}
				
				#wphead-info {padding-top : 12px;}
				
				#site-heading a {
					text-indent : -99999em;
					width : 202px;
					height : 50px;
					display : block;
					background-image: url('.get_bloginfo('template_directory').'/images/logo-loged.png) !important;
				}
				
				#wphead {
					height : 70px;
				}
				
			</style>';
	}
	
	add_action('admin_head', 'my_custom_logo');
	
	function my_wp_admin_css() {
		echo '<style type="text/css">#header-logo { display : none;}</style>';
	}
	add_action('wp_admin_css','my_wp_admin_css');
	
	/* function replace_excerpt($content) {
		$cadena='... <a href="'. get_permalink() .'" class="more-link" rel="nofollow">Leer más »</a>';
		return str_replace(' [...]', $cadena, $content);
	}
	add_filter('the_excerpt', 'replace_excerpt'); */
	
	function create_my_post_types() {
		
		// Fire this during init
		register_post_type('questions', array(
			'labels' 				=> array(
				'name' 				=> __( 'Question' ),
				'singular_name' 	=> __( 'Questions' ),
				'add_new_item' 		=> __( 'Add Question' ),
				'edit_item' 		=> __( 'Edit Question' ),
				'new_item' 			=> __( 'New Question' ),
				'view' 				=> __( 'See Question' ),
				'view_item' 		=> __( 'See Question' ),
				'search_items' 		=> __( 'Search Question' )
			),
			'public' 				=> true,
			'publicly_queryable' 	=> true,
			'show_ui' 				=> true, 
			'show_in_menu' 			=> true, 
			'query_var' 			=> true,
			'exclude_from_search' 	=> false,
			'rewrite' 				=> array( 
				'slug' => 'questions',
				'with_front' => false
			),
			'yarpp_support' 		=> true,
			'taxonomies' 			=> array(
				'post_tag'
			),
			'capability_type' 		=> 'post',
			'hierarchical' 			=> false,
			'has_archive' 			=> true,
			'supports' 				=> array(
				'title',
				'comments'
			),
			'capability_type' 		=> 'questions',
			'capabilities' 			=> array(
				'create_posts' 				=> 'create_questions',
				'delete_others_posts' 		=> 'delete_others_questions',
				'delete_posts' 				=> 'delete_questions',
				'delete_private_posts' 		=> 'delete_private_questions',
				'delete_published_posts'	=> 'delete_published_questions',
				'edit_others_posts' 		=> 'edit_others_questions',
				'edit_posts' 				=> 'edit_questions',
				'edit_private_posts' 		=> 'edit_private_questions',
				'edit_published_posts' 		=> 'edit_published_questions',
				'publish_posts'		 		=> 'publish_questions',
				'manage_categories'			=> 'manage_questions',
				'read_private_posts' 		=> 'read_private_questions'
			),
		));
		
		flush_rewrite_rules();
	}
	add_action( 'init', 'create_my_post_types' );
	
	//add_theme_support( 'post-thumbnails' );
	
	/* 	add_action( 'admin_head', 'ds_hide_stuff'  );
	function ds_hide_stuff() {
		global $post_type;
			remove_action( 'media_buttons', 'media_buttons' );
			remove_meta_box('slugdiv', $post_type, 'normal');

			$ds_hide_postdiv = "<style type=\"text/css\"> #postdiv, #postdivrich { display: none; } #postexcerpt {display:block;}</style>";
			print($ds_hide_postdiv);
	} */
	
	function new_excerpt_length($length) {
		return 50;
	}
	add_filter('excerpt_length', 'new_excerpt_length');
	
	function quitar_widgets_dashboard() {
		global $wp_meta_boxes;
		unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_right_now']);
		unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_recent_comments']);
		unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_incoming_links']);
		unset($wp_meta_boxes['dashboard']['normal']['core']['dashboard_plugins']);
		unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_primary']);
		unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_secondary']);
		unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press']);
		unset($wp_meta_boxes['dashboard']['side']['core']['dashboard_recent_drafts']);
	}
	add_action('wp_dashboard_setup', 'quitar_widgets_dashboard');
	
	function thumbs() {
		$files = get_children('post_parent='.get_the_ID().'&post_type=attachment&post_mime_type=image');
		if($files) :
			$keys = array_reverse(array_keys($files));
			$j=0;
			$num = $keys[$j];
			$image=wp_get_attachment_image($num, 'thumbnail', false);
			$imagepieces = explode('"', $image);
			$imagepath = $imagepieces[1];
			$thumb=wp_get_attachment_thumb_url($num);
			print $thumb;
		endif;
	}

	function limit_title( $size, $echo=true ){
		$title = get_the_title();
		if ( strlen( $title ) <= $size ) {
			$echo_out = '';
		} else {
			$echo_out = '...';
		}
			$title = mb_substr( $title, 0, $size, 'UTF-8' );
		if( $echo ) {
			echo $title . $echo_out;
		} else {
		return ( $title . $echo_out );
		}
	}
	
	function excerpt($limit) {
		$excerpt = explode(' ', get_the_excerpt(), $limit);
			if (count($excerpt)>=$limit) {
				array_pop($excerpt);
				$excerpt = implode(" ",$excerpt).'...';
			} else {
				$excerpt = implode(" ",$excerpt);
			}
		$excerpt = preg_replace('`\[[^\]]*\]`','',$excerpt);
		return $excerpt;
	}
 
	function content($limit) {
		$content = explode(' ', get_the_content(), $limit);
			if (count($content)>=$limit) {
				array_pop($content);
				$content = implode(" ",$content).'...';
			} else {
				$content = implode(" ",$content);
			}
		$content = preg_replace('/\[.+\]/','', $content);
		$content = apply_filters('the_content', $content); 
		$content = str_replace(']]>', ']]&gt;', $content);
		return $content;
	}
	
	function get_first_image_thumb() {
		$Html = get_the_content();
		$extrae = '/<img .*src=["\']([^ ^"^\']*)["\']/';
		preg_match_all( $extrae , $Html , $matches );
		$image = $matches[1][0];
		if($image) { echo $image; } else { echo get_bloginfo ( 'template_directory' ) . '/images/not.png'; }
	}
	
	/* function wpr_maintenace_mode() {
		if ( !current_user_can( 'edit_themes' ) || !is_user_logged_in() ) {
			die('Estamos de pruebas, no tardes en volver que enseguida terminamos.');
		}
	}
	add_action('get_header', 'wpr_maintenace_mode'); */
	
	// function redirect_to_post(){
	// 	global $wp_query;
	// 	if( is_archive() && $wp_query->post_count == 1 ){
	// 		the_post();
	// 		$post_url = get_permalink();
	// 		wp_redirect( $post_url );
	// 	}
	// }
	// add_action('template_redirect', 'redirect_to_post');
	
	function rel_next_prev_pagination() {
		global $wp_query;
		$big = 999999999; // need an unlikely integer
		$paginate_links = paginate_links(array(
			'base' => str_replace($big, '%#%', get_pagenum_link($big)),
			'format' => '?paged=%#%',
			'current' => max(1, get_query_var('paged')),
			'total' => $wp_query->max_num_pages
		));

		$array_pagination = explode("</a>", $paginate_links);
		foreach ($array_pagination as $link) {
			if (strrpos($link, __('Next &raquo;'))) {
				preg_match('(http://"?.*")', $link, $matches, PREG_OFFSET_CAPTURE, 3);
				$next = explode('"', $matches[0][0]);
				echo "<link rel=\"next\" href=\"" . $next[0] . "\">\n";
			}
			if (strrpos($link, __('&laquo; Previous'))) {
				preg_match('(http://"?.*")', $link, $matches, PREG_OFFSET_CAPTURE, 3);
				$previous = explode('"', $matches[0][0]);
				echo "<link rel=\"prev\" href=\"" . $previous[0] . "\">\n";
			}
		}
	}
	add_action('wp_head', 'rel_next_prev_pagination');
	
?>