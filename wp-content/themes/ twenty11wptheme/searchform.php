<?php
/**
 * Template for displaying search forms in Twenty 11 WP Theme
 *
 * @package WordPress
 * @subpackage Twenty11WPTheme
 * @since Twenty 11 WP Theme 1.0
 */
?>
	<form method="get" id="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">
		<label for="s" class="assistive-text"><?php _e( 'Search', 'twenty11wptheme' ); ?></label>
		<input type="text" class="field" name="s" id="s" placeholder="<?php esc_attr_e( 'Search', 'twenty11wptheme' ); ?>" />
		<input type="submit" class="submit" name="submit" id="searchsubmit" value="<?php esc_attr_e( 'Search', 'twenty11wptheme' ); ?>" />
	</form>
