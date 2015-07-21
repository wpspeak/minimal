<?php
//* Start the engine
include_once( get_template_directory() . '/lib/init.php' );

//* Setup Theme
include_once( get_stylesheet_directory() . '/lib/theme-defaults.php' );

//* Child theme (do not remove)
define( 'CHILD_THEME_NAME', 'Minimal Theme' );
define( 'CHILD_THEME_URL', 'http://wpspeak.com/themes/minimal-theme/' );
define( 'CHILD_THEME_VERSION', '1.0.0' );

//* Enqueue Custom Scripts
add_action( 'wp_enqueue_scripts', 'minimal_custom_scripts' );
function minimal_custom_scripts() {
	wp_enqueue_style( 'minimal-custom-fonts', '//fonts.googleapis.com/css?family=Open+Sans|Leckerli+One|Source+Sans+Pro:300,400', array(), CHILD_THEME_VERSION );
	wp_enqueue_style( 'dashicons' );
	wp_enqueue_script( 'minimal-responsive-menu', get_stylesheet_directory_uri() . '/js/responsive-menu.js', array( 'jquery' ), '1.0.0', true );

}

//* Enqueue masonry script on home and archive pages
add_action( 'wp_enqueue_scripts', 'minimal_masonry_custom_layout' );
function minimal_masonry_custom_layout() {
	if ( is_home() || is_archive() ) {
		wp_enqueue_script( 'masonry' );
		wp_enqueue_script( 'masonry-init', get_bloginfo( 'stylesheet_directory' ) . '/js/masonry-init.js', '', '', true );
	}
}

//* Add new featured image sizes
add_image_size( 'masonry-img', 293, 0, TRUE );

//* Create custom color schemes
add_theme_support( 'genesis-style-selector', array(
	'minimal-blue'	=> __( 'Blue', 'minimal' ),
	'minimal-turquoise'	=> __( 'Turquoise', 'minimal' ),
	'minimal-orange'	=> __( 'Orange', 'minimal' ),
) );

//* Add HTML5 markup structure
add_theme_support( 'html5' );


//* Add support for structural wraps
add_theme_support( 'genesis-structural-wraps', array(
	'header',
	'nav',
	'site-inner',
	'footer-widgets',
	'footer'
) );

//* Add viewport meta tag for mobile browsers
add_theme_support( 'genesis-responsive-viewport' );

//* Add support for 3-column footer widgets
add_theme_support( 'genesis-footer-widgets', 3 );

//* Unregister layout settings
genesis_unregister_layout( 'content-sidebar-sidebar' );
genesis_unregister_layout( 'sidebar-sidebar-content' );
genesis_unregister_layout( 'sidebar-content-sidebar' );

//* Remove the site description
remove_action( 'genesis_site_description', 'genesis_seo_site_description' );

//* Unregister sidebars
unregister_sidebar( 'sidebar-alt' );

//* Remove comment form allowed tags
add_filter( 'comment_form_defaults', 'minimal_remove_comment_form_allowed_tags' );
function minimal_remove_comment_form_allowed_tags( $defaults ) {
	
	$defaults['comment_notes_after'] = '';
	return $defaults;

}

//* Unregister secondary navigation menu
add_theme_support( 'genesis-menus', array( 'primary' => __( 'Primary Navigation Menu', 'genesis' ) ) );

//* Remove output of primary navigation right extras
remove_filter( 'genesis_nav_items', 'genesis_nav_right', 10, 2 );
remove_filter( 'wp_nav_menu_items', 'genesis_nav_right', 10, 2 );

//* Customize the entry meta in the entry header (requires HTML5 theme support)
add_filter( 'genesis_post_info', 'minimal_post_info_filter' );
function minimal_post_info_filter($post_info) {
	$post_info = '[post_date] [post_author_posts_link before=""] [post_comments before=""] [post_edit]';
	return $post_info;
}

//* Customize the entry meta in the entry footer 
add_filter( 'genesis_post_meta', 'minimal_post_meta_filter' );
function minimal_post_meta_filter($post_meta) {
	$post_meta = '[post_categories before=""] [post_tags before=""]';
	return $post_meta;
}

//* Remove [...] from WordPress excerpts
function minimal_customize_excerpt_more( $more ) {
    return ' ...';
}
add_filter('excerpt_more', 'minimal_customize_excerpt_more');

//* Add custom body class to the head
add_filter( 'body_class', 'minimal_body_class' );
function minimal_body_class( $classes ) {
	if ( is_home() || is_archive() ) {	
		$classes[] = 'masonry-page';
	}
	return $classes;
	
}

//* Restructure masonry page
add_action( 'genesis_meta','minimal_archive_pages' );
function minimal_archive_pages() {
	if ( is_home() || is_archive() ) {
	
		//* Force full-width-content layout setting
		add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_full_width_content' );

		//* Add featured image 
		remove_action( 'genesis_entry_content', 'genesis_do_post_image', 8 );
		add_action( 'genesis_entry_header', 'minimal_themes_archive_image', 3 );
		function minimal_themes_archive_image() {
		 
			if ( $image = genesis_get_image( 'format=url&size=masonry-img' ) ) {
				printf( '<a href="%s" class="image-hover" rel="bookmark"><img class="post-photo aligncenter" src="%s" alt="%s" /></a>', get_permalink(), $image, the_title_attribute( 'echo=0' ) );
			}
		 
		}

		//* Force excerpts
		add_filter( 'genesis_pre_get_option_content_archive', 'minimal_show_excerpts' );
		function minimal_show_excerpts() {
			return 'excerpts';
		}

		//* Modify the length of post excerpts
		add_filter( 'excerpt_length', 'minimal_excerpt_length' );
		function minimal_excerpt_length( $length ) {
			return 25; // pull first 50 words
		}

		//* Customize the entry meta in the entry footer (requires HTML5 theme support)
		add_filter( 'genesis_post_meta', 'minimal_masonry_meta' );
		function minimal_masonry_meta($post_meta) {
			$post_meta = '[post_categories before=""]';
			return $post_meta;
		}

		//* Reposition Archive Pagination
		remove_action( 'genesis_after_endwhile', 'genesis_posts_nav' );
		add_action( 'genesis_after_content', 'genesis_posts_nav' );

		//* Reposition the breadcrumb
		remove_action( 'genesis_before_loop', 'genesis_do_breadcrumbs' );
		add_action( 'genesis_before_content', 'genesis_do_breadcrumbs' );

		//* Remove entry header meta
		remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
		remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_open', 5 );
		remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_close', 15 );
		
		//* Reposition taxonomy description
		remove_action( 'genesis_before_loop', 'genesis_do_taxonomy_title_description', 15 );
		add_action( 'genesis_before_content', 'genesis_do_taxonomy_title_description', 15 );
		
		//* Reposition Archive Pagination
		remove_action( 'genesis_after_endwhile', 'genesis_posts_nav' );
		add_action( 'genesis_after_content', 'genesis_posts_nav' );
		
		//* Reposition Author archive page description
		remove_action( 'genesis_before_loop', 'genesis_do_author_box_archive', 15 );
		add_action( 'genesis_before_content', 'genesis_do_author_box_archive', 15 );
		
		//* Reposition Author archive page title
		remove_action( 'genesis_before_loop', 'genesis_do_author_title_description', 15 );
		add_action( 'genesis_before_content', 'genesis_do_author_title_description', 15 );
		
		//* Reposition CPT Title Archive page
		remove_action( 'genesis_before_loop', 'genesis_do_cpt_archive_title_description' );
		add_action( 'genesis_before_content', 'genesis_do_cpt_archive_title_description' );
			
	}
}

//* Change the footer text
add_filter('genesis_footer_creds_text', 'minimal_footer_creds_filter');
function minimal_footer_creds_filter( $creds ) {
	$creds = '[footer_copyright] &middot; ' . get_bloginfo('name') . ' &middot; Proudly powered by [footer_wordpress_link] and [footer_childtheme_link before=""]';
	return $creds;
}

//* Hook before post widget after the entry content
add_action( 'genesis_after_header', 'minimal_top_widget', 15 );
function minimal_top_widget() {

	genesis_widget_area( 'top-widget', array(
		'before' => '<div class="top-widget widget-area"><div class="wrap">',
		'after'  => '</div></div>',
	));
}

//* Hook after post widget after the entry content
add_action( 'genesis_after_entry', 'minimal_after_entry', 5 );
function minimal_after_entry() {

	if ( is_singular( 'post' ) )
		genesis_widget_area( 'after-entry-widget', array(
			'before' => '<div class="after-entry-widget widget-area">',
			'after'  => '</div>',
		) );

}

//* Register widget areas
genesis_register_sidebar( array(
	'id'          => 'top-widget',
	'name'        => __( 'Top Widget', 'minimal' ),
	'description' => __( 'This is the widget that appears at top of the page.', 'minimal' ),
) );

genesis_register_sidebar( array(
	'id'          => 'after-entry-widget',
	'name'        => __( 'After Entry Widget', 'minimal' ),
	'description' => __( 'This is the widget that appears after the entry on single posts.', 'minimal' ),
) );
