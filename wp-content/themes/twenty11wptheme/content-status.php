<?php
/**
 * Template for displaying posts in the Status Post Format
 *
 * Used on index and archive pages
 *
 * @link http://codex.wordpress.org/Post_Formats
 *
 * @package WordPress
 * @subpackage Twenty11WPTheme
 * @since Twenty 11 WP Theme 1.0
 */
?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<header class="entry-header">
			<hgroup>
				<h2 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
				<h3 class="entry-format"><?php _e( 'Status', 'twenty11wptheme' ); ?></h3>
			</hgroup>

			<?php if ( comments_open() && ! post_password_required() ) : ?>
			<div class="comments-link">
				<?php comments_popup_link( '<span class="leave-reply">' . __( 'Reply', 'twenty11wptheme' ) . '</span>', _x( '1', 'comments number', 'twenty11wptheme' ), _x( '%', 'comments number', 'twenty11wptheme' ) ); ?>
			</div>
			<?php endif; ?>
		</header><!-- .entry-header -->

		<?php if ( is_search() ) : // Only display Excerpts for Search ?>
		<div class="entry-summary">
			<?php the_excerpt(); ?>
		</div><!-- .entry-summary -->
		<?php else : ?>
		<div class="entry-content">
			<div class="avatar">
				<?php
				/**
				 * Filter the Twenty 11 WP Theme status avatar size.
				 *
				 * @since Twenty 11 WP Theme 1.0
				 *
				 * @param int The height and width avatar dimensions in pixels. Default 65.
				 */
				echo get_avatar( get_the_author_meta( 'ID' ), apply_filters( 'twenty11wptheme_status_avatar', 65 ) );
				?>
			</div>

			<?php the_content( __( 'Continue reading <span class="meta-nav">&rarr;</span>', 'twenty11wptheme' ) ); ?>
			<?php wp_link_pages( array( 'before' => '<div class="page-link"><span>' . __( 'Pages:', 'twenty11wptheme' ) . '</span>', 'after' => '</div>' ) ); ?>
		</div><!-- .entry-content -->
		<?php endif; ?>

		<footer class="entry-meta">
			<?php twenty11wptheme_posted_on(); ?>
			<?php if ( comments_open() ) : ?>
			<span class="sep"> | </span>
			<span class="comments-link"><?php comments_popup_link( '<span class="leave-reply">' . __( 'Leave a reply', 'twenty11wptheme' ) . '</span>', __( '<b>1</b> Reply', 'twenty11wptheme' ), __( '<b>%</b> Replies', 'twenty11wptheme' ) ); ?></span>
			<?php endif; ?>
			<?php edit_post_link( __( 'Edit', 'twenty11wptheme' ), '<span class="edit-link">', '</span>' ); ?>
		</footer><!-- .entry-meta -->
	</article><!-- #post-<?php the_ID(); ?> -->
