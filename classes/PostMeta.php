<?php declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class PostMeta {
	private static ?self $instance = null;

	private array $fields;

	public static function instance(): self {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
		$this->fields = [
			// IMPORTANT: Change these keys ORDER with caution, 'ea-meta-props' plugin JS relies on it
			new IsMetaboxDisabledField(),
			new ExpirationDateField(),
			new RedirectToField()
		];
	}

	public function hideMetaFieldsFromDefaultList( bool $protected, string $metaKey ): bool {
		/**
		 * Hide plugin meta fields within default 'Custom Fields' meta box (where all existing meta fields are listed)
		 * Plugin meta fields will be visible and editable for all post types (not only for selected in plugin settings)
		 */
		return in_array( $metaKey, $this->getFieldNames() ) ? true : $protected;
	}

	public function addMetaBoxToActivePostTypes( string $postType, \WP_Post $post ): void {
		if ( ! PostTypesOption::instance()->isPostTypeOptionSelected( $postType ) ) {
			return;
		}

		$this->addMetaBox( $post );
	}

	private function addMetaBox( \WP_Post $post ): void {
		add_meta_box(
			id: 'expiration_actions_meta_box',
			title: __( 'Expiration Actions', 'expiration-actions' ),
			callback: function () use ( $post ) {
				$this->addMetaBoxCallback( $post );
			},
			callback_args: [
				'__block_editor_compatible_meta_box' => false,
				'__back_compat_meta_box'             => true
			]
		);
	}

	public function registerFieldsForActivePostTypes(): void {
		$postTypesOption = PostTypesOption::instance()->getValueOrDefault();

		array_walk( $postTypesOption, function ( string $postType ) {
			array_walk( $this->fields, function ( PostMetaField $field ) use ( $postType ) {
				$field->registerForPostType( $postType );
			} );
		} );
	}

	public function enqueueScriptForActivePostTypes(): void {
		$postTypesOption = PostTypesOption::instance()->getValueOrDefault();
		if ( empty( $postTypesOption ) ) {
			return;
		}

		$this->enqueueScript();
	}

	private function enqueueScript(): void {
		$scriptName = 'expiration_actions_gutenberg_meta_props';

		$scriptFilePath = 'ea-meta-props/build/';

		$assetInfo = require_once BASE_PATH . $scriptFilePath . 'index.asset.php';

		wp_enqueue_script(
			$scriptName,
			BASE_URL . $scriptFilePath . 'index.js',
			$assetInfo['dependencies'],
			$assetInfo['version']
		);

		/**
		 *  Post meta field names are passing to the frontend to make refactoring easier
		 *  If post meta field name is changing in PHP there's no need to change JS code
		 */
		wp_add_inline_script(
			$scriptName,
			'const expirationActionsMetaFieldsData = ' . json_encode( [
				'fieldNames' => $this->getFieldNames()
			] ),
			'before'
		);
	}

	private function getFieldNames(): array {
		return array_reduce( $this->fields, function ( array $result, PostMetaField $field ) {
			$result[] = $field->getName();

			return $result;
		}, [] );
	}

	private function addMetaBoxCallback( WP_Post $post ): void {
		ob_start();
		?>

		<?php
		array_walk( $this->fields, function ( PostMetaField $field ) use ( $post ) {
			echo $field->generateHTML( $post );
		} );
		?>

		<?php
		echo ob_get_clean();
	}

	public function save( int $postId ): void {
		if ( ! current_user_can( 'edit_post', $postId ) ) {
			return;
		}

		array_walk( $this->fields, function ( PostMetaField $metaField ) use ( $postId ) {
			$metaField->save( $postId, $_POST );
		} );
	}
}