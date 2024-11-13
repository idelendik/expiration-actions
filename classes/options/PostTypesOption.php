<?php declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class PostTypesOption implements OptionInterface {
	private static ?self $instance = null;

	private const OPTION_NAME = "ea_option_post_types";

	public static function instance(): self {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
	}

	public function register( string $optionGroup ): void {
		register_setting( $optionGroup, self::OPTION_NAME, [
			'sanitize_callback' => 'rest_sanitize_object',
			'type'              => 'array',
			'default'           => [ $this, 'getDefaultValue' ],
		] );
	}

	public function addField( string $page, string $pageSection ): void {
		add_settings_field(
			self::OPTION_NAME,
			__( 'Post Types', 'expiration-actions' ),
			function () {
				$postTypes = Helpers::instance()->getNotExcludedPostTypes();

				array_walk( $postTypes, function ( $postType ) {
					$isChecked  = $this->isPostTypeOptionSelected( $postType ) ? 'checked' : '';
					$optionName = self::OPTION_NAME . '[' . $postType . ']'
					?>

                    <fieldset>
                        <div>
                            <label for="<?= $optionName ?>">
                                <input
                                        type="checkbox"
                                        id="<?= $optionName ?>"
                                        name="<?= $optionName ?>"
									<?= $isChecked ?>
                                />
								<?= $postType ?>
                            </label>
                        </div>
                    </fieldset>

					<?php
				} );
			},
			$page,
			$pageSection
		);
	}

	public function getValueOrDefault(): array {
		$value = $this->getValue();

		if ( is_array( $value ) && ! empty( $value ) ) {
			return array_keys( $value );
		}

		return $this->getDefaultValue();
	}

	private function getValue(): false|array {
		return get_option( self::OPTION_NAME );
	}

	private function getDefaultValue(): array {
		return [];
	}

	public function isPostTypeOptionSelected( string $postType ): bool {
		$selectedPostTypes = $this->getValueOrDefault();

		return in_array( $postType, $selectedPostTypes );
	}
}