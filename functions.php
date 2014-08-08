<?php
/*
 * @package WordPress
 * @subpackage Base_Theme
 */
// =========================================================
// REQUIRE
// =========================================================
require_once 'includes/helper.php';
require_once 'includes/walkers.php';
// =========================================================
// USE
// =========================================================
use Factory\PostType;
use Factory\MetaBox;
use Factory\Taxonomy;
use Factory\Controls\ControlsCollection;
use Factory\Controls\Text;
use Factory\Controls\Textarea;
use Factory\Controls\Select;
use Factory\Controls\Checkbox;
use Factory\Controls\Table;

// =========================================================
// CONSTANTS
// =========================================================
define('TDU', get_bloginfo('template_url'));
// =========================================================
// HOOKS
// =========================================================
add_filter( 'use_default_gallery_style', '__return_false' );
add_filter('nav_menu_css_class', 'cheangeMenuClasses');
add_filter('default_content', 'themeDefaultContent');
add_filter('the_content', 'templateUrl');
add_filter('get_the_content', 'templateUrl');
add_filter('widget_text', 'templateUrl');
add_action('wp_enqueue_scripts', 'scriptsMethod');
add_filter('wp_setup_nav_menu_item', 'gcAddCustomNavFields');		
add_action('wp_update_nav_menu_item', 'gcUpdateCustomNavFields', 10, 3);
add_filter('wp_edit_nav_menu_walker', 'gcEditWalker', 10, 2);
add_filter('show_admin_bar', '__return_false');
// =========================================================
// THEME SUPPORT
// =========================================================
add_theme_support( 'post-thumbnails' );
add_theme_support( 'automatic-feed-links' );
add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list' ) );
// =========================================================
// IMAGES SETTINGS
// =========================================================
add_image_size( 'single-post-thumbnail', 400, 9999, false );
set_post_thumbnail_size( 604, 270, true );
// =========================================================
// REGISTER SIDEBARS AND MENUS
// =========================================================
register_sidebar(array(
	'id'            => 'right-sidebar',
	'name'          => 'Right Sidebar',
	'before_widget' => '<div class="widget %2$s" id="%1$s">',
	'after_widget'  => '</div>',
	'before_title'  => '<h3>',
	'after_title'   => '</h3>'));

register_sidebar(array(
	'id'            => 'footer-sidebar',
	'name'          => 'Footer Sidebar',
	'before_widget' => '<div class="grid_3 %2$s" id="%1$s">',
	'after_widget'  => '</div>',
	'before_title'  => '<h4>',
	'after_title'   => '</h4>'));

register_nav_menus( 
	array(
		TimeKeeper::MENU_LOCATION_PRIMARY       => __('Primary Navigation', 'theme'),
		TimeKeeper::MENU_LOCATION_PRIMARY_RIGHT => __('Primary Navigation (RIGHT)', 'theme')		
	)
);

// =========================================================
// CUSTOM POST TYPEs
// =========================================================
$GLOBALS['gc_session']['pt'] = new PostType('session', array('icon_code' => 'f017'));

// =========================================================
// CUSTOM META BOX'S
// =========================================================
$additional_options_ctrls = new ControlsCollection(array(
	new Text('Start time'),
	new Text('Stop time'),
	new Text('Spend time')	
	));

$GLOBALS['gc_session']['mb'] = new MetaBox('session', 'Additional options', $additional_options_ctrls);
// =========================================================
// CUSTOM TAXONOMIES
// =========================================================
$workers_table_controls = new ControlsCollection(array(
	new Select('User', array('options' => getUsersOptions())),
	new Text('Cost')
));

$join_table_controls = new ControlsCollection(array(
	new Select('User ID', array('options' => getUsersOptions())),
	new Text('Cost')
));

$taxonomy_controls = new ControlsCollection(array(	
	new Table('Workers', array('columns' => $workers_table_controls)),
	new Table('Joins', array('columns' => $join_table_controls)),
	new Text('Owner'),
	new Text('Create date')
));

$GLOBALS['gc_session']['t'] = new Taxonomy('session', 'Project', array(), $taxonomy_controls);

// =========================================================
// TimeKeeper
// =========================================================
$GLOBALS['timekeeper'] = new TimeKeeper();

/**
 * Theme helper 
 */
function themePagingNav() 
{
	global $wp_query;

	// Don't print empty markup if there's only one page.
	if ( $wp_query->max_num_pages < 2 )
		return;
	?>
	<nav class="navigation paging-navigation" role="navigation">
		<div class="nav-links cf">

			<?php if ( get_next_posts_link() ) : ?>
			<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'theme' ) ); ?></div>
			<?php endif; ?>

			<?php if ( get_previous_posts_link() ) : ?>
			<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'theme' ) ); ?></div>
			<?php endif; ?>

		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}

/**
 * Theme helper
 */
function themePostNav() 
{
	global $post;

	// Don't print empty markup if there's nowhere to navigate.
	$previous = ( is_attachment() ) ? get_post( $post->post_parent ) : get_adjacent_post( false, '', true );
	$next     = get_adjacent_post( false, '', false );

	if ( ! $next && ! $previous )
		return;
	?>
	<nav class="navigation post-navigation" role="navigation">
		<h1 class="screen-reader-text"><?php _e( 'Post navigation', 'theme' ); ?></h1>
		<div class="nav-links">

			<?php previous_post_link( '%link', _x( '<span class="meta-nav">&larr;</span> %title', 'Previous post link', 'theme' ) ); ?>
			<?php next_post_link( '%link', _x( '%title <span class="meta-nav">&rarr;</span>', 'Next post link', 'theme' ) ); ?>

		</div><!-- .nav-links -->
	</nav><!-- .navigation -->
	<?php
}

/**
 * Theme helper
 * @param  boolean $echo
 * @return string
 */
function themeEntryDate( $echo = true ) 
{
	if ( has_post_format( array( 'chat', 'status' ) ) )
		$format_prefix = _x( '%1$s on %2$s', '1: post format name. 2: date', 'theme' );
	else
		$format_prefix = '%2$s';

	$date = sprintf( '<span class="date"><a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s">%4$s</time></a></span>',
		esc_url( get_permalink() ),
		esc_attr( sprintf( __( 'Permalink to %s', 'theme' ), the_title_attribute( 'echo=0' ) ) ),
		esc_attr( get_the_date( 'c' ) ),
		esc_html( sprintf( $format_prefix, get_post_format_string( get_post_format() ), get_the_date() ) )
	);

	if ( $echo )
		echo $date;

	return $date;
}

/**
 * Theme helper
 */
function themeEntryMeta() 
{
	if ( is_sticky() && is_home() && ! is_paged() )
		echo '<span class="featured-post">' . __( 'Sticky', 'theme' ) . '</span>';

	if ( ! has_post_format( 'link' ) && 'post' == get_post_type() )
		themeEntryDate();

	// Translators: used between list items, there is a space after the comma.
	$categories_list = get_the_category_list( __( ', ', 'theme' ) );
	if ( $categories_list ) {
		echo '<span class="categories-links">' . $categories_list . '</span>';
	}

	// Translators: used between list items, there is a space after the comma.
	$tag_list = get_the_tag_list( '', __( ', ', 'theme' ) );
	if ( $tag_list ) {
		echo '<span class="tags-links">' . $tag_list . '</span>';
	}

	// Post author
	if ( 'post' == get_post_type() ) {
		printf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>',
			esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
			esc_attr( sprintf( __( 'View all posts by %s', 'theme' ), get_the_author() ) ),
			get_the_author()
		);
	}
}

/**
 * Change menu classes
 * @param  string $css_classes 
 * @return string              
 */
function cheangeMenuClasses($css_classes)
{
	$css_classes = str_replace("current-menu-item", "current-menu-item active", $css_classes);
	$css_classes = str_replace("current-menu-parent", "current-menu-parent active", $css_classes);
	return $css_classes;
}

/**
 * Template url - short code for widget's and content
 * @param  string $text 
 * @return 
 */
function templateUrl($text) 
{
	return str_replace('[template-url]',get_bloginfo('template_url'), $text);
}

/**
 * All scripts register in this funciton
 */
function scriptsMethod() 
{
	// =========================================================
	// STYLES
	// =========================================================
	wp_enqueue_style('main', get_bloginfo('stylesheet_url'));
	wp_enqueue_style('open-sans', 'http://fonts.googleapis.com/css?family=Open+Sans:300italic,400italic,600italic,700italic,800italic,400,300,600,700,800&subset=latin,cyrillic-ext,cyrillic,latin-ext');
	wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css');
	wp_enqueue_style('boxer', TDU.'/css/jquery.fs.boxer.css');
	// =========================================================
	// SCRIPTS
	// =========================================================
	wp_enqueue_script('jquery');
	wp_enqueue_script('main', TDU.'/js/main.js', array('jquery'));
	wp_enqueue_script('moment', TDU.'/js/moment.js');
	wp_enqueue_script('boxer', TDU.'/js/jquery.fs.boxer.js');	

	wp_localize_script('main', 'defaults', array( 
			'site'    => get_bloginfo('url'),
			'ajaxurl' => TDU.'/includes/ajax.php',
			'tdu'     => TDU));
}

/**
 * Default content for new post
 * @param  string $content
 * @return string
 */
function themeDefaultContent( $content ) 
{
	$content = "<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur ultrices, magna non porttitor commodo, massa nibh malesuada augue, non viverra odio mi quis nisl. Nullam convallis tincidunt dignissim. Nam vitae purus eget quam adipiscing aliquam. Sed a congue libero. Quisque feugiat tincidunt tortor sed sodales. Etiam mattis, justo in euismod volutpat, ipsum quam aliquet lectus, eu blandit neque libero eu justo. Nunc nibh nulla, accumsan in imperdiet vel, pretium in metus. Aenean in lacus at lacus imperdiet euismod in non nulla. Mauris luctus sodales metus, ac porttitor est lacinia non. Proin diam urna, feugiat at adipiscing in, varius vel mi. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Sed tincidunt commodo massa interdum iaculis.</p><!--more--><p>Aliquam metus libero, elementum et malesuada fermentum, sagittis et libero. Nullam quis odio vel ipsum facilisis viverra id sit amet nibh. Vestibulum ullamcorper luctus lacinia. Etiam accumsan, orci eu blandit vestibulum, purus ante malesuada purus, non commodo odio ligula quis turpis. Vestibulum scelerisque feugiat diam, eu mollis elit cursus nec. Quisque commodo ultricies scelerisque. In hac habitasse platea dictumst. Nullam hendrerit rhoncus lacus, id lobortis leo condimentum sed. Nulla facilisi. Quisque ut velit a neque feugiat rutrum at sit amet neque. Sed at libero dictum est aliquam porttitor. Morbi tempor nulla ut tellus malesuada cursus condimentum metus luctus. Quisque dui neque, lobortis id vehicula et, tincidunt eget justo. Morbi vulputate velit eget leo fermentum convallis. Nam mauris risus, consectetur a posuere ultricies, elementum non orci.</p><p>Ut viverra elit vel mauris venenatis gravida ut quis mi. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam eleifend urna sit amet nisi scelerisque pretium. Nulla facilisi. Donec et odio vel sem gravida cursus vestibulum dapibus enim. Pellentesque eget aliquet nisl. In malesuada, quam ac interdum placerat, elit metus consequat lorem, non consequat felis ipsum et ligula. Sed varius interdum volutpat. Vestibulum et libero nisi. Maecenas sit amet risus et sapien lobortis ornare vel quis ipsum. Nam aliquet euismod aliquam. Donec velit purus, convallis ac convallis vel, malesuada vitae erat.</p>";
	return $content;
}

/**
 * Add custom fields to $item nav object
 * in order to be used in custom Walker
 * @param  object $menu_item
 * @return object
 */
function gcAddCustomNavFields($menu_item) 
{
	$menu_item->icon_class = get_post_meta($menu_item->ID, '_menu_item_icon_class', true);
	$menu_item->css_class = get_post_meta($menu_item->ID, '_menu_item_css_class', true);
    return $menu_item;
}

/**
 * Save menu custom fields
 * @param  integer $menu_id        
 * @param  integer $menu_item_db_id
 * @param  array $args            
 */
function gcUpdateCustomNavFields($menu_id, $menu_item_db_id, $args) 
{     
    if (isset($_REQUEST['menu-item-icon_class']) AND is_array($_REQUEST['menu-item-icon_class'])) 
    {
        $icon_class_value = $_REQUEST['menu-item-icon_class'][$menu_item_db_id];
        update_post_meta($menu_item_db_id, '_menu_item_icon_class', $icon_class_value);
    }
    if (isset($_REQUEST['menu-item-icon_class']) AND is_array($_REQUEST['menu-item-css_class'])) 
    {
        $css_class_value = $_REQUEST['menu-item-css_class'][$menu_item_db_id];
        update_post_meta($menu_item_db_id, '_menu_item_css_class', $css_class_value);
    }
}

/**
 * Define new Walker edit
 * @param  object $walker  
 * @param  integer $menu_id
 * @return string
 */
function gcEditWalker($walker, $menu_id) 
{
	return 'GCEditWalker';
}

/**
 * Get user options for select control
 * Like this:
 * array( 
 * array('{ID}', '{display_name}'),
 * array('{ID}', '{display_name}')
 * );
 * @return array
 */
function getUsersOptions()
{
	$args = array(
		'blog_id'      => $GLOBALS['blog_id'],
		'role'         => '',
		'meta_key'     => '',
		'meta_value'   => '',
		'meta_compare' => '',
		'meta_query'   => array(),
		'include'      => array(),
		'exclude'      => array(),
		'orderby'      => 'login',
		'order'        => 'ASC',
		'offset'       => '',
		'search'       => '',
		'number'       => '',
		'count_total'  => false,
		'fields'       => 'all',
		'who'          => '');
	$users   = get_users($args);
	$options = array();
	if($users)
	{		
		foreach ($users as $u) 
		{
			$options[] = array($u->data->ID, $u->data->display_name);
		}
	}
	return $options;
}

/**
 * Export workers array
 * with cost and ID
 * @param  array $arr --- users
 * @return mixed      --- Workers [array] | false [boolean]
 */
function exportWorkers($arr)
{	
	if(!$arr) return false;

	$res = array();
	foreach ((array)$arr as $val) 
	{		
		$u = get_user_by('id', $val['user']);
		
		if($u)
		{
			$res[$u->data->ID] = sprintf('%s (<b>%s $</b>)', $u->data->display_name, $val['cost']);
		}
	}
	return $res;
}


/**
 * Get seesion project
 * @param  integer $session_id --- session id
 * @return mixed               --- project [object] | false [boolean]
 */
function getSessionProject($session_id)
{
	$terms = wp_get_post_terms($session_id, 'project');
	if($terms)
	{
		return $terms[0];
	}
	else return false;
}

if ( ! isset( $content_width ) ) {
	$content_width = 1200;
}