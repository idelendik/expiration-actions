<?php declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class ExpirationDateField extends PostMetaField {
	public function __construct() {
		parent::__construct(
			'ea_expiration_datetime',
			'string',
			'',
			'sanitize_text_field',
			'Holds an expiration datetime value'
		);
	}

	public function generateHTML( WP_Post $post ): string {
		$propName            = $this->getName();
		$expirationTimestamp = $this->getValue( $post->ID );

		$inputHTML = sprintf( "<input value='{$expirationTimestamp}' id='{$propName}' name='{$propName}' type='datetime-local'/>" );

		$fieldHTML = sprintf( "<div><label for='{$propName}'>%s {$inputHTML}</label></div><div>&nbsp;</div>",
			__( 'Expiration Date', 'expiration-actions' ),
		);

		return $fieldHTML;
	}

	public function isPostExpired( int $postId ): bool {
		$expirationDate = $this->getValue( $postId );

		if ( empty( $expirationDate ) ) {
			return false;
		}

		$isExpirationTimeInThePast = strtotime( $expirationDate ) < time();

		return $isExpirationTimeInThePast;
	}
}