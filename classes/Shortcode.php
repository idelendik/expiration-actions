<?php declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Shortcode {
	private static ?self $instance = null;

	public static function instance(): self {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
	}

	public function insertShortcode( array $atts, string $content ): string {
		$post = get_post();

		if ( ! PostTypesOption::instance()->isPostTypeOptionSelected( $post->post_type ) ) {
			return '';
		}

		if ( ( new IsMetaboxDisabledField() )->isDisabled( $post->ID ) ) {
			return '';
		}

		if ( ! ( new ExpirationDateField() )->isPostExpired( $post->ID ) ) {
			return '';
		}

		$text = $this->getShortcodeContent( $content );

		return $text;
	}

	private function getShortcodeContent( string $shortcodeContent ): string {
		// page level (shortcode tag)
		$shortcodeContent = trim( $shortcodeContent );
		if ( ! empty( $shortcodeContent ) ) {
			return $shortcodeContent;
		}

		// options level (or default text)
		return CommonTextOption::instance()->getValueOrDefault();
	}
}