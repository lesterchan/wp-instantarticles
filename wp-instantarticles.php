<?php
/*
Plugin Name: WP-InstantArticles
Plugin URI: https://lesterchan.net/portfolio/programming/php/
Description: WP-InstantArticles generates a RSS feed of your WordPress posts as Instant Articles for Facebook to consume. You can access your Instant Articles RSS feed at <a href="http://yoursite.com/instant-articles">http://yoursite.com/instant-articles</a>. Check out Facebook's developer guide on <a href="https://developers.facebook.com/docs/instant-articles/publishing">Publishing Instant Articles</a> for more information.
Version: 1.0.0
Author: Lester 'GaMerZ' Chan
Author URI: https://lesterchan.net
Text Domain: wp-instantarticles
License: GPL2
*/

/*  Copyright 2016  Lester Chan  (email : lesterchan@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * WP-InstantArticles plugin name
 *
 * @since 1.0.0
 */
define( 'WP_INSTANTARTICLES_PLUGIN_NAME', 'wp-instantarticles' );

/**
 * WP-InstantArticles nice name
 *
 * @since 1.0.0
 */
define( 'WP_INSTANTARTICLES_NICE_NAME', 'wp_instantarticles' );

/**
 * WP-InstantArticles version
 *
 * @since 1.0.0
 */
define( 'WP_INSTANTARTICLES_VERSION', '1.0.0' );

/**
 * WP-InstantArticles Class
 *
 * @since 1.0.0
 */
class WPInstantArticles {
	/**
	 * Namespace. Defaults to instant-articles.
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 * @var $namespace
	 */
	private $namespace = 'instant-articles';

	/**
	 * Static instance
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 * @var $instance
	 */
	private static $instance;

	/**
	 * Constructor method
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 */
	public function __construct() {
		// Add Plugin Hooks
		add_action( 'plugins_loaded', array( $this, 'add_hooks' ) );

		// Load Translation
		load_plugin_textdomain( WP_INSTANTARTICLES_PLUGIN_NAME );

		// Plugin Activation/Deactivation
		register_activation_hook( __FILE__, array( $this, 'plugin_activation' ) );
		register_deactivation_hook( __FILE__, array( $this, 'plugin_deactivation' ) );
	}

	/**
	 * Initializes the plugin object and returns its instance
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @return object The plugin object instance
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Adds all the plugin hooks
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @return void
	 */
	public function add_hooks() {
		// Actions
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'after_setup_theme', array( $this, 'after_setup_theme' ) );

		// Filters
		add_filter( WP_INSTANTARTICLES_NICE_NAME . '_post_content', array( $this, 'format_post_content' ), 99 );
	}

	/**
	 * Init this plugin
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @return void
	 */
	public function init() {
		$this->namespace = apply_filters( WP_INSTANTARTICLES_NICE_NAME . '_namespace', 'instant-articles' );
		add_feed( $this->namespace, array( $this, 'render_instant_articles' ) );
	}

	/**
	 * After setup theme
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @return void
	 */
	public function after_setup_theme() {
		if( ! $this->in_instant_articles_feed() ) {
			return;
		}
		add_theme_support( 'html5', [ 'caption' ] );
	}

	/**
	 * Render Instant Articles
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @return void
	 */
	public function render_instant_articles() {
		header( 'Content-Type: ' . feed_content_type( 'rss2' ) . '; charset=' . get_option( 'blog_charset' ), true );
		echo '<?xml version="1.0" encoding="' . get_option( 'blog_charset' ) . '"?' . '>';

		$template_rss2_name = apply_filters( WP_INSTANTARTICLES_NICE_NAME . '_template_rss2', 'instantarticles-rss2.php' );
		if ( $template = locate_template( $template_rss2_name ) ) {
			load_template( $template );
		} else {
			load_template( dirname( __FILE__ ) . '/templates/instantarticles-rss2.php' );
		}
	}

	/**
	 * Render Instant Articles WordPress Loop
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @return void
	 */
	public function wp_loop() {
		$template_rss2_items_name = apply_filters( WP_INSTANTARTICLES_NICE_NAME . '_template_rss2_items', 'instantarticles-rss2-items.php' );
		if ( $template = locate_template( $template_rss2_items_name ) ) {
			load_template( $template );
		} else {
			load_template( dirname( __FILE__ ) . '/templates/instantarticles-rss2-items.php' );
		}
	}

	/**
	 * Is it in Instant Articles feed?
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @return bool
	 */
	public function in_instant_articles_feed() {
		$is_feed = is_feed( $this->namespace );
		if( ! $is_feed ) {
			// We check the URL because of early init is_feed() might not be available
			$is_feed = ( strpos( $_SERVER['REQUEST_URI'], '/' . $this->namespace ) !== false );
		}

		return $is_feed;
	}

	/**
	 * Get the article style
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @return string
	 */
	public function get_article_style() {
		return apply_filters( WP_INSTANTARTICLES_NICE_NAME . '_article_style', wp_get_theme() );
	}

	/**
	 * Get the first image URL from a given content
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @param string $content
	 * @return string
	 */
	public function get_post_first_image_url( $content ) {
		$url = '';

		preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $content, $matches, PREG_SET_ORDER );
		if( ! empty( $matches[0][1] ) ) {
			$url = $matches[0][1];
		}

		return $url;
	}

	/**
	 * Get the post's attachment alt
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @param int $attachment_id
	 * @return string
	 */
	public function get_post_attachment_alt( $attachment_id ) {
		$alt = trim( strip_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) );
		if( empty( $alt ) ) {
			$attachment = get_post( $attachment_id );
			$alt = trim( strip_tags( $attachment->post_excerpt ) );
			if( empty( $alt ) ) {
				$alt = trim(strip_tags( $attachment->post_title ) );
			}
		}

		return $alt;
	}

	/**
	 * Get the post categories in a list
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 * @param array $categories
	 * @param string $seperator
	 * @return string
	 */
	public function get_post_categories( $categories, $seperator = ', ' ) {
		$cat = array();
		if( count( $categories ) > 0 ) {
			foreach( $categories as $category ) {
				$cat[] = $category->name;
			}
		}

		return trim( implode( $cat, $seperator ) );
	}

	/**
	 * Format the post content.
	 *
	 * 1. Props to @bueltge for Replacement of <img> to <figure>.
	 * Source: http://wordpress.stackexchange.com/questions/174582/always-use-figure-for-post-images
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @param string $content
	 * @return string
	 */
	public function format_post_content( $content ) {
		$content = preg_replace(
			'/<p>\\s*?(<a.*?><img.*?><\\/a>|<img.*?>)?\\s*<\\/p>/s',
			'<figure>$1</figure>',
			$content
		);

		return $content;
	}

	/**
	 * Get credits for the post
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @return string
	 */
	public function get_credits() {
		global $post;

		$credits    = sprintf(
			__( 'This post <a href="%s">%s</a> appeared first on %s.', WP_INSTANTARTICLES_PLUGIN_NAME ),
			get_permalink( $post),
			get_the_title( $post ),
			get_bloginfo( 'name' )
		);

		return apply_filters( WP_INSTANTARTICLES_NICE_NAME . '_post_credits', $credits );
	}

	/**
	 * Get copyright for the post
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @return string
	 */
	public function get_copyright() {
		$copyright  = sprintf(
			__( 'Copyright &copy; %d %s. All rights reserved.', WP_INSTANTARTICLES_PLUGIN_NAME ),
			date( 'Y' ),
			get_bloginfo( 'name' )
		);

		return apply_filters( WP_INSTANTARTICLES_NICE_NAME . '_post_copyright', $copyright );
	}

	/**
	 * What to do when the plugin is being deactivated
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @param boolean $network_wide
	 * @return void
	 */
	public function plugin_activation( $network_wide ) {
		if ( is_multisite() && $network_wide ) {
			$ms_sites = wp_get_sites();

			if( 0 < sizeof( $ms_sites ) ) {
				foreach ( $ms_sites as $ms_site ) {
					switch_to_blog( $ms_site['blog_id'] );
					$this->plugin_activated();
					restore_current_blog();
				}
			}
		} else {
			$this->plugin_activated();
		}
	}

	/**
	 * Perform plugin activation tasks
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 * @return void
	 */
	private function plugin_activated() {
		flush_rewrite_rules();
	}

	/**
	 * What to do when the plugin is being activated
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @param boolean $network_wide
	 * @return void
	 */
	public function plugin_deactivation( $network_wide ) {
		if ( is_multisite() && $network_wide ) {
			$ms_sites = wp_get_sites();

			if( 0 < sizeof( $ms_sites ) ) {
				foreach ( $ms_sites as $ms_site ) {
					switch_to_blog( $ms_site['blog_id'] );
					$this->plugin_deactivated();
					restore_current_blog();
				}
			}
		} else {
			$this->plugin_deactivated();
		}
	}

	/**
	 * Perform plugin deactivation tasks
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 * @return void
	 */
	private function plugin_deactivated() {}
}

/**
 * Init WP-InstantArticles
 */
WPInstantArticles::get_instance();
