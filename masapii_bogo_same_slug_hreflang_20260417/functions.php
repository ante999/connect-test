<?php
/**
 * Masapii functions and definitions
 *
 * @package Masapii
 */

if ( ! defined( 'MASAPII_VERSION' ) ) {
	define( 'MASAPII_VERSION', '1.0.0' );
}

/* ---------- Theme Setup ---------- */

if ( ! function_exists( 'masapii_setup' ) ) :
	function masapii_setup() {
		load_theme_textdomain( 'masapii', get_template_directory() . '/languages' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
		add_theme_support( 'customize-selective-refresh-widgets' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'align-wide' );
		add_theme_support( 'wp-block-styles' );
		add_theme_support( 'editor-styles' );

		add_theme_support( 'custom-logo', array(
			'height'      => 80,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		) );

		add_theme_support( 'custom-background', array(
			'default-color' => 'ffffff',
			'default-image' => '',
		) );

		register_nav_menus( array(
			'primary'  => __( 'Primary Menu', 'masapii' ),
			'footer'   => __( 'Footer Menu', 'masapii' ),
		) );

		add_image_size( 'masapii-hero', 1920, 1080, true );
		add_image_size( 'masapii-card', 600, 400, true );
	}
endif;
add_action( 'after_setup_theme', 'masapii_setup' );

/* ---------- Content Width ---------- */

function masapii_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'masapii_content_width', 1100 );
}
add_action( 'after_setup_theme', 'masapii_content_width', 0 );

/* ---------- Widgets ---------- */

function masapii_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'masapii' ),
		'id'            => 'sidebar-1',
		'description'   => __( 'Add widgets here.', 'masapii' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => __( 'Footer Widget Area', 'masapii' ),
		'id'            => 'footer-1',
		'description'   => __( 'Footer widget area.', 'masapii' ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
	register_sidebar( array(
		'name'          => __( 'After Article Widget Area', 'masapii' ),
		'id'            => 'after-article-1',
		'description'   => __( 'Widgets displayed below singular post/page content.', 'masapii' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'masapii_widgets_init' );

/* ---------- Asset Helpers ---------- */

function masapii_asset_version( $path ) {
	$file = get_template_directory() . '/' . ltrim( $path, '/' );
	return file_exists( $file ) ? (string) filemtime( $file ) : MASAPII_VERSION;
}

/* ---------- Enqueue Scripts & Styles ---------- */

function masapii_scripts() {
	// Main stylesheet — non-render-blocking via preload
	wp_enqueue_style(
		'masapii-style',
		get_stylesheet_uri(),
		array(),
		masapii_asset_version( 'style.css' )
	);

	// Navigation JS — deferred
	wp_enqueue_script(
		'masapii-navigation',
		get_template_directory_uri() . '/js/navigation.js',
		array(),
		masapii_asset_version( 'js/navigation.js' ),
		true
	);

	// Comment reply
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'masapii_scripts' );

/* ---------- Non-render-blocking CSS ---------- */

/**
 * Convert theme stylesheet to preload + onload pattern
 * to eliminate render-blocking CSS from PageSpeed.
 */
function masapii_preload_css( $html, $handle, $href, $media ) {
	if ( 'masapii-style' !== $handle ) {
		return $html;
	}
	// preload + onload fallback: loads CSS without blocking render
	return sprintf(
		'<link rel="preload" href="%1$s" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">' .
		'<noscript><link rel="stylesheet" href="%1$s" media="%2$s"></noscript>' . "\n",
		esc_url( $href ),
		esc_attr( $media )
	);
}
add_filter( 'style_loader_tag', 'masapii_preload_css', 10, 4 );

/**
 * Inline critical above-the-fold CSS to prevent FOUC
 * when main stylesheet is preloaded.
 */
function masapii_critical_css() {
	?>
	<style id="masapii-critical">
	*,*::before,*::after{box-sizing:border-box}
	html{line-height:1.6;-webkit-text-size-adjust:100%}
	body{margin:0;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Hiragino Kaku Gothic ProN","Noto Sans JP",sans-serif;font-size:16px;color:#333;background:#fff}
	h1,h2,h3{line-height:1.3;font-weight:700}
	p{margin:0 0 1em}
	a{color:#e94560;text-decoration:none}
	img{max-width:100%;height:auto;display:block}
	.site-header{background:#1a1a2e;color:#fff;position:relative;z-index:100}
	.site-header .mp-container{display:flex;align-items:center;justify-content:space-between;max-width:1100px;margin:0 auto;padding-top:.8em;padding-bottom:.8em;padding-left:1.5rem;padding-right:1.5rem}
	.site-branding{display:flex;align-items:center;gap:.8em}
	.site-title{font-size:1.3rem;margin:0;font-weight:700}
	.site-title a{color:#fff;text-decoration:none}
	.main-navigation ul{list-style:none;margin:0;padding:0;display:flex;gap:.2em}
	.main-navigation a{display:block;padding:.5em .9em;color:rgba(255,255,255,.9);text-decoration:none;font-size:.9rem}
	.menu-toggle{display:none}
	.mp-lang-switcher{display:flex;align-items:center;gap:.3em}
	.mp-lang-switcher ul{list-style:none;margin:0;padding:0;display:flex;gap:.3em}
	.mp-lang-switcher li{margin:0}
	.mp-lang-switcher a{display:inline-block;padding:.3em .6em;font-size:.8rem;color:rgba(255,255,255,.8);text-decoration:none;border-radius:4px}
	.mp-lang-switcher .current-language a,.mp-lang-switcher a[aria-current]{background:rgba(255,255,255,.15);color:#fff;font-weight:600}
	.mp-hero{position:relative;width:100%;min-height:60vh;display:flex;align-items:center;justify-content:center;overflow:hidden;color:#fff;contain:layout style}
	.mp-hero__media{position:absolute;top:0;left:0;width:100%;height:100%;object-fit:cover;z-index:1}
	.mp-hero__picture{position:absolute;top:0;left:0;width:100%;height:100%;z-index:1}
	.mp-hero__picture img{width:100%;height:100%;object-fit:cover}
	.mp-hero__overlay{position:absolute;top:0;left:0;width:100%;height:100%;z-index:2}
	.mp-hero__content{position:relative;z-index:3;text-align:center;padding:2em 1.5rem;max-width:800px}
	.mp-hero__title{margin:0 0 .5em;line-height:1.2;font-weight:800}
	.mp-hero__subtitle{margin:0}
	.site-main{padding:2em 0}
	.screen-reader-text{clip:rect(1px,1px,1px,1px);clip-path:inset(50%);height:1px;width:1px;margin:-1px;overflow:hidden;position:absolute}
	@media(max-width:768px){.site-header .mp-container{flex-wrap:wrap}.menu-toggle{display:block;background:none;border:2px solid rgba(255,255,255,.5);color:#fff;padding:.4em .8em;border-radius:4px;cursor:pointer;font-size:.9rem}.main-navigation ul{display:none;flex-direction:column;width:100%;padding-top:.5em}.main-navigation.toggled ul{display:flex}}
	</style>
	<?php
}
add_action( 'wp_head', 'masapii_critical_css', 1 );

/* ---------- Defer non-critical scripts ---------- */

function masapii_defer_scripts( $tag, $handle, $src ) {
	// Skip admin
	if ( is_admin() ) {
		return $tag;
	}
	$defer_handles = array( 'masapii-navigation' );
	if ( in_array( $handle, $defer_handles, true ) ) {
		return sprintf( '<script src="%s" defer></script>' . "\n", esc_url( $src ) );
	}
	return $tag;
}
add_filter( 'script_loader_tag', 'masapii_defer_scripts', 10, 3 );

/* ---------- Performance: Clean up wp_head ---------- */

function masapii_cleanup_head() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );
}
add_action( 'init', 'masapii_cleanup_head' );


/* ---------- Editor Sidebar Display Meta ---------- */

function masapii_sanitize_boolean_meta( $value ) {
	return (bool) rest_sanitize_boolean( $value );
}

function masapii_register_display_post_meta() {
	$post_types = array( 'post', 'page' );
	$meta_keys  = array(
		'_masapii_enable_wpautop'      => true,
		'_masapii_show_featured_image' => true,
		'_masapii_show_toc'            => false,
		'_masapii_show_sidebar'        => false,
	);

	foreach ( $post_types as $post_type ) {
		foreach ( $meta_keys as $meta_key => $default ) {
			register_post_meta(
				$post_type,
				$meta_key,
				array(
					'single'            => true,
					'type'              => 'boolean',
					'default'           => $default,
					'sanitize_callback' => 'masapii_sanitize_boolean_meta',
					'show_in_rest'      => true,
					'auth_callback'     => 'masapii_post_meta_auth_callback',
				)
			);
		}
	}
}
add_action( 'init', 'masapii_register_display_post_meta' );

function masapii_get_boolean_post_meta( $post_id, $meta_key, $default = false ) {
	$post_id = (int) $post_id;
	if ( $post_id <= 0 ) {
		return (bool) $default;
	}

	$stored = get_post_meta( $post_id, $meta_key, true );
	if ( '' === $stored || null === $stored ) {
		return (bool) $default;
	}

	return (bool) rest_sanitize_boolean( $stored );
}

function masapii_content_looks_like_raw_html( $content ) {
	$content = (string) $content;
	if ( '' === trim( $content ) ) {
		return false;
	}

	if ( function_exists( 'has_blocks' ) && has_blocks( $content ) ) {
		return false;
	}

	return (bool) preg_match( '/<(section|div|article|aside|nav|header|footer|main|figure|figcaption|table|thead|tbody|tfoot|tr|td|th|ul|ol|li|dl|dt|dd|form)\b/i', $content );
}

function masapii_enable_wpautop_for_post( $post_id = null ) {
	$post_id = $post_id ? (int) $post_id : (int) get_the_ID();
	$enabled = masapii_get_boolean_post_meta( $post_id, '_masapii_enable_wpautop', true );

	if ( ! $enabled ) {
		return false;
	}

	$post = get_post( $post_id );
	if ( $post instanceof WP_Post && masapii_content_looks_like_raw_html( $post->post_content ) ) {
		return false;
	}

	return true;
}

function masapii_show_featured_image_for_post( $post_id = null ) {
	$post_id = $post_id ? (int) $post_id : (int) get_the_ID();
	return masapii_get_boolean_post_meta( $post_id, '_masapii_show_featured_image', true );
}

function masapii_show_toc_for_post( $post_id = null ) {
	$post_id = $post_id ? (int) $post_id : (int) get_the_ID();
	return masapii_get_boolean_post_meta( $post_id, '_masapii_show_toc', false );
}

function masapii_show_sidebar_for_post( $post_id = null ) {
	$post_id = $post_id ? (int) $post_id : (int) get_the_ID();
	return masapii_get_boolean_post_meta( $post_id, '_masapii_show_sidebar', false );
}

function masapii_parse_id_csv( $value ) {
	$raw_ids = preg_split( '/[\s,]+/', (string) $value );
	$ids     = array();

	foreach ( (array) $raw_ids as $raw_id ) {
		$raw_id = trim( (string) $raw_id );
		if ( '' === $raw_id ) {
			continue;
		}

		$id = absint( $raw_id );
		if ( $id > 0 ) {
			$ids[] = $id;
		}
	}

	$ids = array_values( array_unique( $ids ) );
	sort( $ids );

	return $ids;
}

function masapii_get_after_article_excluded_ids() {
	return masapii_parse_id_csv( get_theme_mod( 'masapii_after_article_excluded_ids', '' ) );
}

function masapii_render_after_article_widget_area() {
	if ( ! is_singular( array( 'post', 'page' ) ) || ! is_active_sidebar( 'after-article-1' ) ) {
		return;
	}

	$post_id = get_the_ID();
	if ( $post_id <= 0 ) {
		return;
	}

	if ( in_array( $post_id, masapii_get_after_article_excluded_ids(), true ) ) {
		return;
	}

	echo '<aside class="mp-after-article-widgets widget-area" aria-label="' . esc_attr__( 'After article widgets', 'masapii' ) . '">';
	dynamic_sidebar( 'after-article-1' );
	echo '</aside>';
}

function masapii_make_unique_heading_id( $base_id, array &$used_ids ) {
	$base_id = sanitize_title( $base_id );
	if ( '' === $base_id ) {
		$base_id = 'section';
	}

	$id      = $base_id;
	$counter = 2;
	while ( in_array( $id, $used_ids, true ) ) {
		$id = $base_id . '-' . $counter;
		$counter++;
	}

	$used_ids[] = $id;
	return $id;
}

function masapii_build_toc_markup( $content ) {
	$content = (string) $content;
	if ( '' === trim( $content ) || false === stripos( $content, '<h2' ) ) {
		return array( '', $content );
	}

	$used_ids = array();
	$items    = array();

	$content = preg_replace_callback(
		'/<h([2-4])([^>]*)>(.*?)<\/h\1>/is',
		function ( $matches ) use ( &$used_ids, &$items ) {
			$level = (int) $matches[1];
			$attrs = (string) $matches[2];
			$inner = (string) $matches[3];
			$text  = trim( wp_strip_all_tags( $inner ) );

			if ( '' === $text ) {
				return $matches[0];
			}

			if ( preg_match( '/\sid=(["\'])(.*?)\1/i', $attrs, $id_match ) ) {
				$id = masapii_make_unique_heading_id( $id_match[2], $used_ids );
				$attrs = preg_replace( '/\sid=(["\'])(.*?)\1/i', ' id="' . esc_attr( $id ) . '"', $attrs, 1 );
			} else {
				$id    = masapii_make_unique_heading_id( $text, $used_ids );
				$attrs = rtrim( $attrs ) . ' id="' . esc_attr( $id ) . '"';
			}

			$items[] = array(
				'level' => $level,
				'id'    => $id,
				'text'  => $text,
			);

			return sprintf( '<h%d%s>%s</h%d>', $level, $attrs, $inner, $level );
		},
		$content
	);

	if ( empty( $items ) ) {
		return array( '', $content );
	}

	$toc  = '<nav class="masapii-toc" aria-label="' . esc_attr__( 'Table of contents', 'masapii' ) . '">';
	$toc .= '<p class="masapii-toc__title">' . esc_html__( '目次', 'masapii' ) . '</p>';
	$toc .= '<ol class="masapii-toc__list">';

	foreach ( $items as $item ) {
		$toc .= sprintf(
			'<li class="masapii-toc__item masapii-toc__item--level-%1$d"><a href="#%2$s">%3$s</a></li>',
			(int) $item['level'],
			esc_attr( $item['id'] ),
			esc_html( $item['text'] )
		);
	}

	$toc .= '</ol></nav>';

	return array( $toc, $content );
}

function masapii_render_singular_content( $more_link_text = null, $strip_teaser = false ) {
	$post = get_post();
	if ( ! $post instanceof WP_Post ) {
		the_content( $more_link_text );
		return;
	}

	$removed_autop      = false;
	$removed_shortcode = false;
	if ( ! masapii_enable_wpautop_for_post( $post->ID ) ) {
		if ( has_filter( 'the_content', 'wpautop' ) ) {
			remove_filter( 'the_content', 'wpautop' );
			$removed_autop = true;
		}
		if ( has_filter( 'the_content', 'shortcode_unautop' ) ) {
			remove_filter( 'the_content', 'shortcode_unautop' );
			$removed_shortcode = true;
		}
	}

	$content = apply_filters( 'the_content', get_the_content( $more_link_text, $strip_teaser, $post ) );

	if ( ! masapii_enable_wpautop_for_post( $post->ID ) ) {
		if ( $removed_autop ) {
			add_filter( 'the_content', 'wpautop' );
		}
		if ( $removed_shortcode ) {
			add_filter( 'the_content', 'shortcode_unautop' );
		}
	}

	if ( masapii_show_toc_for_post( $post->ID ) ) {
		list( $toc, $content ) = masapii_build_toc_markup( $content );
		$content = $toc . $content;
	}

	echo $content;
}

function masapii_output_toc_styles() {
	if ( ! is_singular() || ! masapii_show_toc_for_post( get_queried_object_id() ) ) {
		return;
	}
	?>
	<style id="masapii-toc-style">
	.masapii-toc{margin:0 0 1.5em;padding:1em 1.25em;border:1px solid #d8dee6;border-radius:8px;background:#f8fafc}
	.masapii-toc__title{margin:0 0 .75em;font-weight:700}
	.masapii-toc__list{margin:0;padding-left:1.25em}
	.masapii-toc__item{margin:.35em 0}
	.masapii-toc__item--level-3{margin-left:1em}
	.masapii-toc__item--level-4{margin-left:2em}
	.masapii-toc a{text-decoration:none}
	</style>
	<?php
}
add_action( 'wp_head', 'masapii_output_toc_styles', 30 );

function masapii_start_wp_head_script_type_cleanup() {
	if ( is_admin() || is_feed() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return;
	}
	ob_start();
}
add_action( 'wp_head', 'masapii_start_wp_head_script_type_cleanup', -1000 );

function masapii_end_wp_head_script_type_cleanup() {
	if ( is_admin() || is_feed() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return;
	}

	if ( ob_get_level() > 0 ) {
		$head = ob_get_clean();
		$head = str_replace( ' type="text/javascript"', '', $head );
		$head = preg_replace( '/<(meta|link|base|area|br|col|embed|hr|img|input|param|source|track|wbr)(\b[^<>]*?)\s*\/?>/i', '<$1$2>', $head );
		echo $head;
	}
}
add_action( 'wp_head', 'masapii_end_wp_head_script_type_cleanup', 1000 );

/* ---------- Front-end CSS Validator Compatibility ---------- */

function masapii_fix_frontend_css_validator_styles() {
	if ( is_admin() || is_feed() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return;
	}

	// Remove the inline style block that outputs contain-intrinsic-size.
	wp_dequeue_style( 'wp-img-auto-sizes-contain' );
	wp_deregister_style( 'wp-img-auto-sizes-contain' );

	global $wp_styles;
	if ( ! ( $wp_styles instanceof WP_Styles ) ) {
		return;
	}

	$after = $wp_styles->get_data( 'wp-block-image', 'after' );
	if ( empty( $after ) || ! is_array( $after ) ) {
		return;
	}

	foreach ( $after as $index => $css ) {
		$css = str_replace( 'right:calc(env(safe-area-inset-right) + 16px);', 'right:16px;', $css );
		$css = str_replace( 'top:calc(env(safe-area-inset-top) + 16px);', 'top:16px;', $css );
		$after[ $index ] = $css;
	}

	$wp_styles->registered['wp-block-image']->extra['after'] = $after;
}
add_action( 'wp_head', 'masapii_fix_frontend_css_validator_styles', 7 );

/* ---------- Body Classes ---------- */

function masapii_body_classes( $classes ) {
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}
	if ( ! is_active_sidebar( 'sidebar-1' ) ) {
		$classes[] = 'no-sidebar';
	}
	// LP template class
	if ( is_page_template( 'template-parts/lp.php' ) ) {
		$classes[] = 'mp-lp';
	}
	// Front page class
	if ( is_front_page() ) {
		$classes[] = 'mp-front-page';
	}
	return $classes;
}
add_filter( 'body_class', 'masapii_body_classes' );

/* ---------- Pingback ---------- */

function masapii_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'masapii_pingback_header' );

/* ---------- Include Modules ---------- */

require get_template_directory() . '/inc/template-tags.php';
require get_template_directory() . '/inc/template-functions.php';
require get_template_directory() . '/inc/customizer.php';
require get_template_directory() . '/inc/custom-css-js.php';
require get_template_directory() . '/inc/blocks.php';
require get_template_directory() . '/inc/hero.php';
require get_template_directory() . '/inc/seo.php';

require get_template_directory() . '/inc/bogo-compat.php';

if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

/* ---------- Shortcode: getCategoryArticle ---------- */

function masapii_get_cat_items( $atts ) {
	$atts = shortcode_atts( array( 'num' => '5', 'cat' => '5' ), $atts );

	global $post;
	$old_post = $post;

	$posts = get_posts( array(
		'numberposts' => (int) $atts['num'],
		'order'       => 'DESC',
		'orderby'     => 'post_date',
		'category'    => (int) $atts['cat'],
	) );

	if ( ! $posts ) {
		return '';
	}

	$html = '<div class="mp-post-grid">';
	foreach ( $posts as $post ) {
		setup_postdata( $post );
		$html .= '<article class="mp-card">';
		if ( has_post_thumbnail() ) {
			$html .= '<div class="mp-card__image"><a href="' . esc_url( get_permalink() ) . '">' . get_the_post_thumbnail( get_the_ID(), 'masapii-card' ) . '</a></div>';
		}
		$html .= '<h3 class="mp-card__title"><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></h3>';
		$html .= '<div class="mp-card__text">' . wp_kses_post( get_the_excerpt() ) . '</div>';
		$html .= '</article>';
	}
	$html .= '</div>';

	$post = $old_post;
	wp_reset_postdata();

	return $html;
}
add_shortcode( 'getCategoryArticle', 'masapii_get_cat_items' );

/* ======================================================================
   CF7 / Plugin Asset Restriction
   ======================================================================
   Contact Form 7, Invisible reCAPTCHA, and related plugins register
   their CSS/JS globally. We remove them on every page EXCEPT those
   that actually contain a CF7 form.

   Detection: slug 'contact' OR post_content contains CF7 shortcode/block.
   On non-singular pages (home, archive, 404): always remove.
   ====================================================================== */

/**
 * Check if the current page needs Contact Form 7.
 */
function masapii_page_needs_cf7() {
	if ( ! is_singular() ) {
		return false;
	}

	$post = get_queried_object();
	if ( ! ( $post instanceof WP_Post ) ) {
		return false;
	}

	$slug    = $post->post_name;
	$content = (string) $post->post_content;

	// Page slug is 'contact'
	if ( 'contact' === $slug ) {
		return true;
	}

	// Content contains CF7 shortcode
	if ( has_shortcode( $content, 'contact-form-7' ) ) {
		return true;
	}

	// Content contains CF7 Gutenberg block
	if ( false !== strpos( $content, 'wp:contact-form-7' ) ) {
		return true;
	}

	return false;
}

/**
 * Scan ALL registered styles/scripts and remove any whose source URL
 * contains a blocked plugin folder pattern, OR whose handle name matches.
 */
function masapii_strip_plugin_assets() {
	if ( masapii_page_needs_cf7() ) {
		return;
	}

	// URL path patterns to block
	$blocked_urls = array(
		'/contact-form-7/',
		'/cf7-',
		'/cf7_',
		'/invisible-recaptcha/',
		'/invisible_recaptcha/',
		'invisible-recaptcha',
		'invisible_recaptcha',
		'/flamingo/',
		'/wpcf7-redirect/',
		'recaptcha/api',
		'recaptcha.net',
		'gstatic.com/recaptcha',
	);

	// Also block: any plugin stylesheet whose src ends with /css/style.css
	// (generic filename used by CF7 Invisible reCAPTCHA and similar plugins)
	$theme_uri = get_template_directory_uri();

	// Handle name patterns to block
	$blocked_handles = array(
		'contact-form-7',
		'cf7',
		'wpcf7',
		'invisible-recaptcha',
		'invisible_recaptcha',
		'cf7_invisible',
		'cf7-invisible',
		'cf7ic',
		'google-recaptcha',
		'flamingo',
	);

	// Strip styles
	global $wp_styles;
	if ( ! empty( $wp_styles->registered ) ) {
		foreach ( $wp_styles->registered as $handle => $dep ) {
			// Check handle name
			foreach ( $blocked_handles as $bh ) {
				if ( false !== strpos( $handle, $bh ) ) {
					wp_dequeue_style( $handle );
					wp_deregister_style( $handle );
					continue 2;
				}
			}
			// Check URL
			$src = isset( $dep->src ) ? (string) $dep->src : '';
			if ( '' === $src ) {
				continue;
			}
			// Block by URL pattern
			foreach ( $blocked_urls as $p ) {
				if ( false !== strpos( $src, $p ) ) {
					wp_dequeue_style( $handle );
					wp_deregister_style( $handle );
					continue 2;
				}
			}
			// Block: non-theme plugin CSS with generic name (e.g. /plugins/xxx/css/style.css)
			if ( false !== strpos( $src, '/plugins/' )
				&& false === strpos( $src, $theme_uri )
				&& 'masapii-style' !== $handle
				&& preg_match( '#/css/style\.css#', $src )
			) {
				wp_dequeue_style( $handle );
				wp_deregister_style( $handle );
			}
		}
	}

	// Strip scripts
	global $wp_scripts;
	if ( ! empty( $wp_scripts->registered ) ) {
		foreach ( $wp_scripts->registered as $handle => $dep ) {
			// Check handle name
			foreach ( $blocked_handles as $bh ) {
				if ( false !== strpos( $handle, $bh ) ) {
					wp_dequeue_script( $handle );
					wp_deregister_script( $handle );
					continue 2;
				}
			}
			// Check URL
			$src = isset( $dep->src ) ? (string) $dep->src : '';
			if ( '' === $src ) {
				continue;
			}
			foreach ( $blocked_urls as $p ) {
				if ( false !== strpos( $src, $p ) ) {
					wp_dequeue_script( $handle );
					wp_deregister_script( $handle );
					break;
				}
			}
		}
	}
}
add_action( 'wp_enqueue_scripts', 'masapii_strip_plugin_assets', 9999 );
add_action( 'wp_print_styles',   'masapii_strip_plugin_assets', 9999 );
add_action( 'wp_print_scripts',  'masapii_strip_plugin_assets', 9999 );

/**
 * Nuclear option: full-page output buffer to strip any remaining
 * CF7/reCAPTCHA link/script tags from the final HTML.
 * This catches plugins that bypass wp_enqueue entirely.
 */
function masapii_ob_full_page_start() {
	if ( is_admin() || masapii_page_needs_cf7() ) {
		return;
	}
	ob_start( 'masapii_ob_full_page_filter' );
}
function masapii_ob_full_page_filter( $html ) {
	// Layer 1: Remove link/script tags by plugin folder name in URL
	$html = preg_replace(
		'/<link[^>]+(contact-form-7|invisible.recaptcha|flamingo|cf7)[^>]*\/?>/i',
		'',
		$html
	);
	$html = preg_replace(
		'/<script[^>]*(recaptcha|invisible.recaptcha|cf7)[^>]*>.*?<\/script>/is',
		'',
		$html
	);

	// Layer 2: Target the specific stubborn CSS — css/style.css?ver=3.9.1
	// This is CF7 Invisible reCAPTCHA's known CSS file.
	// Match any <link> tag where href contains 'css/style.css' AND 'ver=3.9'
	// but is NOT the theme's own style.css
	$html = preg_replace(
		'/<link[^>]+href=["\'][^"\']*\/plugins\/[^"\']*css\/style\.css\?ver=[^"\']*["\'][^>]*\/?>/i',
		'',
		$html
	);

	// Layer 3: Remove any remaining Google reCAPTCHA API script
	$html = preg_replace(
		'/<script[^>]*google\.com\/recaptcha[^>]*>.*?<\/script>/is',
		'',
		$html
	);
	$html = preg_replace(
		'/<script[^>]*gstatic\.com\/recaptcha[^>]*>.*?<\/script>/is',
		'',
		$html
	);

	return $html;
}
function masapii_ob_full_page_end() {
	if ( is_admin() || masapii_page_needs_cf7() ) {
		return;
	}
	if ( ob_get_level() ) {
		ob_end_flush();
	}
}
add_action( 'template_redirect', 'masapii_ob_full_page_start', 0 );
add_action( 'shutdown', 'masapii_ob_full_page_end', 9999 );

/* ======================================================================
   jQuery Optimization
   ====================================================================== */

function masapii_dequeue_jquery() {
	if ( is_admin() || masapii_page_needs_cf7() ) {
		return;
	}
	wp_deregister_script( 'jquery' );
	wp_register_script( 'jquery', '', array(), '', true );
	wp_deregister_script( 'jquery-migrate' );
}
add_action( 'wp_enqueue_scripts', 'masapii_dequeue_jquery', 9999 );


/* ======================================================================
   Google Analytics 4 (interaction-first + delayed idle bootstrap)
   ====================================================================== */

function masapii_should_output_ga4() {
	if ( is_admin() || is_feed() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return false;
	}

	if ( is_user_logged_in() || is_preview() || is_customize_preview() ) {
		return false;
	}

	return true;
}

function masapii_output_ga4_optimized() {
	if ( ! masapii_should_output_ga4() ) {
		return;
	}
	?>
	<script id="masapii-ga4-optimized">
	(function(w, d){
	  var GA_ID = 'G-KNPJDMPMF5';
	  var FALLBACK_DELAY_MS = 6000;
	  var RETURNING_DELAY_MS = 1200;
	  var started = false;
	  var configured = false;
	  var pageviewSent = false;
	  var hintsAdded = false;
	  var interactionListenerOptions = { passive: true };
	  var interactionEvents = ['pointerdown', 'keydown', 'touchstart', 'mousedown', 'scroll'];

	  w.dataLayer = w.dataLayer || [];
	  w.gtag = w.gtag || function(){ w.dataLayer.push(arguments); };

	  function addHint(rel, href, crossOrigin) {
	    try {
	      if (d.querySelector('link[rel="' + rel + '"][href="' + href + '"]')) {
	        return;
	      }
	      var link = d.createElement('link');
	      link.rel = rel;
	      link.href = href;
	      if (crossOrigin) {
	        link.crossOrigin = 'anonymous';
	      }
	      d.head.appendChild(link);
	    } catch (e) {
	      /* no-op */
	    }
	  }

	  function warmConnections() {
	    if (hintsAdded) {
	      return;
	    }
	    hintsAdded = true;
	    addHint('dns-prefetch', '//www.googletagmanager.com');
	    addHint('dns-prefetch', '//www.google-analytics.com');
	    addHint('preconnect', 'https://www.googletagmanager.com', true);
	    addHint('preconnect', 'https://www.google-analytics.com', true);
	  }

	  function configTag() {
	    if (configured) {
	      return;
	    }
	    configured = true;
	    w.gtag('js', new Date());
	    w.gtag('config', GA_ID, {
	      send_page_view: false
	    });
	  }

	  function sendPageView() {
	    if (pageviewSent || ! configured) {
	      return;
	    }
	    if (d.visibilityState && d.visibilityState === 'prerender') {
	      return;
	    }
	    pageviewSent = true;
	    try {
	      w.sessionStorage.setItem('masapii_ga4_engaged', '1');
	    } catch (e) {
	      /* no-op */
	    }
	    w.gtag('event', 'page_view', {
	      page_title: d.title || '',
	      page_location: w.location.href,
	      page_path: w.location.pathname + w.location.search + w.location.hash
	    });
	  }

	  function sendPageViewWhenVisible() {
	    if (d.visibilityState === 'hidden') {
	      var onVisible = function() {
	        if (d.visibilityState !== 'hidden') {
	          d.removeEventListener('visibilitychange', onVisible);
	          sendPageView();
	        }
	      };
	      d.addEventListener('visibilitychange', onVisible);
	      return;
	    }
	    sendPageView();
	  }

	  function markEngagedAndLoad() {
	    removeInteractionListeners();
	    try {
	      w.sessionStorage.setItem('masapii_ga4_engaged', '1');
	    } catch (e) {
	      /* no-op */
	    }
	    bootAnalytics();
	  }

	  function bootAnalytics() {
	    if (started) {
	      return;
	    }
	    started = true;
	    warmConnections();

	    var script = d.createElement('script');
	    script.async = true;
	    script.src = 'https://www.googletagmanager.com/gtag/js?id=' + encodeURIComponent(GA_ID);
	    script.onload = function() {
	      configTag();
	      sendPageViewWhenVisible();
	    };
	    script.onerror = function() {
	      started = false;
	      configured = false;
	    };
	    d.head.appendChild(script);
	  }

	  function bootWhenBrowserIsIdle() {
	    if ('requestIdleCallback' in w) {
	      w.requestIdleCallback(function() {
	        bootAnalytics();
	      }, { timeout: 2500 });
	      return;
	    }
	    w.setTimeout(bootAnalytics, 1);
	  }

	  function removeInteractionListeners() {
	    interactionEvents.forEach(function(eventName) {
	      w.removeEventListener(eventName, markEngagedAndLoad, interactionListenerOptions);
	    });
	  }

	  function addInteractionListeners() {
	    interactionEvents.forEach(function(eventName) {
	      w.addEventListener(eventName, markEngagedAndLoad, interactionListenerOptions);
	    });
	  }

	  addInteractionListeners();

	  var fallbackDelay = RETURNING_DELAY_MS;
	  try {
	    if (! w.sessionStorage.getItem('masapii_ga4_engaged')) {
	      fallbackDelay = FALLBACK_DELAY_MS;
	    }
	  } catch (e) {
	    fallbackDelay = FALLBACK_DELAY_MS;
	  }

	  function scheduleFallbackBoot() {
	    w.setTimeout(function() {
	      removeInteractionListeners();
	      bootWhenBrowserIsIdle();
	    }, fallbackDelay);
	  }

	  if (d.readyState === 'complete') {
	    scheduleFallbackBoot();
	    return;
	  }

	  w.addEventListener('load', function() {
	    scheduleFallbackBoot();
	  }, { once: true });
	})(window, document);
	</script>
	<?php
}
add_action( 'wp_footer', 'masapii_output_ga4_optimized', 100 );


/**
 * reCAPTCHA等で生成された特殊な送信ボタンのスタイルを強制適用
 */
add_action('wp_footer', function() {
    $current_path = $_SERVER['REQUEST_URI'];

    // 判定：スラッグが 'contact' または URLに '/en/contact/' が含まれる場合
    if ( is_page('contact') || strpos($current_path, '/en/contact/') !== false ) {
        ?>
        <script>
        window.addEventListener('load', function() {
            function applyCustomButtonStyle() {
                // 1. 元のボタン（.s-button）と 2. プラグインが生成したボタン（.recaptcha-btn または 特定ID）の両方を取得
                const buttons = document.querySelectorAll('.s-button, .recaptcha-btn, #wpcf-custom-btn-0');
                
                buttons.forEach(function(btn) {
                    // もしプラグインによって非表示にされていたら強制的に表示（元のボタンが消されている場合があるため）
                    if (btn.id === 'wpcf-custom-btn-0' || btn.classList.contains('recaptcha-btn')) {
                        btn.style.setProperty('display', 'block', 'important');
                    }

                    const s = btn.style;
                    // 強烈なデザイン上書き
                    s.setProperty('background', 'linear-gradient(45deg, #d43491, #4834d4)', 'important');
                    s.setProperty('border', 'none', 'important');
                    s.setProperty('border-radius', '50px', 'important');
                    s.setProperty('color', '#ffffff', 'important');
                    s.setProperty('padding', '20px 40px', 'important');
                    s.setProperty('font-weight', 'bold', 'important');
                    s.setProperty('font-size', '1.25rem', 'important');
                    s.setProperty('width', '100%', 'important');
                    s.setProperty('max-width', '600px', 'important');
                    s.setProperty('margin', '40px auto 0', 'important');
                    s.setProperty('box-shadow', '0 8px 20px rgba(212, 52, 145, 0.3)', 'important');
                    s.setProperty('transition', 'all 0.4s ease', 'important');
                    s.setProperty('cursor', 'pointer', 'important');
                    s.setProperty('height', 'auto', 'important');
                    s.setProperty('line-height', '1.2', 'important');
                    s.setProperty('text-align', 'center', 'important');
                    s.setProperty('appearance', 'none', 'important');
                    s.setProperty('-webkit-appearance', 'none', 'important');
                });
            }

            // 実行
            applyCustomButtonStyle();

            // CF7のイベント時にも実行
            document.addEventListener('wpcf7submit', function() {
                setTimeout(applyCustomButtonStyle, 50);
            }, false);
            
            // reCAPTCHAプラグインが後からボタンを生成する場合に備え、少し遅れてもう一度実行
            setTimeout(applyCustomButtonStyle, 1000);
        });
        </script>
        <?php
    }
}, 999);

// タイトル調整
function custom_document_title( $title ) {
    if ( is_singular() ) {
        $title['tagline'] = '';
        $title['site']    = '';
    }
    return $title;
}
add_filter( 'document_title_parts', 'custom_document_title' );
