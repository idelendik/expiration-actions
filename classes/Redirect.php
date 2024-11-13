<?php declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Redirect {
	private static ?self $instance = null;

	public static function instance(): self {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
	}

	public function run( WP_Post $post ): void {
		if ( is_front_page() ) {
			return;
		}

		if ( is_admin() ) {
			return;
		}

		if ( ! PostTypesOption::instance()->isPostTypeOptionSelected( $post->post_type ) ) {
			return;
		}

		if ( ( new IsMetaboxDisabledField() )->isDisabled( $post->ID ) ) {
			return;
		}

		if ( ! ( new ExpirationDateField() )->isPostExpired( $post->ID ) ) {
			return;
		}

		$redirectToUrl = ( new RedirectToField() )->getValue( $post->ID );
		if ( "" === $redirectToUrl ) {
			return;
		}

		wp_redirect( $redirectToUrl );
	}
}