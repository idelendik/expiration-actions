<?php declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class IsMetaboxDisabledField extends PostMetaField {
	public function __construct() {
		parent::__construct(
			'ea_is_metabox_disabled',
			'string',
			'1',
			'sanitize_text_field',
			'Holds a value that describes whether a metabox is disabled'
		);
	}

	public function generateHTML( WP_Post $post ): string {
		$propName  = $this->getName();
		$isChecked = $this->getValue( $post->ID ) ? 'checked' : '';

		$inputHiddenHTML = "<input type='hidden' name='{$propName}' value='0'>";
		$inputHTML       = "<input type='checkbox' name='{$propName}' value='1' {$isChecked}>";

		$fieldHTML = sprintf( "<div><label for='{$propName}'>%s {$inputHiddenHTML} {$inputHTML}</label></div><div>&nbsp;</div>",
			__( 'Disabled', 'expiration-actions' )
		);

		return $fieldHTML;
	}

	public function isDisabled( int $postId ): bool {
		return (bool) $this->getValue( $postId );
	}
}