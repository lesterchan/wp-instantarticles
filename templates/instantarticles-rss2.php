<rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/">
	<channel>
		<title><?php wp_title_rss(); ?></title>
		<link><?php bloginfo_rss( 'url' ) ?></link>
		<description><?php bloginfo_rss( 'description') ?></description>
		<language><?php bloginfo_rss( 'language' ); ?></language>
		<lastBuildDate><?php echo mysql2date( 'c', get_lastpostmodified( 'GMT' ), false ); ?></lastBuildDate>
		<?php do_action( WP_INSTANTARTICLES_NICE_NAME . '_rss2_head' ); ?>
		<?php WPInstantArticles::get_instance()->wp_loop(); ?>
	</channel>
</rss>
