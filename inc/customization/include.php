<?php

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Set single team post to false - override default which is true
 *
 * @return bool
 */
function allsmiles_core_team_has_single() {
	return false;
}
