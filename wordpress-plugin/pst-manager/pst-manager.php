<?php
/**
 * Plugin Name:  PST Manager
 * Description:  Centralized management of Persian Standard Typography
 *               fonts, CDN integration, Elementor settings, and custom CSS.
 * Version:      1.0.0
 * Author:       Parsa Hafezalkotob
 * Text Domain:  pst-manager
 * Domain Path:  /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'PST_MANAGER_VERSION', '1.0.0' );
define( 'PST_MANAGER_OPTION_GROUP', 'pst_manager_options_group' );
define( 'PST_MANAGER_OPTION_NAME', 'pst_manager_options' );

class PST_Manager {

    public function __construct() {
        add_action( 'plugins_loaded', [ $this, 'load_textdomain' ] );
        add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
        add_action( 'admin_init', [ $this, 'register_settings' ] );

        // لینک تنظیمات در صفحه افزونه‌ها
        add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), [ $this, 'add_settings_link' ] );

        // main.js همیشه بارگذاری می‌شود (در فوتر)
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_main_js' ], 998 );

        // main.css همیشه بارگذاری می‌شود
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_main_css' ], 999 );

        // فونت‌های انتخابی
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_fonts_css' ], 1000 );

        // متغیرهای فونت (--font-primary, --font-secondary)
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_font_variables_css' ], 1001 );

        // استایل سفارشی کاربر
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_user_custom_css' ], 1002 );

        // Elementor
        if ( $this->is_elementor_active() ) {
            add_filter( 'elementor/fonts/groups', [ $this, 'add_font_group' ] );
            add_filter( 'elementor/fonts/additional_fonts', [ $this, 'add_fonts_to_list' ] );
        }
    }

    public function load_textdomain() {
        load_plugin_textdomain( 'pst-manager', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
    }

    public function is_elementor_active() {
        return class_exists( '\Elementor\Plugin' );
    }

    // ══════════════════════════════════════
    // لینک تنظیمات در صفحه افزونه‌ها
    // ══════════════════════════════════════
    public function add_settings_link( $links ) {
        $settings_link = '<a href="' . admin_url( 'options-general.php?page=pst-manager' ) . '">' . __( 'Settings', 'pst-manager' ) . '</a>';
        array_unshift( $links, $settings_link );
        return $links;
    }

    // ══════════════════════════════════════
    // main.js
    // ══════════════════════════════════════

    public function enqueue_main_js() {
        if ( is_admin() ) return;

        $options  = get_option( PST_MANAGER_OPTION_NAME );
        $base_url = trailingslashit( $options['cdn_base_url'] ?? 'https://cdn.cdoc.ir/pst' );

        wp_enqueue_script(
            'pst-main',
            $base_url . 'js/main.js',
            [],
            PST_MANAGER_VERSION,
            true // در فوتر بارگذاری شود
        );
    }

    // ══════════════════════════════════════
    // main.css
    // ══════════════════════════════════════

    public function enqueue_main_css() {
        if ( is_admin() ) return;

        $options  = get_option( PST_MANAGER_OPTION_NAME );
        $base_url = trailingslashit( $options['cdn_base_url'] ?? 'https://cdn.cdoc.ir/pst' );

        wp_enqueue_style(
            'pst-main',
            $base_url . 'css/main.css',
            [],
            PST_MANAGER_VERSION
        );
    }

    // ══════════════════════════════════════
    // فونت‌ها
    // ══════════════════════════════════════

    public function enqueue_fonts_css() {
        if ( is_admin() ) return;

        $options = get_option( PST_MANAGER_OPTION_NAME );
        if ( empty( $options['fonts'] ) ) return;

        $base_url = trailingslashit( $options['cdn_base_url'] ?? 'https://cdn.cdoc.ir/pst' );
        $css      = "/* PST Fonts Import */\n";
        foreach ( (array) $options['fonts'] as $font ) {
            $css .= "@import url('" . esc_url( $base_url . 'fonts/' . $font . '/font-face.css' ) . "');\n";
        }

        wp_register_style( 'pst-fonts', false );
        wp_enqueue_style( 'pst-fonts' );
        wp_add_inline_style( 'pst-fonts', $css );
    }

    // ══════════════════════════════════════
    // متغیرهای فونت (فقط font-primary و font-secondary)
    // ══════════════════════════════════════

    public function enqueue_font_variables_css() {
        if ( is_admin() ) return;

        $options = get_option( PST_MANAGER_OPTION_NAME );
        $css     = $this->generate_font_variables_css( $options );
        if ( $css ) {
            wp_register_style( 'pst-font-vars', false );
            wp_enqueue_style( 'pst-font-vars' );
            wp_add_inline_style( 'pst-font-vars', $css );
        }
    }

    /**
     * Mapping font slug to original font-family name
     */
    private function get_font_family( $slug ) {
        $map = [
            'IranSansX'  => 'IranSansX',
            'IranYekanX' => 'IranYekanX',
            'AbarLow'    => 'Abar Low',
            'AbarMid'    => 'Abar Mid',
            'AbarHigh'   => 'Abar High',
            'Vazirmatn'  => 'Vazirmatn',
        ];
        return $map[ $slug ] ?? '';
    }

    private function generate_font_variables_css( $options ) {
        $css = "\n:root {\n";
        $has_any = false;

        if ( ! empty( $options['font_primary'] ) ) {
            $family = $this->get_font_family( $options['font_primary'] );
            if ( $family ) {
                $css .= "  --font-primary: '" . esc_attr( $family ) . "', sans-serif;\n";
                $has_any = true;
            }
        }
        if ( ! empty( $options['font_secondary'] ) ) {
            $family = $this->get_font_family( $options['font_secondary'] );
            if ( $family ) {
                $css .= "  --font-secondary: '" . esc_attr( $family ) . "', sans-serif;\n";
                $has_any = true;
            }
        }
        $css .= "}\n";

        return $has_any ? $css : '';
    }

    // ══════════════════════════════════════
    // CSS سفارشی کاربر
    // ══════════════════════════════════════

    public function enqueue_user_custom_css() {
        if ( is_admin() ) return;

        $options = get_option( PST_MANAGER_OPTION_NAME );
        if ( ! empty( $options['custom_css_code'] ) ) {
            wp_register_style( 'pst-user-custom', false );
            wp_enqueue_style( 'pst-user-custom' );
            wp_add_inline_style( 'pst-user-custom', $options['custom_css_code'] );
        }
    }

    // ══════════════════════════════════════
    // Elementor
    // ══════════════════════════════════════

    public function add_font_group( $font_groups ) {
        $options = get_option( PST_MANAGER_OPTION_NAME );
        if ( ! empty( $options['elementor_inject_fonts'] ) && ! empty( $options['fonts'] ) ) {
            $font_groups['pst'] = __( 'PST Fonts', 'pst-manager' );
        }
        return $font_groups;
    }

    public function add_fonts_to_list( $fonts ) {
        $options = get_option( PST_MANAGER_OPTION_NAME );
        if ( ! empty( $options['elementor_inject_fonts'] ) && ! empty( $options['fonts'] ) ) {
            foreach ( $options['fonts'] as $slug ) {
                $family = $this->get_font_family( $slug );
                if ( $family ) {
                    $fonts[ $family ] = 'pst';
                }
            }
        }
        return $fonts;
    }

    // ══════════════════════════════════════
    // صفحه تنظیمات
    // ══════════════════════════════════════

    public function add_admin_menu() {
        add_options_page(
            __( 'PST Manager', 'pst-manager' ),
            __( 'PST Manager', 'pst-manager' ),
            'manage_options',
            'pst-manager',
            [ $this, 'settings_page_html' ]
        );
    }

    public function settings_page_html() {
        if ( ! current_user_can( 'manage_options' ) ) return;

        $options = get_option( PST_MANAGER_OPTION_NAME );
        ?>
        <div class="wrap">
            <h1><?php _e( 'PST Manager', 'pst-manager' ); ?></h1>

            <form method="post" action="options.php">
                <?php settings_fields( PST_MANAGER_OPTION_GROUP ); ?>
                <table class="form-table">
                    <!-- CDN Base URL -->
                    <tr>
                        <th scope="row"><?php _e( 'CDN Base URL', 'pst-manager' ); ?></th>
                        <td>
                            <input type="url" name="<?php echo PST_MANAGER_OPTION_NAME; ?>[cdn_base_url]"
                                   value="<?php echo esc_attr( $options['cdn_base_url'] ?? 'https://cdn.cdoc.ir/pst' ); ?>"
                                   class="regular-text" />
                        </td>
                    </tr>

                    <!-- انتخاب فونت‌ها -->
                    <tr>
                        <th scope="row"><?php _e( 'Required Fonts', 'pst-manager' ); ?></th>
                        <td>
                            <?php
                            $available_fonts = [
                                'IranSansX'  => [
                                    'label'  => __( 'IranSansX', 'pst-manager' ),
                                    'family' => 'IranSansX',
                                ],
                                'IranYekanX' => [
                                    'label'  => __( 'IranYekanX', 'pst-manager' ),
                                    'family' => 'IranYekanX',
                                ],
                                'AbarLow'    => [
                                    'label'  => __( 'Abar Low', 'pst-manager' ),
                                    'family' => 'Abar Low',
                                ],
                                'AbarMid'    => [
                                    'label'  => __( 'Abar Mid', 'pst-manager' ),
                                    'family' => 'Abar Mid',
                                ],
                                'AbarHigh'   => [
                                    'label'  => __( 'Abar High', 'pst-manager' ),
                                    'family' => 'Abar High',
                                ],
                                'Vazirmatn'  => [
                                    'label'  => __( 'Vazirmatn', 'pst-manager' ),
                                    'family' => 'Vazirmatn',
                                ],
                            ];
                            $selected_fonts = (array) ( $options['fonts'] ?? [] );
                            foreach ( $available_fonts as $font_slug => $font_data ) :
                                $checked = in_array( $font_slug, $selected_fonts ) ? 'checked' : '';
                            ?>
                                <label style="margin-right: 15px;">
                                    <input type="checkbox" name="<?php echo PST_MANAGER_OPTION_NAME; ?>[fonts][]"
                                           value="<?php echo esc_attr( $font_slug ); ?>" <?php echo $checked; ?> />
                                    <?php echo esc_html( $font_data['label'] ); ?>
                                </label>
                            <?php endforeach; ?>
                        </td>
                    </tr>

                    <!-- Elementor Fonts Injection -->
                    <?php if ( $this->is_elementor_active() ) : ?>
                    <tr>
                        <th scope="row"><?php _e( 'Elementor Fonts Injection', 'pst-manager' ); ?></th>
                        <td>
                            <label>
                                <input type="checkbox" name="<?php echo PST_MANAGER_OPTION_NAME; ?>[elementor_inject_fonts]"
                                       value="1" <?php checked( $options['elementor_inject_fonts'] ?? true ); ?> />
                                <?php _e( 'Add selected fonts to Elementor font list', 'pst-manager' ); ?>
                            </label>
                        </td>
                    </tr>
                    <?php endif; ?>

                    <!-- فونت اصلی -->
                    <tr>
                        <th scope="row"><?php _e( 'Primary Font', 'pst-manager' ); ?></th>
                        <td>
                            <select name="<?php echo PST_MANAGER_OPTION_NAME; ?>[font_primary]">
                                <option value=""><?php _e( 'None', 'pst-manager' ); ?></option>
                                <?php foreach ( $available_fonts as $font_slug => $font_data ) :
                                    $selected = ( $options['font_primary'] ?? '' ) === $font_slug ? 'selected' : '';
                                ?>
                                    <option value="<?php echo esc_attr( $font_slug ); ?>" <?php echo $selected; ?>><?php echo esc_html( $font_data['label'] ); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <!-- فونت دوم -->
                    <tr>
                        <th scope="row"><?php _e( 'Secondary Font', 'pst-manager' ); ?></th>
                        <td>
                            <select name="<?php echo PST_MANAGER_OPTION_NAME; ?>[font_secondary]">
                                <option value=""><?php _e( 'None', 'pst-manager' ); ?></option>
                                <?php foreach ( $available_fonts as $font_slug => $font_data ) :
                                    $selected = ( $options['font_secondary'] ?? '' ) === $font_slug ? 'selected' : '';
                                ?>
                                    <option value="<?php echo esc_attr( $font_slug ); ?>" <?php echo $selected; ?>><?php echo esc_html( $font_data['label'] ); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>

                    <!-- Custom CSS -->
                    <tr>
                        <th scope="row"><?php _e( 'Custom CSS', 'pst-manager' ); ?></th>
                        <td>
                            <textarea name="<?php echo PST_MANAGER_OPTION_NAME; ?>[custom_css_code]" rows="12" cols="80"
                                      class="large-text code" style="font-family: monospace;"
                                      placeholder="<?php esc_attr_e( 'Enter your custom CSS here…', 'pst-manager' ); ?>"
                            ><?php echo esc_textarea( $options['custom_css_code'] ?? '' ); ?></textarea>
                            <p class="description"><?php _e( 'This CSS will be loaded after all PST styles and can override any existing variables or rules.', 'pst-manager' ); ?></p>
                        </td>
                    </tr>

                </table>
                <?php submit_button( __( 'Save Settings', 'pst-manager' ) ); ?>
            </form>
        </div>
        <?php
    }

    // ══════════════════════════════════════
    // ثبت و پاک‌سازی تنظیمات
    // ══════════════════════════════════════

    public function register_settings() {
        register_setting(
            PST_MANAGER_OPTION_GROUP,
            PST_MANAGER_OPTION_NAME,
            [ 'sanitize_callback' => [ $this, 'sanitize_options' ] ]
        );
    }

    public function sanitize_options( $input ) {
        $sanitized = [];

        $sanitized['cdn_base_url']   = esc_url_raw( $input['cdn_base_url'] ?? 'https://cdn.cdoc.ir/pst' );
        $sanitized['fonts']          = array_map( 'sanitize_text_field', (array) ( $input['fonts'] ?? [] ) );
        $sanitized['font_primary']   = sanitize_text_field( $input['font_primary'] ?? '' );
        $sanitized['font_secondary'] = sanitize_text_field( $input['font_secondary'] ?? '' );

        $sanitized['elementor_inject_fonts'] = ! empty( $input['elementor_inject_fonts'] ) ? 1 : 0;
        $sanitized['custom_css_code']        = $input['custom_css_code'] ?? '';

        return $sanitized;
    }
}

new PST_Manager();