<?php declare( strict_types=1 );

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

interface OptionInterface {
	public function register( string $optionGroup ): void;

	public function addField( string $page, string $pageSection ): void;

	public function getValueOrDefault(): string|array;
}