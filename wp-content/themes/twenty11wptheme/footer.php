<?php
/**
 * Template for displaying the footer
 *
 * Contains the closing of the id=main div and all content after
 *
 * @package WordPress
 * @subpackage Twenty11WPTheme
 * @since Twenty 11 WP Theme 1.0
 */
?>

	</div><!-- #main -->

	<footer id="colophon" role="contentinfo">

			<?php
				/*
				 * A sidebar in the footer? Yep. You can can customize
				 * your footer with three columns of widgets.
				 */
				if ( ! is_404() )
					get_sidebar( 'footer' );
			?>

			<div id="site-generator">
				<?php do_action( 'twenty11wptheme_credits' ); ?>
				<a href="<?php echo esc_url( __( 'http://wordpress.org/', 'twenty11wptheme' ) ); ?>" title="<?php esc_attr_e( 'Semantic Personal Publishing Platform', 'twenty11wptheme' ); ?>"><?php printf( __( 'Proudly powered by %s', 'twenty11wptheme' ), 'WordPress' ); ?></a>
			</div>
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>