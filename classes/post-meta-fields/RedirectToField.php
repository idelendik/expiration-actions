<?php declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class RedirectToField extends PostMetaField {
	public function __construct() {
		parent::__construct(
			'ea_redirect_url',
			'string',
			'',
			'sanitize_text_field',
			'Holds a redirect URL value'
		);
	}

	public function generateHTML( WP_Post $post ): string {
		$propName    = $this->getName();
		$redirectUrl = $this->getValue( $post->ID );

		$inputHTML = "<input value='{$redirectUrl}' id='{$propName}' name='{$propName}' placeholder='URL' type='text' />";

		$fieldHTML = sprintf( "<div><label for='{$propName}'>%s {$inputHTML}</label></div>",
			__( 'Redirect To', 'expiration-actions' )
		);

		return $fieldHTML;
	}
}