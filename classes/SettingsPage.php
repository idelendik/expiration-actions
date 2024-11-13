<?php declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class SettingsPage {
	private static ?self $instance = null;

	private const PAGE_NAME = 'expiration_actions';
	private const OPTION_GROUP = 'ea-settings';

	public static function instance(): self {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
	}

	public function addSettingsPage(): void {
		add_options_page(
			__( 'Expiration Actions Settings', 'expiration-actions' ),
			__( 'Expiration Actions', 'expiration-actions' ),
			'manage_options',
			self::PAGE_NAME,
			function () { ?>
                <div class="wrap">
                    <h1><?= __( 'Expiration Actions Settings', 'expiration-actions' ) ?></h1>
                    <p><?= __( 'Expiration Actions will be available for selected post types only', 'expiration-actions' ) ?></p>

                    <form action="options.php" method="POST">
						<?php
						settings_fields( self::OPTION_GROUP );

						do_settings_sections( self::PAGE_NAME );

						submit_button();
						?>
                    </form>
                </div>
			<?php },
		);
	}

	public function addPostTypesSection(): void {
		$this->addPageSectionWithFields(
			'post_types_section',
			null,
			[
				PostTypesOption::instance()
			]
		);
	}

	public function addShortcodeSettingsSection(): void {
		$this->addPageSectionWithFields(
			'shortcode_settings_section',
			__( 'Shortcode Settings', 'expiration-actions' ),
			[
				CommonTextOption::instance()
			]
		);
	}

	private function addPageSectionWithFields(
		string $pageSection,
		?string $sectionTitle,
		array $fields
	): void {
		add_settings_section( $pageSection, $sectionTitle, '__return_empty_string', self::PAGE_NAME );
		$this->addSettingFieldsToSection( $pageSection, $fields );
	}

	private function addSettingFieldsToSection( string $pageSection, array $fields ): void {
		array_walk( $fields, function ( OptionInterface $field ) use ( $pageSection ) {
			$field->addField( self::PAGE_NAME, $pageSection );
			$field->register( self::OPTION_GROUP );
		} );
	}
}