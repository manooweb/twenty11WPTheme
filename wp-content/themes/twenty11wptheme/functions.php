<?php
/**
 * Twenty 11 WP Theme functions and definitions
 *
 * Sets up the theme and provides some helper functions. Some helper functions
 * are used in the theme as custom template tags. Others are attached to action and
 * filter hooks in WordPress to change core functionality.
 *
 * The first function, twenty11wptheme_setup(), sets up the theme by registering support
 * for various features in WordPress, such as post thumbnails, navigation menus, and the like.
 *
 * When using a child theme (see http://codex.wordpress.org/Theme_Development and
 * http://codex.wordpress.org/Child_Themes), you can override certain functions
 * (those wrapped in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before the parent
 * theme's file, so the child theme functions would be used.
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are instead attached
 * to a filter or action hook. The hook can be removed by using remove_action() or
 * remove_filter() and you can attach your own function to the hook.
 *
 * We can remove the parent theme's hook only after it is attached, which means we need to
 * wait until setting up the child theme:
 *
 * <code>
 * add_action( 'after_setup_theme', 'my_child_theme_setup' );
 * function my_child_theme_setup() {
 *     // We are providing our own filter for excerpt_length (or using the unfiltered value)
 *     remove_filter( 'excerpt_length', 'twenty11wptheme_excerpt_length' );
 *     ...
 * }
 * </code>
 *
 * For more information on hooks, actions, and filters, see http://codex.wordpress.org/Plugin_API.
 *
 * @package WordPress
 * @subpackage Twenty11WPTheme
 * @since Twenty 11 WP Theme 1.0
 */

// Set the content width based on the theme's design and stylesheet.
if ( ! isset( $content_width ) )
	$content_width = 584;

/*
 * Tell WordPress to run twenty11wptheme_setup() when the 'after_setup_theme' hook is run.
 */
add_action( 'after_setup_theme', 'twenty11wptheme_setup' );

if ( ! function_exists( 'twenty11wptheme_setup' ) ):
/**
 * Set up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 *
 * To override twenty11wptheme_setup() in a child theme, add your own twenty11wptheme_setup to your child theme's
 * functions.php file.
 *
 * @uses load_theme_textdomain()    For translation/localization support.
 * @uses add_editor_style()         To style the visual editor.
 * @uses add_theme_support()        To add support for post thumbnails, automatic feed links, custom headers
 * 	                                and backgrounds, and post formats.
 * @uses register_nav_menus()       To add support for navigation menus.
 * @uses register_default_headers() To register the default custom header images provided with the theme.
 * @uses set_post_thumbnail_size()  To set a custom post thumbnail size.
 *
 * @since Twenty 11 WP Theme 1.0
 */
function twenty11wptheme_setup() {

	/*
	 * Make Twenty 11 WP Theme available for translation.
	 * Translations can be added to the /languages/ directory.
	 * If you're building a theme based on Twenty 11 WP Theme, use
	 * a find and replace to change 'twenty11wptheme' to the name
	 * of your theme in all the template files.
	 */
	load_theme_textdomain( 'twenty11wptheme', get_template_directory() . '/languages' );

	// This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style();

	// Load up our theme options page and related code.
	require( get_template_directory() . '/inc/theme-options.php' );

	// Grab Twenty 11 WP Theme's Ephemera widget.
	require( get_template_directory() . '/inc/widgets.php' );

	// Add default posts and comments RSS feed links to <head>.
	add_theme_support( 'automatic-feed-links' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menu( 'primary', __( 'Primary Menu', 'twenty11wptheme' ) );

	// Add support for a variety of post formats
	add_theme_support( 'post-formats', array( 'aside', 'link', 'gallery', 'status', 'quote', 'image' ) );

	$theme_options = twenty11wptheme_get_theme_options();
	if ( 'dark' == $theme_options['color_scheme'] )
		$default_background_color = '1d1d1d';
	else
		$default_background_color = 'e2e2e2';

	// Add support for custom backgrounds.
	add_theme_support( 'custom-background', array(
		/*
		 * Let WordPress know what our default background color is.
		 * This is dependent on our current color scheme.
		 */
		'default-color' => $default_background_color,
	) );

	// This theme uses Featured Images (also known as post thumbnails) for per-post/per-page Custom Header images
	add_theme_support( 'post-thumbnails' );

	// Add support for custom headers.
	$custom_header_support = array(
		// The default header text color.
		'default-text-color' => '000',
		// The height and width of our custom header.
		/**
		 * Filter the Twenty 11 WP Theme default header image width.
		 *
		 * @since Twenty 11 WP Theme 1.0
		 *
		 * @param int The default header image width in pixels. Default 1000.
		 */
		'width' => apply_filters( 'twenty11wptheme_header_image_width', 1000 ),
		/**
		 * Filter the Twenty 11 WP Theme default header image height.
		 *
		 * @since Twenty 11 WP Theme 1.0
		 *
		 * @param int The default header image height in pixels. Default 288.
		 */
		'height' => apply_filters( 'twenty11wptheme_header_image_height', 288 ),
		// Support flexible heights.
		'flex-height' => true,
		// Random image rotation by default.
		'random-default' => true,
		// Callback for styling the header.
		'wp-head-callback' => 'twenty11wptheme_header_style',
		// Callback for styling the header preview in the admin.
		'admin-head-callback' => 'twenty11wptheme_admin_header_style',
		// Callback used to display the header preview in the admin.
		'admin-preview-callback' => 'twenty11wptheme_admin_header_image',
	);

	add_theme_support( 'custom-header', $custom_header_support );

	if ( ! function_exists( 'get_custom_header' ) ) {
		// This is all for compatibility with versions of WordPress prior to 3.4.
		define( 'HEADER_TEXTCOLOR', $custom_header_support['default-text-color'] );
		define( 'HEADER_IMAGE', '' );
		define( 'HEADER_IMAGE_WIDTH', $custom_header_support['width'] );
		define( 'HEADER_IMAGE_HEIGHT', $custom_header_support['height'] );
		add_custom_image_header( $custom_header_support['wp-head-callback'], $custom_header_support['admin-head-callback'], $custom_header_support['admin-preview-callback'] );
		add_custom_background();
	}

	/*
	 * We'll be using post thumbnails for custom header images on posts and pages.
	 * We want them to be the size of the header image that we just defined.
	 * Larger images will be auto-cropped to fit, smaller ones will be ignored. See header.php.
	 */
	set_post_thumbnail_size( $custom_header_support['width'], $custom_header_support['height'], true );

	/*
	 * Add Twenty 11 WP Theme's custom image sizes.
	 * Used for large feature (header) images.
	 */
	add_image_size( 'large-feature', $custom_header_support['width'], $custom_header_support['height'], true );
	// Used for featured posts if a large-feature doesn't exist.
	add_image_size( 'small-feature', 500, 300 );

	// Default custom headers packaged with the theme. %s is a placeholder for the theme template directory URI.
	register_default_headers( array(
		'wheel' => array(
			'url' => '%s/images/headers/wheel.jpg',
			'thumbnail_url' => '%s/images/headers/wheel-thumbnail.jpg',
			/* translators: header image description */
			'description' => __( 'Wheel', 'twenty11wptheme' )
		),
		'shore' => array(
			'url' => '%s/images/headers/shore.jpg',
			'thumbnail_url' => '%s/images/headers/shore-thumbnail.jpg',
			/* translators: header image description */
			'description' => __( 'Shore', 'twenty11wptheme' )
		),
		'trolley' => array(
			'url' => '%s/images/headers/trolley.jpg',
			'thumbnail_url' => '%s/images/headers/trolley-thumbnail.jpg',
			/* translators: header image description */
			'description' => __( 'Trolley', 'twenty11wptheme' )
		),
		'pine-cone' => array(
			'url' => '%s/images/headers/pine-cone.jpg',
			'thumbnail_url' => '%s/images/headers/pine-cone-thumbnail.jpg',
			/* translators: header image description */
			'description' => __( 'Pine Cone', 'twenty11wptheme' )
		),
		'chessboard' => array(
			'url' => '%s/images/headers/chessboard.jpg',
			'thumbnail_url' => '%s/images/headers/chessboard-thumbnail.jpg',
			/* translators: header image description */
			'description' => __( 'Chessboard', 'twenty11wptheme' )
		),
		'lanterns' => array(
			'url' => '%s/images/headers/lanterns.jpg',
			'thumbnail_url' => '%s/images/headers/lanterns-thumbnail.jpg',
			/* translators: header image description */
			'description' => __( 'Lanterns', 'twenty11wptheme' )
		),
		'willow' => array(
			'url' => '%s/images/headers/willow.jpg',
			'thumbnail_url' => '%s/images/headers/willow-thumbnail.jpg',
			/* translators: header image description */
			'description' => __( 'Willow', 'twenty11wptheme' )
		),
		'hanoi' => array(
			'url' => '%s/images/headers/hanoi.jpg',
			'thumbnail_url' => '%s/images/headers/hanoi-thumbnail.jpg',
			/* translators: header image description */
			'description' => __( 'Hanoi Plant', 'twenty11wptheme' )
		)
	) );
}
endif; // twenty11wptheme_setup

if ( ! function_exists( 'twenty11wptheme_header_style' ) ) :
/**
 * Styles the header image and text displayed on the blog.
 *
 * @since Twenty 11 WP Theme 1.0
 */
function twenty11wptheme_header_style() {
	$text_color = get_header_textcolor();

	// If no custom options for text are set, let's bail.
	if ( $text_color == HEADER_TEXTCOLOR )
		return;

	// If we get this far, we have custom styles. Let's do this.
	?>
	<style type="text/css" id="twenty11wptheme-header-css">
	<?php
		// Has the text been hidden?
		if ( 'blank' == $text_color ) :
	?>
		#site-title,
		#site-description {
			position: absolute !important;
			clip: rect(1px 1px 1px 1px); /* IE6, IE7 */
			clip: rect(1px, 1px, 1px, 1px);
		}
	<?php
		// If the user has set a custom color for the text use that
		else :
	?>
		#site-title a,
		#site-description {
			color: #<?php echo $text_color; ?> !important;
		}
	<?php endif; ?>
	</style>
	<?php
}
endif; // twenty11wptheme_header_style

if ( ! function_exists( 'twenty11wptheme_admin_header_style' ) ) :
/**
 * Styles the header image displayed on the Appearance > Header admin panel.
 *
 * Referenced via add_theme_support('custom-header') in twenty11wptheme_setup().
 *
 * @since Twenty 11 WP Theme 1.0
 */
function twenty11wptheme_admin_header_style() {
?>
	<style type="text/css" id="twenty11wptheme-admin-header-css">
	.appearance_page_custom-header #headimg {
		border: none;
	}
	#headimg h1,
	#desc {
		font-family: "Helvetica Neue", Arial, Helvetica, "Nimbus Sans L", sans-serif;
	}
	#headimg h1 {
		margin: 0;
	}
	#headimg h1 a {
		font-size: 32px;
		line-height: 36px;
		text-decoration: none;
	}
	#desc {
		font-size: 14px;
		line-height: 23px;
		padding: 0 0 3em;
	}
	<?php
		// If the user has set a custom color for the text use that
		if ( get_header_textcolor() != HEADER_TEXTCOLOR ) :
	?>
		#site-title a,
		#site-description {
			color: #<?php echo get_header_textcolor(); ?>;
		}
	<?php endif; ?>
	#headimg img {
		max-width: 1000px;
		height: auto;
		width: 100%;
	}
	</style>
<?php
}
endif; // twenty11wptheme_admin_header_style

if ( ! function_exists( 'twenty11wptheme_admin_header_image' ) ) :
/**
 * Custom header image markup displayed on the Appearance > Header admin panel.
 *
 * Referenced via add_theme_support('custom-header') in twenty11wptheme_setup().
 *
 * @since Twenty 11 WP Theme 1.0
 */
function twenty11wptheme_admin_header_image() { ?>
	<div id="headimg">
		<?php
		$color = get_header_textcolor();
		$image = get_header_image();
		if ( $color && $color != 'blank' )
			$style = ' style="color:#' . $color . '"';
		else
			$style = ' style="display:none"';
		?>
		<h1 class="displaying-header-text"><a id="name"<?php echo $style; ?> onclick="return false;" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
		<div id="desc" class="displaying-header-text"<?php echo $style; ?>><?php bloginfo( 'description' ); ?></div>
		<?php if ( $image ) : ?>
			<img src="<?php echo esc_url( $image ); ?>" alt="" />
		<?php endif; ?>
	</div>
<?php }
endif; // twenty11wptheme_admin_header_image

/**
 * Set the post excerpt length to 40 words.
 *
 * To override this length in a child theme, remove
 * the filter and add your own function tied to
 * the excerpt_length filter hook.
 *
 * @since Twenty 11 WP Theme 1.0
 *
 * @param int $length The number of excerpt characters.
 * @return int The filtered number of characters.
 */
function twenty11wptheme_excerpt_length( $length ) {
	return 40;
}
add_filter( 'excerpt_length', 'twenty11wptheme_excerpt_length' );

if ( ! function_exists( 'twenty11wptheme_continue_reading_link' ) ) :
/**
 * Return a "Continue Reading" link for excerpts
 *
 * @since Twenty 11 WP Theme 1.0
 *
 * @return string The "Continue Reading" HTML link.
 */
function twenty11wptheme_continue_reading_link() {
	return ' <a href="'. esc_url( get_permalink() ) . '">' . __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'twenty11wptheme' ) . '</a>';
}
endif; // twenty11wptheme_continue_reading_link

/**
 * Replace "[...]" in the Read More link with an ellipsis.
 *
 * The "[...]" is appended to automatically generated excerpts.
 *
 * To override this in a child theme, remove the filter and add your own
 * function tied to the excerpt_more filter hook.
 *
 * @since Twenty 11 WP Theme 1.0
 *
 * @param string $more The Read More text.
 * @return The filtered Read More text.
 */
function twenty11wptheme_auto_excerpt_more( $more ) {
	return ' &hellip;' . twenty11wptheme_continue_reading_link();
}
add_filter( 'excerpt_more', 'twenty11wptheme_auto_excerpt_more' );

/**
 * Add a pretty "Continue Reading" link to custom post excerpts.
 *
 * To override this link in a child theme, remove the filter and add your own
 * function tied to the get_the_excerpt filter hook.
 *
 * @since Twenty 11 WP Theme 1.0
 *
 * @param string $output The "Continue Reading" link.
 * @return string The filtered "Continue Reading" link.
 */
function twenty11wptheme_custom_excerpt_more( $output ) {
	if ( has_excerpt() && ! is_attachment() ) {
		$output .= twenty11wptheme_continue_reading_link();
	}
	return $output;
}
add_filter( 'get_the_excerpt', 'twenty11wptheme_custom_excerpt_more' );

/**
 * Show a home link for the wp_nav_menu() fallback, wp_page_menu().
 *
 * @since Twenty 11 WP Theme 1.0
 *
 * @param array $args The page menu arguments. @see wp_page_menu()
 * @return array The filtered page menu arguments.
 */
function twenty11wptheme_page_menu_args( $args ) {
	if ( ! isset( $args['show_home'] ) )
		$args['show_home'] = true;
	return $args;
}
add_filter( 'wp_page_menu_args', 'twenty11wptheme_page_menu_args' );

/**
 * Register sidebars and widgetized areas.
 *
 * Also register the default Epherma widget.
 *
 * @since Twenty 11 WP Theme 1.0
 */
function twenty11wptheme_widgets_init() {

	register_widget( 'Twenty11WPTheme_Ephemera_Widget' );

	register_sidebar( array(
		'name' => __( 'Main Sidebar', 'twenty11wptheme' ),
		'id' => 'sidebar-1',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<div class="widget-title">',
		'after_title' => '</div>',
	) );

	register_sidebar( array(
		'name' => __( 'Showcase Sidebar', 'twenty11wptheme' ),
		'id' => 'sidebar-2',
		'description' => __( 'The sidebar for the optional Showcase Template', 'twenty11wptheme' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<div class="widget-title">',
		'after_title' => '</div>',
	) );

	register_sidebar( array(
		'name' => __( 'Footer Area One', 'twenty11wptheme' ),
		'id' => 'sidebar-3',
		'description' => __( 'An optional widget area for your site footer', 'twenty11wptheme' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<div class="widget-title">',
		'after_title' => '</div>',
	) );

	register_sidebar( array(
		'name' => __( 'Footer Area Two', 'twenty11wptheme' ),
		'id' => 'sidebar-4',
		'description' => __( 'An optional widget area for your site footer', 'twenty11wptheme' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<div class="widget-title">',
		'after_title' => '</div>',
	) );

	register_sidebar( array(
		'name' => __( 'Footer Area Three', 'twenty11wptheme' ),
		'id' => 'sidebar-5',
		'description' => __( 'An optional widget area for your site footer', 'twenty11wptheme' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => "</aside>",
		'before_title' => '<div class="widget-title">',
		'after_title' => '</div>',
	) );
}
add_action( 'widgets_init', 'twenty11wptheme_widgets_init' );

if ( ! function_exists( 'twenty11wptheme_content_nav' ) ) :
/**
 * Display navigation to next/previous pages when applicable.
 *
 * @since Twenty 11 WP Theme 1.0
 *
 * @param string $html_id The HTML id attribute.
 */
function twenty11wptheme_content_nav( $html_id ) {
	global $wp_query;

	if ( $wp_query->max_num_pages > 1 ) : ?>
		<nav id="<?php echo esc_attr( $html_id ); ?>">
			<div class="assistive-text"><?php _e( 'Post navigation', 'twenty11wptheme' ); ?></div>
			<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'twenty11wptheme' ) ); ?></div>
			<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'twenty11wptheme' ) ); ?></div>
		</nav><!-- #nav-above -->
	<?php endif;
}
endif; // twenty11wptheme_content_nav

/**
 * Return the first link from the post content. If none found, the
 * post permalink is used as a fallback.
 *
 * @since Twenty 11 WP Theme 1.0
 *
 * @uses get_url_in_content() to get the first URL from the post content.
 *
 * @return string The first link.
 */
function twenty11wptheme_get_first_url() {
	$content = get_the_content();
	$has_url = function_exists( 'get_url_in_content' ) ? get_url_in_content( $content ) : false;

	if ( ! $has_url )
		$has_url = twenty11wptheme_url_grabber();

	/** This filter is documented in wp-includes/link-template.php */
	return ( $has_url ) ? $has_url : apply_filters( 'the_permalink', get_permalink() );
}

/**
 * Return the URL for the first link found in the post content.
 *
 * @since Twenty 11 WP Theme 1.0
 *
 * @return string|bool URL or false when no link is present.
 */
function twenty11wptheme_url_grabber() {
	if ( ! preg_match( '/<a\s[^>]*?href=[\'"](.+?)[\'"]/is', get_the_content(), $matches ) )
		return false;

	return esc_url_raw( $matches[1] );
}

/**
 * Count the number of footer sidebars to enable dynamic classes for the footer.
 *
 * @since Twenty 11 WP Theme 1.0
 */
function twenty11wptheme_footer_sidebar_class() {
	$count = 0;

	if ( is_active_sidebar( 'sidebar-3' ) )
		$count++;

	if ( is_active_sidebar( 'sidebar-4' ) )
		$count++;

	if ( is_active_sidebar( 'sidebar-5' ) )
		$count++;

	$class = '';

	switch ( $count ) {
		case '1':
			$class = 'one';
			break;
		case '2':
			$class = 'two';
			break;
		case '3':
			$class = 'three';
			break;
	}

	if ( $class )
		echo 'class="' . $class . '"';
}

if ( ! function_exists( 'twenty11wptheme_comment' ) ) :
/**
 * Template for comments and pingbacks.
 *
 * To override this walker in a child theme without modifying the comments template
 * simply create your own twenty11wptheme_comment(), and that function will be used instead.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 *
 * @since Twenty 11 WP Theme 1.0
 *
 * @param object $comment The comment object.
 * @param array  $args    An array of comment arguments. @see get_comment_reply_link()
 * @param int    $depth   The depth of the comment.
 */
function twenty11wptheme_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;
	switch ( $comment->comment_type ) :
		case 'pingback' :
		case 'trackback' :
	?>
	<li class="post pingback">
		<p><?php _e( 'Pingback:', 'twenty11wptheme' ); ?> <?php comment_author_link(); ?><?php edit_comment_link( __( 'Edit', 'twenty11wptheme' ), '<span class="edit-link">', '</span>' ); ?></p>
	<?php
			break;
		default :
	?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
		<article id="comment-<?php comment_ID(); ?>" class="comment">
			<footer class="comment-meta">
				<div class="comment-author vcard">
					<?php
						$avatar_size = 68;
						if ( '0' != $comment->comment_parent )
							$avatar_size = 39;

						echo get_avatar( $comment, $avatar_size );

						/* translators: 1: comment author, 2: date and time */
						printf( __( '%1$s on %2$s <span class="says">said:</span>', 'twenty11wptheme' ),
							sprintf( '<span class="fn">%s</span>', get_comment_author_link() ),
							sprintf( '<a href="%1$s"><time datetime="%2$s">%3$s</time></a>',
								esc_url( get_comment_link( $comment->comment_ID ) ),
								get_comment_time( 'c' ),
								/* translators: 1: date, 2: time */
								sprintf( __( '%1$s at %2$s', 'twenty11wptheme' ), get_comment_date(), get_comment_time() )
							)
						);
					?>

					<?php edit_comment_link( __( 'Edit', 'twenty11wptheme' ), '<span class="edit-link">', '</span>' ); ?>
				</div><!-- .comment-author .vcard -->

				<?php if ( $comment->comment_approved == '0' ) : ?>
					<em class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'twenty11wptheme' ); ?></em>
					<br />
				<?php endif; ?>

			</footer>

			<div class="comment-content"><?php comment_text(); ?></div>

			<div class="reply">
				<?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply <span>&darr;</span>', 'twenty11wptheme' ), 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
			</div><!-- .reply -->
		</article><!-- #comment-## -->

	<?php
			break;
	endswitch;
}
endif; // ends check for twenty11wptheme_comment()

if ( ! function_exists( 'twenty11wptheme_posted_on' ) ) :
/**
 * Print HTML with meta information for the current post-date/time and author.
 *
 * Create your own twenty11wptheme_posted_on to override in a child theme
 *
 * @since Twenty 11 WP Theme 1.0
 */
function twenty11wptheme_posted_on() {
	printf( __( '<span class="sep">Posted on </span><a href="%1$s" title="%2$s" rel="bookmark"><time class="entry-date" datetime="%3$s">%4$s</time></a><span class="by-author"> <span class="sep"> by </span> <span class="author vcard"><a class="url fn n" href="%5$s" title="%6$s" rel="author">%7$s</a></span></span>', 'twenty11wptheme' ),
		esc_url( get_permalink() ),
		esc_attr( get_the_time() ),
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() ),
		esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
		esc_attr( sprintf( __( 'View all posts by %s', 'twenty11wptheme' ), get_the_author() ) ),
		get_the_author()
	);
}
endif;

/**
 * Add two classes to the array of body classes.
 *
 * The first is if the site has only had one author with published posts.
 * The second is if a singular post being displayed
 *
 * @since Twenty 11 WP Theme 1.0
 *
 * @param array $classes Existing body classes.
 * @return array The filtered array of body classes.
 */
function twenty11wptheme_body_classes( $classes ) {

	if ( function_exists( 'is_multi_author' ) && ! is_multi_author() )
		$classes[] = 'single-author';

	if ( is_singular() && ! is_home() && ! is_page_template( 'showcase.php' ) && ! is_page_template( 'sidebar-page.php' ) )
		$classes[] = 'singular';

	return $classes;
}
add_filter( 'body_class', 'twenty11wptheme_body_classes' );

/**
 * Retrieve the IDs for images in a gallery.
 *
 * @uses get_post_galleries() First, if available. Falls back to shortcode parsing,
 *                            then as last option uses a get_posts() call.
 *
 * @since Twenty 11 WP Theme 1.0
 *
 * @return array List of image IDs from the post gallery.
 */
function twenty11wptheme_get_gallery_images() {
	$images = array();

	if ( function_exists( 'get_post_galleries' ) ) {
		$galleries = get_post_galleries( get_the_ID(), false );
		if ( isset( $galleries[0]['ids'] ) )
		 	$images = explode( ',', $galleries[0]['ids'] );
	} else {
		$pattern = get_shortcode_regex();
		preg_match( "/$pattern/s", get_the_content(), $match );
		$atts = shortcode_parse_atts( $match[3] );
		if ( isset( $atts['ids'] ) )
			$images = explode( ',', $atts['ids'] );
	}

	if ( ! $images ) {
		$images = get_posts( array(
			'fields'         => 'ids',
			'numberposts'    => 999,
			'order'          => 'ASC',
			'orderby'        => 'menu_order',
			'post_mime_type' => 'image',
			'post_parent'    => get_the_ID(),
			'post_type'      => 'attachment',
		) );
	}

	return $images;
}
