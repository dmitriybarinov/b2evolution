<?php
	/**
	 * This template generates an RSS 2.0 feed for the requested blog
	 *
	 * See {@link http://backend.userland.com/rss}
	 *
	 * b2evolution - {@link http://b2evolution.net/}
	 * Released under GNU GPL License - {@link http://b2evolution.net/about/license.html}
	 * @copyright (c)2003-2004 by Francois PLANQUE - {@link http://fplanque.net/}
	 *
	 * @package xmlsrv
	 */
	$skin = '';										// We don't want this do be displayed in a skin !
	$show_statuses = array();			// Restrict to published posts
	$timestamp_min = '';					// Show past
	$timestamp_max = 'now';				// Hide future
	/**
	 * Initialize everything:
	 */
	require dirname(__FILE__).'/../evocore/_blog_main.inc.php' ;
	header("Content-type: application/xml");
	echo "<?xml version=\"1.0\"?".">";
?>
<!-- generator="<?php echo $app_name ?>/<?php echo $app_version ?>" -->
<rss version="2.0" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:admin="http://webns.net/mvcb/" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns:content="http://purl.org/rss/1.0/modules/content/">
	<channel>
		<title><?php
			$Blog->disp( 'name', 'xml' );
			single_cat_title( ' - ', 'xml' );
			single_month_title( ' - ', 'xml' );
			single_post_title( ' - ', 'xml' );
		?></title>
		<link><?php $Blog->disp( 'blogurl', 'xml' ) ?></link>
		<description><?php $Blog->disp( 'shortdesc', 'xml' ) ?></description>
		<language><?php $Blog->disp( 'locale', 'xml' ) ?></language>
		<docs>http://backend.userland.com/rss</docs>
		<admin:generatorAgent rdf:resource="http://b2evolution.net/?v=<?php echo $app_version ?>"/>
		<ttl>60</ttl>
		<?php while( $Item = $MainList->get_item() ) {	?>
		<item>
			<title><?php $Item->title( '', '', false, 'xml' ) ?></title>
			<link><?php $Item->permalink( 'single' ) ?></link>
			<pubDate><?php $Item->issue_date( 'r', true ) ?></pubDate>
			<?php /* Disabled because of spambots: <author><php the_author_email( 'xml' ) ></author> */ ?>
			<?php $Item->categories( false, '<category domain="main">', '</category>', '<category domain="alt">', '</category>', '<category domain="external">', '</category>', "\n", 'xml' ) ?>
			<guid isPermaLink="false"><?php the_ID() ?>@<?php echo $baseurl ?></guid>
			<description><?php
				$Item->url_link( '', ' ', 'xml' );
				$Item->content( 1, false, T_('[...] Read more!'), '', '', '', 'xml', $rss_excerpt_length );
			?></description>
			<content:encoded><![CDATA[<?php
				$Item->url_link( '<p>', '</p>' );
				$Item->content()
			?>]]></content:encoded>
			<comments><?php comments_link( '', 1, 1 ) ?></comments>
		</item>
		<?php } ?>
	</channel>
</rss>
<?php log_hit(); // log the hit on this page ?>