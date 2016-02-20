<?php
	if( have_posts() ):
		while ( have_posts() ):
			the_post();
			$post_time_c    = get_post_time( 'c', true );
			$post_image     = wp_get_attachment_url( get_post_thumbnail_id() );
			$post_image_alt = '';
			if( empty( $post_image ) ) {
				$post_image = WPInstantArticles::get_instance()->get_post_first_image_url( get_the_content() );
			} else {
				$post_image_alt = WPInstantArticles::get_instance()->get_post_attachment_alt( get_post_thumbnail_id() );
			}
			$post_image     = apply_filters( WP_INSTANTARTICLES_NICE_NAME . '_post_image', $post_image );
			$post_image_alt = apply_filters( WP_INSTANTARTICLES_NICE_NAME . '_post_image_alt', $post_image_alt );

			$rss_excerpt        = apply_filters( 'the_excerpt_rss', get_the_excerpt() );
			$rss_description    = empty( $rss_excerpt ) ? wp_trim_words( get_the_content_feed() ) : $rss_excerpt;

			$author_id          = get_the_author_meta( 'ID' );
			$author_nicename    = get_the_author_meta( 'user_nicename' );
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
						<meta property="fb:article_style" content="<?php echo WPInstantArticles::get_instance()->get_article_style(); ?>">
						<link rel="canonical" href="<?php the_permalink(); ?>">
					</head>
					<body>
					<article>
						<header>
							<?php if( ! empty( $post_image ) ): ?>
								<figure>
									<img src="<?php echo $post_image; ?>" />
									<?php if( ! empty( $post_image_alt ) ): ?>
										<figcaption><?php echo $post_image_alt; ?></figcaption>
									<?php endif; ?>
								</figure>
							<?php endif; ?>
							<h1><?php the_title(); ?></h1>
							<?php if( has_excerpt() ): ?>
								<h2><?php the_excerpt(); ?></h2>
							<?php endif; ?>
							<h3 class="op-kicker"><?php echo WPInstantArticles::get_instance()->get_post_categories( get_the_category() ); ?></h3>
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
							<?php do_action( WP_INSTANTARTICLES_NICE_NAME . '_post_header' ); ?>
						</header>

						<?php echo apply_filters( WP_INSTANTARTICLES_NICE_NAME . '_post_content', apply_filters( 'the_content', get_the_content( '' ) ) ); ?>

						<?php do_action( WP_INSTANTARTICLES_NICE_NAME . '_post_content' ); ?>
						<footer>
							<?php do_action( WP_INSTANTARTICLES_NICE_NAME . '_post_footer' ); ?>
							<aside>
								<p><?php echo WPInstantArticles::get_instance()->get_credits(); ?></p>
							</aside>
							<small><?php echo WPInstantArticles::get_instance()->get_copyright(); ?></small>
						</footer>
					</article>
					</body>
					</html>
					]]>
				</content:encoded>
				<?php do_action( WP_INSTANTARTICLES_NICE_NAME . '_rss2_item' ); ?>
			</item>
	<?php endwhile; ?>
<?php endif; ?>
