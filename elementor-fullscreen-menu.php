<?php
/**
 * Plugin Name: Elementor Full Screen Menu
 * Description: A lightweight Elementor addon that adds a full screen menu element.
 * Plugin URI:  https://github.com/yourusername/elementor-fullscreen-menu
 * Version:     0.03
 * Author:      Your Name
 * Author URI:  https://yourwebsite.com
 * Text Domain: elementor-fullscreen-menu
 * Domain Path: /languages
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 * Elementor tested up to: 3.17.0
 * Elementor Pro tested up to: 3.17.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define plugin constants.
define( 'ELEMENTOR_FULLSCREEN_MENU_VERSION', '0.03' );
define( 'ELEMENTOR_FULLSCREEN_MENU_FILE', __FILE__ );
define( 'ELEMENTOR_FULLSCREEN_MENU_PATH', plugin_dir_path( __FILE__ ) );
define( 'ELEMENTOR_FULLSCREEN_MENU_URL', plugin_dir_url( __FILE__ ) );

/**
 * Main Elementor Full Screen Menu Class
 */
final class Elementor_Fullscreen_Menu {

    /**
     * Instance
     *
     * @var Elementor_Fullscreen_Menu The single instance of the class.
     */
    private static $_instance = null;

    /**
     * Instance
     *
     * Ensures only one instance of the class is loaded or can be loaded.
     *
     * @return Elementor_Fullscreen_Menu An instance of the class.
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'plugins_loaded', [ $this, 'init' ] );
    }

    /**
     * Initialize the plugin
     */
    public function init() {
        // Check if Elementor is installed and activated
        if ( ! did_action( 'elementor/loaded' ) ) {
            add_action( 'admin_notices', [ $this, 'admin_notice_missing_elementor' ] );
            return;
        }

        // Register widget
        add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
        
        // Register styles and scripts
        add_action( 'wp_enqueue_scripts', [ $this, 'register_assets' ] );
    }

    /**
     * Admin notice
     *
     * Warning when the site doesn't have Elementor installed or activated.
     */
    public function admin_notice_missing_elementor() {
        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }

        $message = sprintf(
            /* translators: 1: Plugin name 2: Elementor */
            esc_html__( '"%1$s" requires "%2$s" to be installed and activated.', 'elementor-fullscreen-menu' ),
            '<strong>' . esc_html__( 'Elementor Full Screen Menu', 'elementor-fullscreen-menu' ) . '</strong>',
            '<strong>' . esc_html__( 'Elementor', 'elementor-fullscreen-menu' ) . '</strong>'
        );

        printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
    }

    /**
     * Register Widgets
     *
     * Register new Elementor widgets.
     */
    public function register_widgets( $widgets_manager ) {
        // Include Widget file
        require_once( ELEMENTOR_FULLSCREEN_MENU_PATH . 'includes/widgets/fullscreen-menu-widget.php' );
        
        // Register widget
        $widgets_manager->register( new Elementor_Fullscreen_Menu_Widget() );
    }

/**
 * Register assets (CSS and JS)
 */
public function register_assets() {
    // Register styles
    wp_register_style(
        'elementor-fullscreen-menu',
        ELEMENTOR_FULLSCREEN_MENU_URL . 'assets/css/fullscreen-menu.css',
        [],
        ELEMENTOR_FULLSCREEN_MENU_VERSION
    );

    // Register scripts
    wp_register_script(
        'elementor-fullscreen-menu',
        ELEMENTOR_FULLSCREEN_MENU_URL . 'assets/js/fullscreen-menu.js',
        ['jquery'],
        ELEMENTOR_FULLSCREEN_MENU_VERSION,
        true
    );

    // Always enqueue them when not in admin
    if (!is_admin()) {
        wp_enqueue_style('elementor-fullscreen-menu');
        wp_enqueue_script('elementor-fullscreen-menu');
    }
}
}

// Initialize the plugin
Elementor_Fullscreen_Menu::instance();