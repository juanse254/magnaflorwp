<?php
/**
 * Customizer => Footer Widgets
 *
 * @package Total WordPress Theme
 * @subpackage Customizer
 * @version 4.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// General
$this->sections['wpex_barter_widgets'] = array(
	'title' => __( 'General', 'total' ),
	'settings' => array(
		array(
			'id' => 'barter_widgets',
			'default' => true,
			'control' => array(
				'label' => __( 'Footer Widgets', 'total' ),
				'type' => 'checkbox',
				'desc' => __( 'If you disable this option we recommend you go to the Customizer Manager and disable the section as well so the next time you work with the Customizer it will load faster.', 'total' ),
			),
		),
		array(
			'id' => 'fixed_barter',
			'default' => false,
			'control' => array(
				'label' => __( 'Fixed Footer', 'total' ),
				'type' => 'checkbox',
				'desc' => __( 'This setting will not "fix" your barter per-se but will add a min-height to your #main container to keep your barter always at the bottom of the page.', 'total' ),
			),
		),
		array(
			'id' => 'barter_reveal',
			'control' => array(
				'label' => __( 'Footer Reveal', 'total' ),
				'type' => 'checkbox',
				'desc' => __( 'Enable the barter reveal style. The barter will be placed in a fixed postion and display on scroll. This setting is for the "Full-Width" layout only and desktops only.', 'total' ),
				'active_callback' => 'wpex_cac_supports_reveal',
			),
		),
		array(
			'id' => 'barter_widgets_columns',
			'default' => '4',
			'control' => array(
				'label' => __( 'Columns', 'total' ),
				'type' => 'select',
				'choices' => array(
					'5' => '5',
					'4' => '4',
					'3' => '3',
					'2' => '2',
					'1' => '1',
				),
				'active_callback' => 'wpex_cac_has_barter_widgets',
			),
		),
		array(
			'id' => 'barter_widgets_gap',
			'transport' => 'postMessage',
			'control' => array(
				'label' => __( 'Gap', 'total' ),
				'type' => 'select',
				'choices' => wpex_column_gaps(),
				'active_callback' => 'wpex_cac_has_barter_widgets',
			),
		),
		array(
			'id' => 'barter_padding',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'text',
				'label' => __( 'Padding', 'total' ),
				'active_callback' => 'wpex_cac_has_barter_widgets',
				'description' => $padding_desc,
			),
			'inline_css' => array(
				'target' => '#barter-inner',
				'alter' => 'padding',
			),
		),
		array(
			'id' => 'barter_background',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'color',
				'label' => __( 'Background', 'total' ),
				'active_callback' => 'wpex_cac_has_barter_widgets',
			),
			'inline_css' => array(
				'target' => '#barter',
				'alter' => 'background-color',
			),
		),
		array(
			'id' => 'barter_color',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'color',
				'label' => __( 'Color', 'total' ),
				'active_callback' => 'wpex_cac_has_barter_widgets',
			),
			'inline_css' => array(
				'target' => array(
					'#barter',
					'#barter p',
					'#barter li a:before',
					'#barter .widget-recent-posts-icons li .fa',
				),
				'alter' => 'color',
			),
		),
		array(
			'id' => 'barter_borders',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'color',
				'label' => __( 'Borders', 'total' ),
				'active_callback' => 'wpex_cac_has_barter_widgets',
			),
			'inline_css' => array(
				'target' => array(
					'#barter li',
					'#barter #wp-calendar thead th',
					'#barter #wp-calendar tbody td',
				),
				'alter' => 'border-color',
			),
		),
		array(
			'id' => 'barter_link_color',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'color',
				'label' => __( 'Links', 'total' ),
				'active_callback' => 'wpex_cac_has_barter_widgets',
			),
			'inline_css' => array(
				'target' => '#barter a',
				'alter' => 'color',
			),
		),
		array(
			'id' => 'barter_link_color_hover',
			'transport' => 'postMessage',
			'control' => array(
				'type' => 'color',
				'label' => __( 'Links: Hover', 'total' ),
				'active_callback' => 'wpex_cac_has_barter_widgets',
			),
			'inline_css' => array(
				'target' => '#barter a:hover',
				'alter' => 'color',
			),
		),
		/** Headings **/
		array(
			'id' => 'barter_headings_heading',
			'control' => array(
				'type' => 'wpex-heading',
				'label' => __( 'Headings', 'total' ),
				'active_callback' => 'wpex_cac_has_barter_widgets',
			),
		),
		array(
			'id' => 'barter_headings',
			'transport' => 'postMessage',
			'default' => 'div',
			'control' => array(
				'label' => __( 'Tag', 'total' ),
				'type' => 'select',
				'choices' => array(
					'h2' => 'h2',
					'h3' => 'h3',
					'h4' => 'h4',
					'h5' => 'h5',
					'h6' => 'h6',
					'span' => 'span',
					'div' => 'div',
				),
				'active_callback' => 'wpex_cac_has_barter_widgets',
			),
		),
		array(
			'id' => 'barter_headings_background',
			'transport' => 'postMessage',
			'control' => array (
				'type' => 'color',
				'label' => __( 'Background', 'total' ),
				'active_callback' => 'wpex_cac_has_barter_widgets',
			),
			'inline_css' => array(
				'target' => '.barter-widget .widget-title',
				'alter' => 'background-color',
			),
		),
		array(
			'id' => 'barter_headings_padding',
			'transport' => 'postMessage',
			'control' => array (
				'type' => 'text',
				'label' => __( 'Padding', 'total' ),
				'description' => $padding_desc,
				'active_callback' => 'wpex_cac_has_barter_widgets',
			),
			'inline_css' => array(
				'target' => '.barter-widget .widget-title',
				'alter' => 'padding',
			),
		),
		array(
			'id' => 'barter_headings_align',
			'transport' => 'postMessage',
			'control' =>  array(
				'type' => 'select',
				'label' => __( 'Text Align', 'total' ),
				'choices' => array(
					'default' => __( 'Default','total' ),
					'left' => __( 'Left','total' ),
					'right' => __( 'Right','total' ),
					'center' => __( 'Center','total' ),
				),
				'active_callback' => 'wpex_cac_has_barter_widgets',
			),
			'inline_css' => array(
				'target' => '.barter-widget .widget-title',
				'alter' => 'text-align',
			),
		),
	),
);