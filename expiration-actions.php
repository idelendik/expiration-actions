<?php

/**
 * Expiration Actions
 *
 * Plugin Name:       Expiration Actions
 * Plugin URI:        https://github.com/idelendik/expiration-actions
 * Description:       A WordPress plugin to specify expiration datetime and action for a post type
 * Version:           1.0.0
 * Requires at least: 6.7
 * Requires PHP:      7.4
 * Author:            Igor Delendik
 * Author URI:        https://github.com/idelendik
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://github.com/idelendik/expiration-actions
 * Text Domain:       expiration-actions
 * Domain Path:       languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BASE_PATH', plugin_dir_path( __FILE__ ) ); // /var/www/...
define( 'BASE_URL', plugin_dir_url( __FILE__ ) );   // http://localhost/wp-content/plugins/...

final class ExpirationActions {
	private static ?self $instance = null;

	public static function init(): self {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
		add_action( 'plugins_loaded', function () {
			load_plugin_textdomain(
				'expiration-actions',
				false,
				dirname( plugin_basename( __FILE__ ) ) . '/languages/'
			);
		} );

		require_once __DIR__ . '/classes/PostMetaField.php';
		require_once __DIR__ . '/classes/post-meta-fields/IsMetaboxDisabledField.php';
		require_once __DIR__ . '/classes/post-meta-fields/ExpirationDateField.php';
		require_once __DIR__ . '/classes/post-meta-fields/RedirectToField.php';
		require_once __DIR__ . '/classes/PostMeta.php';

		require_once __DIR__ . '/classes/Shortcode.php';

		require_once __DIR__ . '/classes/Redirect.php';

		require_once __DIR__ . '/classes/Helpers.php';

		require_once __DIR__ . '/classes/SettingsPage.php';

		require_once __DIR__ . '/classes/options/OptionInterface.php';
		require_once __DIR__ . '/classes/options/PostTypesOption.php';
		require_once __DIR__ . '/classes/options/CommonTextOption.php';

		add_action( 'admin_menu', function () {
			SettingsPage::instance()->addSettingsPage();
		} );

		add_action( 'admin_init', function () {
			SettingsPage::instance()->addPostTypesSection();
			SettingsPage::instance()->addShortcodeSettingsSection();
		} );

		add_action( 'the_post', function ( WP_Post $post ) {
			Redirect::instance()->run( $post );
		} );

		add_shortcode( 'expiration_message', function ( array $atts, string $content ) {
			return Shortcode::instance()->insertShortcode( $atts, $content );
		} );

		add_action( 'init', function (): void {
			PostMeta::instance()->registerFieldsForActivePostTypes();
		} );
		add_action( 'enqueue_block_editor_assets', function () {
			PostMeta::instance()->enqueueScriptForActivePostTypes();
		} );
		add_filter( 'is_protected_meta', function ( bool $protected, string $metaKey ): bool {
			return PostMeta::instance()->hideMetaFieldsFromDefaultList( $protected, $metaKey );
		}, 10, 2 );
		add_action( 'add_meta_boxes', function ( string $postType, WP_Post $post ): void {
			PostMeta::instance()->addMetaBoxToActivePostTypes( $postType, $post );
		}, 10, 2 );
		add_action( 'save_post', function ( int $postId ) {
			PostMeta::instance()->save( $postId );
		} );
	}
}

ExpirationActions::init();