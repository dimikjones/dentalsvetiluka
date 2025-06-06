<?php

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'QodeFramework' ) && ! class_exists( 'AllSmilesCore' ) ) {
	return;
}

// Floating Contact Actions - Options.
if ( ! function_exists( 'dentalsvetiluka_add_floating_contact_actions' ) ) {
	/**
	 * Function that add options for post format
	 *
	 * @param mixed $page - general post format meta box section
	 */
	function dentalsvetiluka_add_floating_contact_actions( $page ) {

		if ( $page ) {

			$page->add_field_element(
				array(
					'field_type'    => 'yesno',
					'name'          => 'qodef_dsl_floating_contact_actions',
					'title'         => esc_html__( 'Floating Contact Actions', 'dentalsvetiluka' ),
					'default_value' => 'no',
				)
			);

			$floating_contact_section = $page->add_section_element(
				array(
					'name'       => 'qodef_dsl_floating_contact_actions_section',
					'title'      => esc_html__( 'Floating Contact Section', 'dentalsvetiluka' ),
					'dependency' => array(
						'show' => array(
							'qodef_dsl_floating_contact_actions' => array(
								'values'        => 'yes',
								'default_value' => 'no',
							),
						),
					),
				)
			);

			$floating_contact_section->add_field_element(
				array(
					'field_type'    => 'yesno',
					'name'          => 'qodef_dsl_floating_contact_actions_phone',
					'title'         => esc_html__( 'Phone', 'dentalsvetiluka' ),
					'default_value' => 'no',
				)
			);
			$floating_contact_section->add_field_element(
				array(
					'field_type'  => 'text',
					'name'        => 'qodef_dsl_floating_contact_actions_phone_number',
					'title'       => esc_html__( 'Phone Number', 'dentalsvetiluka' ),
					'description' => esc_html__( 'Enter Number in full format with country code (e.g. +381555333)', 'dentalsvetiluka' ),
					'dependency'  => array(
						'show' => array(
							'qodef_dsl_floating_contact_actions_phone' => array(
								'values'        => 'yes',
								'default_value' => 'no',
							),
						),
					),
				)
			);

			$floating_contact_section->add_field_element(
				array(
					'field_type'    => 'yesno',
					'name'          => 'qodef_dsl_floating_contact_actions_whatsapp',
					'title'         => esc_html__( 'WhatsApp', 'dentalsvetiluka' ),
					'default_value' => 'no',
				)
			);
			$floating_contact_section->add_field_element(
				array(
					'field_type'  => 'text',
					'name'        => 'qodef_dsl_floating_contact_actions_whatsapp_number',
					'title'       => esc_html__( 'WhatsApp Number', 'dentalsvetiluka' ),
					'description' => esc_html__( 'Enter Number in regular format without country code (e.g. 064555333)', 'dentalsvetiluka' ),
					'dependency'  => array(
						'show' => array(
							'qodef_dsl_floating_contact_actions_whatsapp' => array(
								'values'        => 'yes',
								'default_value' => 'no',
							),
						),
					),
				)
			);

			$floating_contact_section->add_field_element(
				array(
					'field_type'    => 'yesno',
					'name'          => 'qodef_dsl_floating_contact_actions_viber',
					'title'         => esc_html__( 'Viber', 'dentalsvetiluka' ),
					'default_value' => 'no',
				)
			);
			$floating_contact_section->add_field_element(
				array(
					'field_type'  => 'text',
					'name'        => 'qodef_dsl_floating_contact_actions_viber_number',
					'title'       => esc_html__( 'Viber Number', 'dentalsvetiluka' ),
					'description' => esc_html__( 'Enter Number in full format with country code but WITHOUT + (e.g. 381555333)', 'dentalsvetiluka' ),
					'dependency'  => array(
						'show' => array(
							'qodef_dsl_floating_contact_actions_viber' => array(
								'values'        => 'yes',
								'default_value' => 'no',
							),
						),
					),
				)
			);

			// Hook to include additional options after module options.
			do_action( 'dentalsvetiluka_action_after_floating_contact_actions_option', $page );
		}
	}

	add_action( 'allsmiles_core_action_after_general_options_map', 'dentalsvetiluka_add_floating_contact_actions', 3 );
}

// Load template for Floating Contact Actions.
if ( ! function_exists( 'dentalsvetiluka_add_floating_contact_actions_load_template' ) ) {
	/**
	 * Loads contact actions HTML
	 */
	function dentalsvetiluka_add_floating_contact_actions_load_template() {
		$contact_enabled  = 'no' !== allsmiles_core_get_post_value_through_levels( 'qodef_dsl_floating_contact_actions' );
		$phone_enabled    = 'no' !== allsmiles_core_get_post_value_through_levels( 'qodef_dsl_floating_contact_actions_phone' );
		$whatsapp_enabled = 'no' !== allsmiles_core_get_post_value_through_levels( 'qodef_dsl_floating_contact_actions_whatsapp' );
		$viber_enabled    = 'no' !== allsmiles_core_get_post_value_through_levels( 'qodef_dsl_floating_contact_actions_viber' );

		if ( $contact_enabled ) {

			$params = array(
				'phone_enabled'    => $phone_enabled,
				'whatsapp_enabled' => $whatsapp_enabled,
				'viber_enabled'    => $viber_enabled,
			);

			if ( $phone_enabled || $whatsapp_enabled || $viber_enabled ) {
				qode_framework_template_part( DENTALSVETILUKA_INC_PATH, 'floating-contact', 'templates/floating-contact', '', $params );
			}
		}
	}

	add_action( 'wp_footer', 'dentalsvetiluka_add_floating_contact_actions_load_template', 10 );
}
