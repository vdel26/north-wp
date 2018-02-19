<?php 


function SRW_childtheme_styles() {
	// Parent theme
    $parent_style = 'semplice-stylesheet'; // Parent theme ID
    $theme_uri = get_stylesheet_directory_uri();
    $theme_ver = wp_get_theme()->get('Version');
    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );

    // slick
    wp_register_style('slick', $theme_uri.'/js/slick.css', array(), '1.8.0');
	wp_register_script('slick', $theme_uri.'/js/slick.min.js', array('jquery'), '1.8.0', true);

    // Child theme
    wp_enqueue_style( 'north-semplice',
        $theme_uri . '/style.css',
        array( $parent_style, 'slick', 'semplice-frontend-stylesheet' ),
        $theme_ver
    );


    wp_enqueue_script('north-js', $theme_uri.'/js/north.js', 
		// dependencias de theme.js
		array( 'jquery', 'slick', 'semplice-frontend-js' ),
		$theme_ver, 
		true
	);
}
add_action( 'wp_enqueue_scripts', 'SRW_childtheme_styles' );



// -----------------------------------------
// POST TYPES
// Project CPT redefines to add rest api support
// -----------------------------------------
function SRW_custom_post_types() {
	$labels = array(
		'name'               => _x( 'Random menu Links', 'post type general name', 'row_themes' ),
		'singular_name'      => _x( 'Random menu Link', 'post type singular name', 'row_themes' )
	);

	$args = array(
		'labels'             => $labels,
		'public'             => true,
		'publicly_queryable' => true,
		'exclude_from_search' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'show_in_nav_menus'  => false,
		'capability_type'    => 'post',
		'has_archive'        => false,
		'hierarchical'       => false,
		'menu_position'      => 50,
		'show_in_rest'       => true,
		'rest_base'          => 'random-link',
		'rest_controller_class' => 'WP_REST_Posts_Controller',
		'supports'           => array( 'title', 'editor')
	);

	register_post_type( 'random-link', $args );
}
add_action( 'init', 'SRW_custom_post_types' );



/*
 * SHORTCODES
 */
// menu shortcode.
function SRW_menu_shortcode($atts) {
	extract(shortcode_atts(array(
		"name" => ''
	), $atts));

	$menu = wp_nav_menu(
		array(
			'menu' => $name,
			'echo' => false,
			'container' => false,
			'fallback_cb' => false,
			'menu_class' => 'nav'
		)
	);

	if(!empty($menu))
		return $menu;
}
add_shortcode("wpmenu", "SRW_menu_shortcode");

// home loader (depends on session storage)
session_start(); 
function SRW_loader_shortcode($atts) {
	if(!isset($_SESSION['northloaded'])){
		$_SESSION['northloaded'] = 1;
		return '<section id="home-loader" class="content-block home-loader" data-valign="center"><img class="is-content" src="http://localhost/north/north/wp-content/uploads/2017/12/north-logo-big.svg" alt="north-logo-big" caption="" data-width="original" data-scaling="no"></section>';
	} else {
		return 'cargado!!';
	}
}
add_shortcode("home-loader", "SRW_loader_shortcode");


// project navigation (prev/next) links
function SRW_projects_nav_shortcode($atts) {
	global $post;

	$nav  = '<div class="projects-nav">';
	$next = get_previous_post_link( '<span class="next-proj-link">%link</span>', '<span class="icom-arrow-right"></span>');
	$prev = get_next_post_link( '<span class="prev-proj-link">%link</span>', '<span class="icom-arrow-left"></span>');

	// if no button get first or last post
	if(!$next || !$prev){
		$projects = get_posts(array(
			'post_type' => 'project',
			'posts_per_page' => -1
		));

		if(!$next){
			$postID = $projects[0]->ID;
			$next = '<span class="next-proj-link"><a href="'. get_permalink($postID) .'"><span class="icom-arrow-right"></span></a></span>';
		}
		if(!$prev){
			$postID = $projects[count($projects)-1]->ID;
			$prev = '<span class="prev-proj-link"><a href="'. get_permalink($postID) .'"><span class="icom-arrow-left"></span></a></span>';
		}
	}

	$nav .= $next . $prev .'</div>';

	return $nav;
}
add_shortcode("projects-nav", "SRW_projects_nav_shortcode");


// PAGE TITLE IN FORMS
function SRW_hiddenfield_title() {
	global $post; ?>
	<script>
		jQuery(document).ready(function($){
			if( $('input#page-title').length > 0 ){
				$('input#page-title').val('<?php echo $post->post_title ?>');
			}
		})
	</script>
	<?php
}
add_action("wp_footer", "SRW_hiddenfield_title",100);



?>