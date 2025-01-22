<?php
/**
 * Headless_CMS features custom functions.
 *
 * @package headless-cms
 */

/**
 * An extension to get_template_part function to allow variables to be passed to the template.
 *
 * @param  string $slug file slug like you use in get_template_part without php extension.
 * @param  array  $variables pass an array of variables you want to use in array keys.
 *
 * @return void
 */

add_filter( 'graphql_jwt_auth_secret_key', function() {
	$plugin_options = get_option( 'hcms_plugin_options' );
	if ( ! is_array($plugin_options) && empty( $plugin_options['jwt_secret'] ) ) {
		return '';
	}
	
	return $plugin_options['jwt_secret'];
});
