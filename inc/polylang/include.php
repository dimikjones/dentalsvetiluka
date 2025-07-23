<?php

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'POLYLANG_VERSION' ) ) {
	return;
}

// Include team custom post type for translation with Polylang plugin.
if ( ! function_exists( 'dentalsvetiluka_add_team_cpt_to_pll' ) ) {
	function dentalsvetiluka_add_team_cpt_to_pll( $post_types, $is_settings ) {
		if ( $is_settings ) {
			// Hides 'team' from the list of custom post types in Polylang settings.
			unset( $post_types['team'] );
		} else {
			// Enables language and translation management for 'team'.
			$post_types['team'] = 'team';
		}

		return $post_types;
	}

	add_filter( 'pll_get_post_types', 'dentalsvetiluka_add_team_cpt_to_pll', 10, 2 );
}
