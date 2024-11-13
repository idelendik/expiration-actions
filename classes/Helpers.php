<?php declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Helpers {
	private static ?self $instance = null;

	public static function instance(): self {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
	}

	public function getNotExcludedPostTypes(): array {
		$excludedDefaultPostTypes = [
			'attachment',
			'revision',
			'nav_menu_item',
			'custom_css',
			'customize_changeset',
			'oembed_cache',
			'user_request',
			'wp_block',
			'wp_template',
			'wp_template_part',
			'wp_global_styles',
			'wp_navigation',
			'wp_font_family',
			'wp_font_face'
		];

		return array_filter( get_post_types(), fn( $postType ) => ! in_array( $postType, $excludedDefaultPostTypes ) );
	}
}