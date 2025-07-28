<?php

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'dentalsvetiluka_disable_block_editor_styles_frontend' ) ) {
	function dentalsvetiluka_disable_block_editor_styles_frontend() {
		// Check if we are NOT in the WordPress admin panel or editor.
		if ( ! is_admin() ) {
			// Dequeue block editor styles.
			// Removes core block styles.
			wp_dequeue_style( 'wp-block-library' );
			// Removes theme block styles.
			wp_dequeue_style( 'wp-block-library-theme' );
		}
	}

	add_action( 'wp_enqueue_scripts', 'dentalsvetiluka_disable_block_editor_styles_frontend', 100 );
}

/**
 * Disable the WordPress Heartbeat API on the frontend but keep it active in the admin and post editor.
 */
if ( ! function_exists( 'dentalsvetiluka_disable_heartbeat_frontend' ) ) {
	function dentalsvetiluka_disable_heartbeat_frontend() {
		// Check if we are on the frontend and NOT in the admin or post editor.
		if ( ! is_admin() ) {
			// Removes Heartbeat API script from loading.
			wp_deregister_script( 'heartbeat' );
		}
	}

	add_action( 'wp_enqueue_scripts', 'dentalsvetiluka_disable_heartbeat_frontend', 100 );
}

/**
 * Control the Heartbeat API execution based on user area.
 */
if ( ! function_exists( 'dentalsvetiluka_control_heartbeat_settings' ) ) {
	function dentalsvetiluka_control_heartbeat_settings( $settings ) {
		// If we are on the frontend, reduce the heartbeat frequency.
		if ( ! is_admin() ) {
			// Set Heartbeat interval to 60 seconds (default is usually 15-60 sec).
			$settings['interval'] = 60;
		}

		return $settings;
	}

	add_filter( 'heartbeat_settings', 'dentalsvetiluka_control_heartbeat_settings' );
}

/**
 * Disable all WordPress emoji scripts and styles.
 */
if ( ! function_exists( 'dentalsvetiluka_disable_emojis' ) ) {
	function dentalsvetiluka_disable_emojis() {
		// Remove emoji script from frontend and admin.
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );

		// Remove emoji styles from frontend and admin.
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );

		// Prevent emojis from being injected in the RSS feed.
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

		// Remove TinyMCE emoji support (editor compatibility).
		add_filter( 'tiny_mce_plugins', 'dentalsvetiluka_disable_emojis_tinymce' );
		add_filter( 'wp_resource_hints', 'dentalsvetiluka_disable_emojis_remove_dns_prefetch', 10, 2 );
	}

	add_action( 'init', 'dentalsvetiluka_disable_emojis' );
}

/**
 * Remove TinyMCE emoji plugin support in the WordPress editor.
 */
if ( ! function_exists( 'dentalsvetiluka_disable_emojis_tinymce' ) ) {
	function dentalsvetiluka_disable_emojis_tinymce( $plugins ) {
		// Bail if the plugins is not an array.
		if ( ! is_array( $plugins ) ) {
			return array();
		}

		// Remove the `wpemoji` plugin and return everything else.
		return array_diff( $plugins, array( 'wpemoji' ) );
	}
}

/**
 * Remove emoji CDN hostname from DNS prefetching hints.
 *
 * @param  array  $urls          URLs to print for resource hints.
 * @param  string $relation_type The relation type the URLs are printed for.
 * @return array                 Difference betwen the two arrays.
 */
if ( ! function_exists( 'dentalsvetiluka_disable_emojis_remove_dns_prefetch' ) ) {
	function dentalsvetiluka_disable_emojis_remove_dns_prefetch( $urls, $relation_type ) {
		if ( 'dns-prefetch' === $relation_type ) {
			/** This filter is documented in wp-includes/formatting.php */
			$emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/' );

			$urls = array_diff( $urls, array( $emoji_svg_url ) );
		}

		return $urls;
	}
}

/**
 * Remove all oEmbed-related scripts and discovery links from WordPress.
 */
if ( ! function_exists( 'dentalsvetiluka_disable_wp_oembed' ) ) {
	function dentalsvetiluka_disable_wp_oembed() {
		// Remove oEmbed discovery links from <head> (prevents unnecessary requests).
		remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );

		// Remove oEmbed-specific JavaScript that loads on the frontend.
		remove_action( 'wp_head', 'wp_oembed_add_host_js' );

		// Disable REST API oEmbed endpoints (prevents external sites from embedding WP content).
		remove_action( 'rest_api_init', 'wp_oembed_register_route' );

		// Remove oEmbed filtering from content processing (stops WP auto-converting URLs).
		remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );
		remove_filter( 'oembed_response_data', 'get_oembed_response_data', 10 );

		// Disable automatic oEmbed URL conversion for posts/comments.
		remove_filter( 'the_content', [ $GLOBALS['wp_embed'], 'autoembed' ], 8 );
		remove_filter( 'widget_text_content', [ $GLOBALS['wp_embed'], 'autoembed' ], 8 );
	}

	add_action( 'init', 'dentalsvetiluka_disable_wp_oembed' );
}

/**
 * Disable self-pingbacks in WordPress to prevent unnecessary notifications.
 *
 * Pingbacks allow automatic notifications when linking to a post on another site.
 * However, self-pingbacks occur when a site links to its own posts, cluttering comments.
 * This function removes links from the ping process if they belong to the same domain.
 */
if ( ! function_exists( 'dentalsvetiluka_disable_self_pingbacks' ) ) {
	function dentalsvetiluka_disable_self_pingbacks( &$links ) {
		// Get the site's base URL.
		$home_url = home_url();

		foreach ( $links as $key => $link ) {
			// Check if the link belongs to this site.
			if ( strpos( $link, $home_url ) === 0 ) {
				// Remove self-pingback link.
				unset( $links[ $key ] );
			}
		}
	}

	add_action( 'pre_ping', 'dentalsvetiluka_disable_self_pingbacks' );
}

/**
 * Modify wp_speculation_rules_configuration to use 'prerender' mode with 'moderate' eagerness.
 *
 * Prerendering helps improve perceived performance by preloading the next likely page.
 */
if ( ! function_exists( 'dentalsvetiluka_modify_speculation_rules' ) ) {
	function dentalsvetiluka_modify_speculation_rules( $config ) {
		if ( is_array( $config ) ) {
			$config['mode']      = 'prerender';
			$config['eagerness'] = 'moderate';
		}

		return $config;
	}

	add_filter( 'wp_speculation_rules_configuration', 'dentalsvetiluka_modify_speculation_rules' );
}

/**
 * Limit WordPress post revisions to 5.
 *
 * This reduces unnecessary database storage while retaining useful revisions for editing.
 */
if ( ! function_exists( 'dentalsvetiluka_limit_post_revisions' ) ) {
	function dentalsvetiluka_limit_post_revisions() {
		// Set the max number of revisions.
		return 5;
	}

	add_filter( 'wp_revisions_to_keep', 'dentalsvetiluka_limit_post_revisions' );
}

/**
 * Disable the `capital_P_dangit` function in WordPress.
 *
 * This function is normally applied to content, titles, comments, and feeds.
 * Removing it prevents WordPress from auto-correcting "wordpress" to "WordPress."
 */
if ( ! function_exists( 'dentalsvetiluka_disable_capital_p_dangit' ) ) {
	function dentalsvetiluka_disable_capital_p_dangit() {
		remove_filter( 'the_content', 'capital_P_dangit', 11 );
		remove_filter( 'the_title', 'capital_P_dangit', 11 );
		remove_filter( 'wp_title', 'capital_P_dangit', 11 );
		remove_filter( 'document_title', 'capital_P_dangit', 11 );
		remove_filter( 'widget_text_content', 'capital_P_dangit', 11 );
		remove_filter( 'comment_text', 'capital_P_dangit', 31 );
	}

	add_action( 'init', 'dentalsvetiluka_disable_capital_p_dangit' );
}

if ( ! function_exists( 'dentalsvetiluka_offload_multiple_stylesheets' ) ) {
	/**
	 * Adds the rel='preload' to stylesheets and onload replace it with rel='stylesheet' in order to offload them.
	 *
	 * @param string $html The complete HTML <link> tag.
	 * @param string $handle The stylesheet's registered handle.
	 *
	 * @return string The modified HTML <link> tag with rel='preload' attribute, or original HTML.
	 */
	function dentalsvetiluka_offload_multiple_stylesheets( $html, $handle ) {
		// Define an array of stylesheet handles that you want to offload.
		$offload_handles = array(
			'swiper',
			'elementor-icons',
			'elementor-frontend',
			'ionicons',
			'linear-icons',
			'linea-icons',
			'font-awesome',
			'dripicons',
			'elegant-icons',
		);

		// Check if the current stylesheet's handle is in our list of offload handles.
		if ( in_array( $handle, $offload_handles ) ) {
			// If it is, preload styles
			// return str_replace( "media='stylesheet'", "rel='preload' as='style'" . "onload=\"this.rel='stylesheet'\"", $html ).
			return str_replace( "rel='stylesheet'", "rel=\"preload\" as=\"style\" onload=\"this.rel=&#39;stylesheet&#39;\"", $html );
		}

		// If the handle is not in our list, return the original HTML.
		return $html;
	}

	add_filter( 'style_loader_tag', 'dentalsvetiluka_offload_multiple_stylesheets', 10, 2 );
}

if ( ! function_exists( 'dentalsvetiluka_dequeue_multiple_stylesheets' ) ) {
	/**
	 * Dequeues multiple stylesheets from being loaded.
	 */
	function dentalsvetiluka_dequeue_multiple_stylesheets() {
		// Define an array of stylesheet handles that you want to dequeue.
		$dequeue_handles = array(
			'swiper',
			'elementor-icons',
			'elementor-frontend',
			'ionicons',
			'linear-icons',
			'linea-icons',
			'font-awesome',
			'dripicons',
			'elegant-icons',
		);

		foreach ( $dequeue_handles as $handle ) {
			wp_dequeue_style( $handle );
		}
	}

	//add_action( 'wp_enqueue_scripts', 'dentalsvetiluka_dequeue_multiple_stylesheets', 999 );
}
