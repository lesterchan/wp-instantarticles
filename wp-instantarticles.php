<?php
/*
Plugin Name: WP-InstantArticles
Plugin URI: http://lesterchan.net/portfolio/programming/php/
Description: WP-InstantArticles generates a RSS feed of your WordPress posts as Instant Articles for Facebook to consume. You can access your Instant Articles RSS feed at <a href="http://yoursite.com/instant-articles">http://yoursite.com/instant-articles</a>. Check out Facebook's developer guide on <a href="https://developers.facebook.com/docs/instant-articles/publishing">Publishing Instant Articles</a> for more information.
Version: 1.0.0
Author: Lester 'GaMerZ' Chan
Author URI: http://lesterchan.net
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
 * WP-InstantArticles name
 *
 * @since 1.0.0
 */
define( 'WP_INSTANTARTICLES_NAME', 'wp-instantarticles' );

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
		load_plugin_textdomain( 'wp-sweep' );

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
		$instant_articles_ns = apply_filters( WP_INSTANTARTICLES_NAME . '_namespace', 'instant-articles' );
		add_feed( $instant_articles_ns, array( $this, 'render_instant_articles' ) );
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
		?>
		<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/">
			<channel>
				<title><?php wp_title_rss(); ?></title>
				<link><?php bloginfo_rss( 'url' ) ?></link>
				<description><?php bloginfo_rss("description") ?></description>
				<language><?php bloginfo_rss( 'language' ); ?></language>
				<lastBuildDate><?php echo mysql2date( 'c', get_lastpostmodified( 'GMT' ), false ); ?></lastBuildDate>
				<?php do_action( WP_INSTANTARTICLES_NAME . '_rss2_head' ); ?>
				<?php $this->render_wp_loop(); ?>
			</channel>
		</rss>
		<?php
	}

	/**
	 * Render Instant Articles WordPress Loop
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @return void
	 */
	public function render_wp_loop() {
		$site_name = get_bloginfo( 'name' );
		$theme_name = apply_filters( WP_INSTANTARTICLES_NAME . '_article_style', wp_get_theme() );

		while( have_posts() ):
			the_post();
			$post_time_c    = get_post_time( 'c', true );
			$post_image     = wp_get_attachment_url( get_post_thumbnail_id() );
			if( empty( $post_image ) ) {
				$post_image = $this->get_first_image_url( get_the_content() );
			}
			$post_image = apply_filters( WP_INSTANTARTICLES_NAME . '_post_image', $post_image );

			$rss_excerpt        = apply_filters( 'the_excerpt_rss', get_the_excerpt() );
			$rss_description    = empty( $rss_excerpt ) ? wp_trim_words( get_the_content_feed() ) : $rss_excerpt;

			$author_id          = get_the_author_meta( 'ID' );
			$author_nicename    = get_the_author( 'user_nicename' );
			$author_facebook    = get_the_author_meta( 'facebook' );
			$author_description = get_the_author_meta( 'user_description' );
?>
			<item>
				<title><?php the_title_rss(); ?></title>
				<link><?php the_permalink_rss(); ?></link>
				<pubDate><?php echo $post_time_c; ?></pubDate>
				<author><?php the_author(); ?></author>
				<guid><?php echo md5( get_the_guid() ); ?></guid>
				<description><?php echo $rss_description; ?></description>
				<content:encoded>
					<![CDATA[
					<!doctype html>
					<html lang="en" prefix="op: http://media.facebook.com/op#">
					<head>
						<meta charset="utf-8">
						<meta property="op:markup_version" content="v1.0">
						<meta property="fb:article_style" content="<?php echo $theme_name; ?>">
						<link rel="canonical" href="<?php the_permalink(); ?>">
					</head>
					<body>
					<article>
						<header>
							<?php if( ! empty( $post_image ) ): ?>
								<figure>
									<img src="<?php echo $post_image; ?>" />
								</figure>
							<?php endif; ?>
							<h1><?php the_title(); ?></h1>
							<?php if( has_excerpt() ): ?>
								<h3 class="op-kicker"><?php the_excerpt(); ?></h3>
							<?php endif; ?>
							<time class="op-published" dateTime="<?php echo $post_time_c; ?>"><?php the_time( 'g:i A \o\n M j, Y' ); ?></time>
							<time class="op-modified" dateTime="<?php echo get_post_modified_time( 'c', true ); ?>"><?php the_modified_time( 'g:i A \o\n M j, Y' ); ?></time>
							<address>
								<?php if( ! empty( $author_facebook ) ): ?>
									<a rel="facebook" href="<?php echo $author_facebook; ?>"><?php the_author(); ?></a>
								<?php else: ?>
									<a href="<?php echo get_author_posts_url( $author_id, $author_nicename ); ?>"><?php the_author(); ?></a>
								<?php endif; ?>
								<?php if( ! empty( $author_description ) ): ?>
									<?php echo $author_description; ?>
								<?php endif; ?>
							</address>
						</header>
						<?php the_content(); ?>
						<footer>
							<aside>
								<p>This post <a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a> appeared first on <?php echo $site_name; ?>.</p>
							</aside>
							<small>Copyright &copy; <?php echo date( 'Y' ); ?> <?php echo $site_name; ?>. All rights reserved.</small>
						</footer>
					</article>
					</body>
					</html>
					]]>
				</content:encoded>
				<?php do_action( WP_INSTANTARTICLES_NAME . '_rss2_item' ); ?>
			</item>
<?php
		endwhile;
	}

	/**
	 * Get the first image URL from the content
	 *
	 * @since 1.0.0
	 *
	 * @access private
	 * @param $content
	 * @return string
	 */
	private function get_first_image_url( $content ) {
		$url = '';

		preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $content, $matches, PREG_SET_ORDER );
		if( ! empty( $matches[0][1] ) ) {
			$url = $matches[0][1];
		}

		return $url;
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
