<?php
/**
 * Adds all Typography options to the Customizer and outputs the custom CSS for them
 *
 * @package Total WordPress Theme
 * @subpackage Customizer
 * @version 4.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Start class
if ( ! class_exists( 'WPEX_Typography' ) ) {

	class WPEX_Typography {

		/**
		 * Main constructor
		 *
		 * @since 1.6.0
		 */
		public function __construct() {

			// Get customizer enabled panels
			$enabled_panels = get_option( 'wpex_customizer_panels', array( 'typography' => true ) );

			// Register customizer settings
			if ( isset( $enabled_panels['typography'] ) ) {
				add_action( 'customize_register', array( 'WPEX_Typography' , 'register' ), 40 );
			}

			// Admin functions
			if ( is_admin() ) {

				// Add fonts to the mce editor
				add_action( 'admin_init', array( 'WPEX_Typography', 'mce_scripts' ) );
				add_filter( 'tiny_mce_before_init', array( 'WPEX_Typography', 'mce_fonts' ) );

			}

			// Front end functions
			else {

				// Load Google Font scripts
				if ( wpex_get_mod( 'google_fonts_in_barter' ) ) {
					add_action( 'wp_barter', array( 'WPEX_Typography', 'load_fonts' ) );
				} else {
					add_action( 'wp_enqueue_scripts', array( 'WPEX_Typography', 'load_fonts' ) );
				}

			}

			// CSS output
			if ( is_customize_preview() && isset( $enabled_panels['typography'] ) ) {
				add_action( 'customize_preview_init', array( 'WPEX_Typography', 'customize_preview_init' ) );
				add_action( 'wp_head', array( 'WPEX_Typography', 'live_preview_styles' ), 999 );
			} else {
				add_filter( 'wpex_head_css', array( 'WPEX_Typography', 'head_css' ), 99 );
			}

		}

		/**
		 * Array of Typography settings to add to the customizer
		 *
		 * @since 1.6.0
		 */
		public static function elements() {

			// Set default font to Open Sans unless Google Services are disabled
			$body_default = wpex_disable_google_services() ? '' : 'Open Sans';

			// Return settings
			$array = apply_filters( 'wpex_typography_settings', array(
				'body' => array(
					'label' => __( 'Body', 'total' ),
					'target' => 'body',
					'defaults' => array(
						'font-family' => $body_default,
					),
				),
				'logo' => array(
					'label' => __( 'Logo', 'total' ),
					'target' => '#site-logo a.site-logo-text',
					'exclude' => array( 'font-color' ),
					'active_callback' => 'wpex_cac_hasnt_custom_logo',
				),
				'top_menu' => array(
					'label' => __( 'Top Bar', 'total' ),
					'target' => '#top-bar-content',
					'exclude' => array( 'font-color' ),
					'active_callback' => 'wpex_cac_has_topbar',
				),
				'menu' => array(
					'label' => __( 'Main Menu', 'total' ),
					'target' => '#site-navigation .dropdown-menu a',
					'exclude' => array( 'font-color', 'line-height' ),
				),
				'menu_dropdown' => array(
					'label' => __( 'Main Menu: Dropdowns', 'total' ),
					'target' => '#site-navigation .dropdown-menu ul a',
					'exclude' => array( 'font-color' ),
				),
				'mobile_menu' => array(
					'label' => __( 'Mobile Menu', 'total' ),
					'target' => '.wpex-mobile-menu, #sidr-main',
					'exclude' => array( 'font-color' ),
				),
				'page_title' => array(
					'label' => __( 'Page Header Title', 'total' ),
					'target' => '.page-header .page-header-title',
					'exclude' => array( 'font-color' ),
					'active_callback' => 'wpex_cac_has_page_header',
				),
				'page_subheading' => array(
					'label' => __( 'Page Title Subheading', 'total' ),
					'target' => '.page-header .page-subheading',
					'active_callback' => 'wpex_cac_has_page_header',
				),
				'blog_entry_title' => array(
					'label' => __( 'Blog Entry Title', 'total' ),
					'target' => '.blog-entry-title.entry-title a, .blog-entry-title.entry-title a:hover',
				),
				'blog_post_title' => array(
					'label' => __( 'Blog Post Title', 'total' ),
					'target' => '.single-post-title',
				),
				'breadcrumbs' => array(
					'label' => __( 'Breadcrumbs', 'total' ),
					'target' => '.site-breadcrumbs',
					'exclude' => array( 'font-color', 'line-height' ),
					'active_callback' => 'wpex_cac_has_breadcrumbs',
				),
				'headings' => array(
					'label' => __( 'Headings', 'total' ),
					'target' => 'h1,h2,h3,h4,h5,h6,.theme-heading,.page-header-title,.heading-typography,.widget-title,.wpex-widget-recent-posts-title,.comment-reply-title,.vcex-heading,.entry-title,.sidebar-box .widget-title,.search-entry h2',
					'exclude' => array( 'font-size' ),
				),
				'theme_heading' => array(
					'label' => __( 'Theme Heading', 'total' ),
					'target' => '.theme-heading',
					'description' =>  __( 'Heading used in various places such as the related and comments heading.', 'total' ),
					'margin' => true,
				),
				'sidebar_widget_title' => array(
					'label' => __( 'Sidebar Widget Heading', 'total' ),
					'target' => '.sidebar-box .widget-title',
					'margin' => true,
				),
				'entry_h1' => array(
					'label' => __( 'Post H1', 'total' ),
					'target' => '.entry h1',
					'margin' => true,
				),
				'entry_h2' => array(
					'label' => __( 'Post H2', 'total' ),
					'target' => '.entry h2',
					'margin' => true,
				),
				'entry_h3' => array(
					'label' => __( 'Post H3', 'total' ),
					'target' => '.entry h3',
					'margin' => true,
				),
				'entry_h4' => array(
					'label' => __( 'Post H4', 'total' ),
					'target' => '.entry h4',
					'margin' => true,
				),
				'barter_widget_title' => array(
					'label' => __( 'Footer Widget Heading', 'total' ),
					'target' => '.barter-widget .widget-title',
					'active_callback' => 'wpex_cac_has_barter_widgets',
				),
				'callout' => array(
					'label' => __( 'Footer Callout', 'total' ),
					'target' => '.barter-callout-content',
					'exclude' => array( 'font-color' ),
				),
				'copyright' => array(
					'label' => __( 'Footer Bottom Text', 'total' ),
					'target' => '#copyright',
					'exclude' => array( 'font-color' ),
					'active_callback' => 'wpex_cac_has_barter_bottom',
				),
				'barter_menu' => array(
					'label' => __( 'Footer Bottom Menu', 'total' ),
					'target' => '#barter-bottom-menu',
					'exclude' => array( 'font-color' ),
					'active_callback' => 'wpex_cac_has_barter_bottom',
				),
			) );

			// Return array
			return $array;

		}

		/**
		 * Loads js file for customizer preview
		 *
		 * @since 3.3.0
		 */
		public static function customize_preview_init() {

			wp_enqueue_script( 'wpex-typography-customize-preview',
				wpex_asset_url( 'js/dynamic/wpex-typography-customize-preview.js' ),
				array( 'customize-preview' ),
				WPEX_THEME_VERSION,
				true
			);

			wp_localize_script( 'wpex-typography-customize-preview', 'wpexTypo', array(
				'googleFontsUrl'    => wpex_get_google_fonts_url(),
				'googleFontsSuffix' => '100italic,200italic,300italic,400italic,500italic,600italic,700italic,800italic,100,200,300,400,500,600,700,800',
			) );
			
		}

		/**
		 * Register typography options to the Customizer
		 *
		 * @since 1.6.0
		 */
		public static function register ( $wp_customize ) {

			// Get elements
			$elements = self::elements();

			// Return if elements are empty. This check is needed due to the filter added above
			if ( empty( $elements ) ) {
				return;
			}

			// Add General Panel
			$wp_customize->add_panel( 'wpex_typography', array(
				'priority' => 142,
				'capability' => 'edit_theme_options',
				'title' => __( 'Typography', 'total' ),
			) );

			// Add General Tab with font smoothing
			$wp_customize->add_section( 'wpex_typography_general' , array(
				'title' => __( 'General', 'total' ),
				'priority' => 1,
				'panel' => 'wpex_typography',
			) );

			// Font Smoothing
			$wp_customize->add_setting( 'enable_font_smoothing', array(
				'type' => 'theme_mod',
				'sanitize_callback' => false,
			) );
			$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'enable_font_smoothing', array(
				'label' => __( 'Font Smoothing', 'total' ),
				'section' => 'wpex_typography_general',
				'settings' => 'enable_font_smoothing',
				'priority' => 1,
				'type' => 'checkbox',
				'description' => __( 'Enable font-smoothing site wide. This makes fonts look a little "skinner".', 'total' ),
			) ) );

			// Font Smoothing
			if ( ! wpex_disable_google_services() ) {

				// Load fonts in barter
				$wp_customize->add_setting( 'google_fonts_in_barter', array(
					'type' => 'theme_mod',
					'sanitize_callback' => false,
				) );

				$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'google_fonts_in_barter', array(
					'label' => __( 'Load Fonts After The Body Tag', 'total' ),
					'section' => 'wpex_typography_general',
					'settings' => 'google_fonts_in_barter',
					'priority' => 1,
					'type' => 'checkbox',
				) ) );

				// Select subsets
				$wp_customize->add_setting( 'google_font_subsets', array(
					'type' => 'theme_mod',
					'default' => 'latin',
					'sanitize_callback' => false,
				) );
				$wp_customize->add_control( new WPEX_Customize_Multicheck_Control( $wp_customize, 'google_font_subsets', array(
					'label' => __( 'Font Subsets', 'total' ),
					'section' => 'wpex_typography_general',
					'settings' => 'google_font_subsets',
					'priority' => 2,
					'choices' => array(
						'latin' => 'latin',
						'latin-ext' => 'latin-ext',
						'cyrillic' => 'cyrillic',
						'cyrillic-ext' => 'cyrillic-ext',
						'greek' => 'greek',
						'greek-ext' => 'greek-ext',
						'vietnamese' => 'vietnamese',
					),
				) ) );

				// Load custom font 1
				$wp_customize->add_setting( 'load_custom_google_font_1', array(
					'type' => 'theme_mod',
					'sanitize_callback' => 'esc_html',
				) );
				$wp_customize->add_control( new WPEX_Fonts_Dropdown_Custom_Control( $wp_customize, 'load_custom_google_font_1', array(
						'label' => __( 'Load Custom Font', 'total' ),
						'section' => 'wpex_typography_general',
						'settings' => 'load_custom_google_font_1',
						'type' => 'wpex-font-family',
						'description' => __( 'Allows you to load a custom font site wide for use with custom CSS.', 'total' ),
					)
				) );

			}

			// Save translations in memory
			// Beacause we loop through all settings this way we don't have to call the __() function multiple times
			// and the strings can be translated prior to the loop.
			$s_family         = __( 'Font Family', 'total' );
			$s_style          = __( 'Font Style', 'total' );
			$s_default        = __( 'Default', 'total' );
			$s_xlight         = __( 'Extra Light: 100', 'total' );
			$s_light          = __( 'Light: 200', 'total' );
			$s_weight         = __( 'Font Weight', 'total' );
			$s_weight_desc    = __( 'Note: Not all Fonts support every font weight style.', 'total' );
			$s_300            = __( 'Book: 300', 'total' );
			$s_400            = __( 'Normal: 400', 'total' );
			$s_500            = __( 'Medium: 500', 'total' );
			$s_600            = __( 'Semibold: 600', 'total' );
			$s_700            = __( 'Bold: 700', 'total' );
			$s_800            = __( 'Extra Bold: 800', 'total' );
			$s_900            = __( 'Black: 900', 'total' );
			$s_normal         = __( 'Normal', 'total' );
			$s_italic         = __( 'Italic', 'total' );
			$s_capitalize     = __( 'Capitalize', 'total' );
			$s_lowercase      = __( 'Lowercase', 'total' );
			$s_uppercase      = __( 'Uppercase', 'total' );
			$s_em_px          = __( 'Value in px or em.', 'total' );
			$s_transform      = __( 'Text Transform', 'total' );
			$s_size           = __( 'Font Size', 'total' );
			$s_color          = __( 'Font Color', 'total' );
			$s_line_height    = __( 'Line Height', 'total' );
			$s_letter_spacing = __( 'Letter Spacing', 'total' );
			$s_margin         = __( 'Margin', 'total' );
			$s_margin_desc    = __( 'Please use the following format: top right bottom left.', 'total' );

			// Lopp through elements
			$count = '1';
			foreach( $elements as $element => $array ) {
				$count++;

				// Get label
				$label              = ! empty( $array['label'] ) ? $array['label'] : null;
				$exclude_attributes = ! empty( $array['exclude'] ) ? $array['exclude'] : false;
				$active_callback    = ! empty( $array['active_callback'] ) ? $array['active_callback'] : null;
				$description        = ! empty( $array['description'] ) ? $array['description'] : '';
				$transport          = ! empty( $array['transport'] ) ? $array['transport'] : 'postMessage';

				// Get attributes
				if ( ! empty ( $array['attributes'] ) ) {
					$attributes = $array['attributes'];
				} else {
					$attributes = array(
						'font-family',
						'font-weight',
						'font-style',
						'text-transform',
						'font-size',
						'line-height',
						'letter-spacing',
						'font-color',
					);
				}

				// Allow for margin on this attribute
				if ( isset( $array['margin'] ) ) {
					$attributes[] = 'margin';
				}

				// Set keys equal to vals
				$attributes = array_combine( $attributes, $attributes );

				// Exclude attributes for specific options
				if ( $exclude_attributes ) {
					foreach ( $exclude_attributes as $key => $val ) {
						unset( $attributes[ $val ] );
					}
				}

				// Register new setting if label isn't empty
				if ( $label ) {

					// Define Section
					$wp_customize->add_section( 'wpex_typography_'. $element , array(
						'title'       => $label,
						'priority'    => $count,
						'panel'       => 'wpex_typography',
						'description' => $description
					) );

					// Font Family
					if ( in_array( 'font-family', $attributes ) ) {

						// Get default
						$default = ! empty( $array['defaults']['font-family'] ) ? $array['defaults']['font-family'] : NULL;

						// Add setting
						$wp_customize->add_setting( $element .'_typography[font-family]', array(
							'type'              => 'theme_mod',
							'default'           => $default,
							'transport'         => $transport,
							'sanitize_callback' => false,
						) );

						// Add Control
						$wp_customize->add_control( new WPEX_Fonts_Dropdown_Custom_Control( $wp_customize, $element .'_typography[font-family]', array(
								'type'            => 'wpex-font-family',
								'label'           => $s_family,
								'section'         => 'wpex_typography_'. $element,
								'settings'        => $element .'_typography[font-family]',
								'priority'        => 1,
								'active_callback' => $active_callback,
						) ) );

					}

					// Font Weight
					if ( in_array( 'font-weight', $attributes ) ) {
						$wp_customize->add_setting( $element .'_typography[font-weight]', array(
							'type'              => 'theme_mod',
							'sanitize_callback' => false,
							'transport'         => $transport,
						) );
						$wp_customize->add_control( $element .'_typography[font-weight]', array(
							'label'           => $s_weight,
							'section'         => 'wpex_typography_'. $element,
							'settings'        => $element .'_typography[font-weight]',
							'priority'        => 2,
							'type'            => 'select',
							'active_callback' => $active_callback,
							'choices' => array(
								''    => $s_default,
								'100' => $s_xlight,
								'200' => $s_light,
								'300' => $s_300,
								'400' => $s_400,
								'500' => $s_500,
								'600' => $s_600,
								'700' => $s_700,
								'800' => $s_800,
								'900' => $s_900,
							),
							'description' => $s_weight_desc,
						) );
					}

					// Font Style
					if ( in_array( 'font-style', $attributes ) ) {
						$wp_customize->add_setting( $element .'_typography[font-style]', array(
							'type' => 'theme_mod',
							'sanitize_callback' => false,
							'transport' => $transport,
						) );
						$wp_customize->add_control( $element .'_typography[font-style]', array(
							'label' => $s_style,
							'section' => 'wpex_typography_'. $element,
							'settings' => $element .'_typography[font-style]',
							'priority' => 3,
							'type' => 'select',
							'active_callback' => $active_callback,
							'choices' => array(
								'' => $s_default,
								'normal' => $s_normal,
								'italic' => $s_italic,
							),
						) );
					}

					// Text-Transform
					if ( in_array( 'text-transform', $attributes ) ) {
						$wp_customize->add_setting( $element .'_typography[text-transform]', array(
							'type' => 'theme_mod',
							'sanitize_callback' => false,
							'transport' => $transport,
						) );
						$wp_customize->add_control( $element .'_typography[text-transform]', array(
							'label' => $s_transform,
							'section' => 'wpex_typography_'. $element,
							'settings' => $element .'_typography[text-transform]',
							'priority' => 4,
							'type' => 'select',
							'active_callback' => $active_callback,
							'choices' => array(
								'' => $s_default,
								'capitalize' => $s_capitalize,
								'lowercase' => $s_lowercase,
								'uppercase' => $s_uppercase,
							),
						) );
					}

					// Font Size
					if ( in_array( 'font-size', $attributes ) ) {
						$wp_customize->add_setting( $element .'_typography[font-size]', array(
							'type' => 'theme_mod',
							'sanitize_callback' => false,
							'transport' => $transport,
						) );
						$wp_customize->add_control( $element .'_typography[font-size]', array(
							'label' => $s_size,
							'section' => 'wpex_typography_'. $element,
							'settings' => $element .'_typography[font-size]',
							'priority' => 5,
							'type' => 'text',
							'description' => $s_em_px,
							'active_callback' => $active_callback,
						) );
					}

					// Font Color
					if ( in_array( 'font-color', $attributes ) ) {
						$wp_customize->add_setting( $element .'_typography[color]', array(
							'type' => 'theme_mod',
							'default' => '',
							'sanitize_callback' => false,
							'transport' => $transport,
						) );
						$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $element .'_typography_color', array(
							'label' => $s_color,
							'section' => 'wpex_typography_'. $element,
							'settings' => $element .'_typography[color]',
							'priority' => 6,
							'active_callback' => $active_callback,
						) ) );
					}

					// Line Height
					if ( in_array( 'line-height', $attributes ) ) {
						$wp_customize->add_setting( $element .'_typography[line-height]', array(
							'type' => 'theme_mod',
							'sanitize_callback' => false,
							'transport' => $transport,
						) );
						$wp_customize->add_control( $element .'_typography[line-height]',
							array(
								'label' => $s_line_height,
								'section' => 'wpex_typography_'. $element,
								'settings' => $element .'_typography[line-height]',
								'priority' => 7,
								'type' => 'text',
								'active_callback' => $active_callback,
						) );
					}

					// Letter Spacing
					if ( in_array( 'letter-spacing', $attributes ) ) {
						$wp_customize->add_setting( $element .'_typography[letter-spacing]', array(
							'type' => 'theme_mod',
							'sanitize_callback' => false,
							'transport' => $transport,
						) );
						$wp_customize->add_control( new WP_Customize_Control( $wp_customize, $element .'_typography_letter_spacing', array(
							'label' => $s_letter_spacing,
							'section' => 'wpex_typography_'. $element,
							'settings' => $element .'_typography[letter-spacing]',
							'priority' => 8,
							'type' => 'text',
							'active_callback' => $active_callback,
							'description' => $s_em_px,
						) ) );
					}

					// Margin
					if ( in_array( 'margin', $attributes ) ) {
						$wp_customize->add_setting( $element .'_typography[margin]', array(
							'type' => 'theme_mod',
							'sanitize_callback' => false,
							'transport' => $transport,
						) );
						$wp_customize->add_control( $element .'_typography[margin]',
							array(
								'label' => $s_margin,
								'section' => 'wpex_typography_'. $element,
								'settings' => $element .'_typography[margin]',
								'priority' => 9,
								'type' => 'text',
								'active_callback' => $active_callback,
								'description' => $s_margin_desc,
						) );
					}

				}
			}

		}

		/**
		 * Loop through settings
		 *
		 * @since 1.6.0
		 */
		public static function loop( $return = 'css' ) {

			// Define Vars
			$css            = '';
			$fonts          = array();
			$preview_styles = array();
			$elements       = self::elements();

			// Loop through each elements that need typography styling applied to them
			foreach( $elements as $element => $array ) {

				// Add empty css var
				$add_css = '';

				// Get target and current mod
				$target  = isset( $array['target'] ) ? $array['target'] : '';
				$get_mod = wpex_get_mod( $element .'_typography' );

				// Attributes to loop through
				if ( ! empty( $array['attributes'] ) ) {
					$attributes = $array['attributes'];
				} else {
					$attributes = array(
						'font-family',
						'font-weight',
						'font-style',
						'font-size',
						'color',
						'line-height',
						'letter-spacing',
						'text-transform',
						'margin',
					);
				}

				// Loop through attributes
				foreach ( $attributes as $attribute ) {

					// Define val
					$default = isset( $array['defaults'][$attribute] ) ? $array['defaults'][$attribute] : NULL;
					$val     = isset ( $get_mod[$attribute] ) ? $get_mod[$attribute] : $default;

					// If there is a value lets do something
					if ( $val ) {

						// Sanitize
						$val = str_replace( '"', '', $val );

						// Sanitize data
						$val = ( 'font-size' == $attribute ) ? wpex_sanitize_data( $val, 'font_size' ) : $val;
						$val = ( 'letter-spacing' == $attribute ) ? wpex_sanitize_data( $val, 'px' ) : $val;

						// Add quotes around font-family && font family to scripts array
						if ( 'font-family' == $attribute ) {
							$fonts[] = $val;
							if ( strpos( $val, '"' ) || strpos( $val, ',' ) ) {
								$val = $val;
							} else {
								$val = '"'. esc_html( $val ) .'"';
							}
						}

						// Add to inline CSS
						if ( 'css' == $return ) {
							$add_css .= $attribute .':'. $val .';';
						}

						// Customizer styles need to be added for each attribute
						elseif ( 'preview_styles' == $return ) {
							$preview_styles['customizer-typography-'. $element .'-'. $attribute] = $target .'{'. $attribute .':'. $val .';}';
						}

					}

				}

				// Front-end inline CSS
				if ( $add_css && 'css' == $return ) {
					$css .= $target .'{'. $add_css .'}';
				}

			}

			// Return CSS
			if ( 'css' == $return && $css ) {
				$css = '/*TYPOGRAPHY*/'. $css;
				return $css;
			}

			// Return styles
			if ( 'preview_styles' == $return && $preview_styles ) {
				return $preview_styles;
			}

			// Return Fonts Array
			if ( 'fonts' == $return && $fonts ) {
				return array_unique( $fonts ); // Return only 1 of each font
			}

		}

		/**
		 * Outputs the typography custom CSS
		 *
		 * @since 1.6.0
		 */
		public static function head_css( $output ) {
			$typography_css = self::loop( 'css' );
			if ( $typography_css ) {
				$output .= $typography_css;
			}
			return $output;
		}

		/**
		 * Returns correct CSS to output to wp_head
		 *
		 * @since 2.1.3
		 */
		public static function live_preview_styles() {
			$live_preview_styles = self::loop( 'preview_styles' );
			if ( is_array( $live_preview_styles ) ) {
				foreach ( $live_preview_styles as $key => $val ) {
					if ( ! empty( $val ) ) {
						echo '<style class="' . $key . '"> ' . $val . '</style>';
					}
				}
			}
		}

		/**
		 * Loads Google fonts via wp_enqueue_style
		 *
		 * @since 1.6.0
		 */
		public static function load_fonts() {
			$fonts = self::loop( 'fonts' );
			if ( is_array( $fonts ) ) {
				foreach ( $fonts as $font ) {
					wpex_enqueue_google_font( $font );
				}
			}
		}

		/**
		 * Add loaded fonts into the TinyMCE
		 *
		 * @since 1.6.0
		 */
		public static function mce_fonts( $initArray ) {

			// Get fonts from class
			$fonts = self::loop( 'fonts' );

			// Apply filters for child theme editing
			$fonts = apply_filters( 'wpex_mce_fonts', $fonts );

			// Sanitize to prevent issues with custom fonts
			$fonts = $fonts ? $fonts : array();

			// Declare fonts array to add to mce
			$fonts_array = array();

			// Add custom fonts
			if ( function_exists( 'wpex_add_custom_fonts' ) ) {
				$custom_fonts = wpex_add_custom_fonts();
				if ( $custom_fonts && is_array( $custom_fonts ) ) {
					$fonts = array_merge( $fonts, $custom_fonts );
				}
			}

			// Loop through fonts
			if ( $fonts ) {

				// Create new array of fonts
				foreach ( $fonts as $font ) {
					$fonts_array[] = $font .'=' . $font;
				}

				// Implode fonts array into a semicolon seperated list
				$fonts = implode( ';', $fonts_array );

				// Add Fonts To MCE
				if ( $fonts ) {

					$initArray['font_formats'] = $fonts .';Andale Mono=andale mono,times;Arial=arial,helvetica,sans-serif;Arial Black=arial black,avant garde;Book Antiqua=book antiqua,palatino;Comic Sans MS=comic sans ms,sans-serif;Courier New=courier new,courier;Georgia=georgia,palatino;Helvetica=helvetica;Impact=impact,chicago;Symbol=symbol;Tahoma=tahoma,arial,helvetica,sans-serif;Terminal=terminal,monaco;Times New Roman=times new roman,times;Trebuchet MS=trebuchet ms,geneva;Verdana=verdana,geneva;Webdings=webdings;Wingdings=wingdings,zapf dingbats';

				}

			}

			// Return hook array
			return $initArray;

		}

		/**
		 * Add loaded fonts to the sourcode in the admin so it can display in the editor
		 *
		 * @since 1.6.0
		 */
		public static function mce_scripts() {

			// Get Google fonts
			$google_fonts = wpex_google_fonts_array();

			// For google fonts only so return if none are defined
			if ( ! $google_fonts ) {
				return;
			}

			// Get fonts
			$fonts = self::loop( 'fonts' );

			// Apply filters
			$fonts = apply_filters( 'wpex_mce_fonts', $fonts );

			// Check
			if ( empty( $fonts ) || ! is_array( $fonts ) ) {
				return;
			}

			// Add Google fonts to tinymce
			foreach ( $fonts as $font ) {
				if ( ! in_array( $font, $google_fonts ) ) {
					continue;
				}
				$subset = wpex_get_mod( 'google_font_subsets', 'latin' );
				$subset = $subset ? $subset : 'latin';
				$subset = '&amp;subset='. $subset;
				$font   = wpex_get_google_fonts_url() .'/css?family='. str_replace(' ', '%20', $font ) .':300italic,400italic,600italic,700italic,800italic,400,300,600,700,800'. $subset;
				$style  = str_replace( ',', '%2C', $font );
				add_editor_style( $style );
			}
		}

	}

	new WPEX_Typography();

}

/* Helper function generates customizer live preview js
// Better then looping through on every page load...same some time and allows for manually minifying
function wpex_generate_typography_customizer_live_preview_js() {
	$elements = WPEX_Typography::elements();
	$output = '';
	foreach( $elements as $element => $array ) {

		// Attributes to loop through - each attribute has it's own setting
		if ( ! empty( $array['settings'] ) ) {
			$attributes = $array['settings'];
		} else {
			$attributes = array(
				'font-family',
				'font-weight',
				'font-style',
				'font-size',
				'color',
				'line-height',
				'letter-spacing',
				'text-transform',
				'margin',
			);
		}
		$add_css = '';
		$target  = isset( $array['target'] ) ? $array['target'] : '';

		// Loop through attributes
		foreach ( $attributes as $attribute ) {

			// Generate style classname
			$style_class = 'customizer-typography-'. $element .'-'. $attribute;

			// Open js output
			$output .= 'api("'. $element .'_typography['. $attribute .']", function(value){value.bind(function(newval){';

			// These are the styles to add inside the style tag
			$styles = $target .'{'. $attribute .': \' + newval + \';';

			// Add font-family if it doesn't exist already
			if ( 'font-family' == $attribute ) {

				// Add script to header if google font
				$output .= 'if ( newval ) {
								var fontHandle = newval.trim().toLowerCase().replace( " ", "-" );
								var fontScriptID = "'. $style_class .'";
								var fontScriptHref = newval.replace( " ", "%20" );
								fontScriptHref = fontScriptHref.replace( ",", "%2C" );
								fontScriptHref = wpex.googleFontsUrl + "/css?family="+ newval + ":"+ wpex.googleFontsSuffix;
								if ( $( "#"+ fontScriptID +"" ).length ) {
									$( "#"+ fontScriptID +"" ).attr( "href", fontScriptHref );
								} else {
									$( "head" ).append(\'<link id="\' + fontScriptID +\'" rel="stylesheet" type="text/css" href="\'+ fontScriptHref +\'">\');
								}
							}
				';

			}

			// Output
			$output .= '
				var el = $( \'.'. $style_class .'\' );
				if ( newval ) {
					var style = \'<style class="'. $style_class .'">'. $styles .'</style>\';
					if ( el.length ) {
						el.replaceWith( style );
					} else {
						 $( "head" ).append( style );
					}
				} else {
					el.remove();
				}
			';

			// Close js output
			$output .= '});});';


		} // End attributes loop
	}
	$output = $output;
	echo $output;
	exit;
}
add_action( 'init', 'wpex_generate_typography_customizer_live_preview_js' ); // Needed for filters
*/