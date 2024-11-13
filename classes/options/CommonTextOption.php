<?php declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class CommonTextOption implements OptionInterface {
	private static ?self $instance = null;

	private const OPTION_NAME = "ea_option_common_shortcode_text";

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
			'sanitize_callback' => 'esc_attr',
			'type'              => 'string',
			'default'           => [ $this, 'getDefaultValue' ],
		] );
	}

	public function addField( string $page, string $pageSection ): void {
		add_settings_field(
			self::OPTION_NAME,
			__( 'Text', 'expiration-actions' ),
			function () { ?>
                <input
                        class="regular-text"
                        type="text"
                        name="<?= self::OPTION_NAME ?>"
                        value="<?= $this->getValue() ?>"
                        placeholder="<?= $this->getDefaultValue() ?>"
                />
                <p>
					<?= sprintf(
					/* translators: %s: Default Shortcode text */
						__( "Default value <code>%s</code> will be used if the field is empty", 'expiration-actions' ),
						$this->getDefaultValue()
					) ?>
                </p>
			<?php },
			$page,
			$pageSection
		);
	}

	public function getValueOrDefault(): string {
		$value = $this->getValue();

		if ( is_string( $value ) && ! empty( $value ) ) {
			return $value;
		}

		return $this->getDefaultValue();
	}

	private function getValue(): false|string {
		return get_option( self::OPTION_NAME );
	}

	private function getDefaultValue(): string {
		return __( "The page has expired", 'expiration-actions' );
	}
}