# WP-InstantArticles
Contributors: GamerZ  
Donate link: https://lesterchan.net/site/donation/  
Tags: facebook, instant articles, instant, article, quick, RSS, feed  
Requires at least: 4.4  
Tested up to: 6.3  
Stable tag: trunk  
License: GPLv2 or later  
License URI: http://www.gnu.org/licenses/gpl-2.0.html  

WP-InstantArticles generates a RSS feed of your WordPress posts as Instant Articles for Facebook to consume.

## Description
Facebook Instant Articles is a new way for publishers to distribute stories on Facebook. Instant Articles load up to 10 times faster than the mobile web.

You can access your Instant Articles RSS feed at `http://yoursite.com/instant-articles`.

For more information checkout [Facebook's Instant Articles Developer Guide](https://developers.facebook.com/docs/instant-articles).

### Build Status
[![Build Status](https://travis-ci.org/lesterchan/wp-instantarticles.svg?branch=master)](https://travis-ci.org/lesterchan/wp-instantarticles)

### Development
* [https://github.com/lesterchan/wp-instantarticles](https://github.com/lesterchan/wp-instantarticles "https://github.com/lesterchan/wp-instantarticles")

### Credits
* Plugin icon by [Freepik](http://www.freepik.com) from [Flaticon](http://www.flaticon.com)

### Donations
I spent most of my free time creating, updating, maintaining and supporting these plugins, if you really love my plugins and could spare me a couple of bucks, I will really appreciate it. If not feel free to use it without any obligations.

## Changelog
### 1.0.0
* Initial release

## Installation
1. Upload `wp-instantarticles` folder to the `/wp-content/plugins/` directory
2. Activate the `WP-InstantArticles` plugin through the 'Plugins' menu in WordPress
3. There are no options for the plugin. You can access the Instant Articles RSS feed at `http://yoursite.com/instant-articles`.

## Screenshots
N/A

## Frequently Asked Questions
### 404 when accessing `http://yoursite.com/instant-articles`.
* You might need to re-generate permalink (WP-Admin -> Settings -> Permalinks -> Save Changes)

### Support for Custom Post Types?
* You can access your Custom Post Type Instant Articles feed via `http://yoursite.com/instant-articles/?post_type=CPT`.

### What are the filters available?
* wp_instantarticles_namespace
 * Default: instant-articles
* wp_instantarticles_template_rss2
 * Default: instantarticles-rss2.php
* wp_instantarticles_template_rss2_items
 * Default: instantarticles-rss2-items.php
* wp_instantarticles_article_style
 * Default: wp_get_theme() which returns your theme folder name.
* wp_instantarticles_post_credits
 * Default: This post POST_TITLE appeared first on SITE_NAME.
* wp_instantarticles_post_copyright
 * Default: Copyright &copy; YEAR SITE_NAME. All rights reserved.
* wp_instantarticles_post_image
 * Default: Featured image URL or first image URL in post.
* wp_instantarticles_post_image_alt
 * Default: Featured image alt, excerpt or  title.
* wp_instantarticles_post_content
 * Default: Post content.

### What are the hooks available?
* wp_instantarticles_rss2_head
 * Fires: Within `<channel></channel>`, before first `<item>`.
* wp_instantarticles_rss2_item
 * Fires: After `</content:encoded>`, before `</item>`.
* wp_instantarticles_post_header
 * Fires: Before `</header>`.
* wp_instantarticles_post_content
 * Fires: After post content, before `</footer>`.
* wp_instantarticles_post_footer
 * Fires: Immediately after `<footer>`.

## Upgrade Notice
N/A
