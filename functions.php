<?php

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Elektromikron theme constants.
define( 'DENTALSVETILUKA_INC_PATH', __DIR__ . '/inc' );
define( 'DENTALSVETILUKA_ROOT_DIR', get_stylesheet_directory() );

if ( ! function_exists( 'dentalsvetiluka_enqueue_scripts' ) ) {
	/**
	 * Function that enqueue theme's child style
	 */
	function dentalsvetiluka_enqueue_scripts() {
		$theme_version = function_exists( 'wp_get_theme' ) ? wp_get_theme()->get( 'Version' ) : false;
		$main_style    = 'allsmiles-main';

		wp_enqueue_style( 'dentalsvetiluka-style', get_stylesheet_directory_uri() . '/assets/front.css', array( $main_style ), $theme_version );
	}

	add_action( 'wp_enqueue_scripts', 'dentalsvetiluka_enqueue_scripts' );
}

// Include child theme modules.
if ( ! function_exists( 'dentalsvetiluka_include_modules' ) ) {

	function dentalsvetiluka_include_modules() {

		foreach ( glob( DENTALSVETILUKA_ROOT_DIR . '/inc/*/include.php' ) as $module ) {
			include_once $module;
		}
	}

	// Call the function for file inclusion.
	dentalsvetiluka_include_modules();
}
