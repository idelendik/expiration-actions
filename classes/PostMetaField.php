<?php declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abstract class PostMetaField {
	private string $name;
	private string $type;
	private string $defaultValue;
	private string $sanitizeCallback;
	private string $description;

	public function __construct(
		string $name,
		string $type,
		string $defaultValue,
		string $sanitizeCallback,
		string $description
	) {
		$this->name             = $name;
		$this->type             = $type;
		$this->defaultValue     = $defaultValue;
		$this->sanitizeCallback = $sanitizeCallback;
		$this->description      = $description;
	}

	public function getName(): string {
		return $this->name;
	}

	protected function getType(): string {
		return $this->type;
	}

	protected function getValueDefault(): string {
		return $this->defaultValue;
	}

	protected function getSanitizeCallback(): string {
		return $this->sanitizeCallback;
	}

	protected function getDescription(): string {
		return $this->description;
	}

	public function save( int $postId, array $S_POST ): void {
		/** To prevent firing on post page creation */
		if ( empty( $_POST ) ) {
			return;
		}

		update_post_meta(
			$postId,
			$this->name,
			$S_POST[ $this->name ] ?? $this->getValueDefault()
		);
	}

	public function registerForPostType( string $postType ): void {
		register_post_meta(
			$postType,
			$this->getName(),
			[
				'type'              => $this->getType(),
				'description'       => $this->getDescription(),
				'single'            => true,
				'default'           => $this->getValueDefault(),
				'sanitize_callback' => $this->getSanitizeCallback(),
				'auth_callback'     => function () {
					return current_user_can( 'edit_posts' );
				},
				'show_in_rest'      => true,
			]
		);
	}

	public function getValue( int $postId ): string {
		return get_post_meta( $postId, $this->getName(), true );
	}

	protected abstract function generateHTML( WP_Post $post ): string;
}



