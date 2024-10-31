<?php

namespace Tussendoor\Couverts;

if (!defined('ABSPATH')) exit;

class AdminController {

    protected $plugin;
    protected $status;

    public function __construct($status)
    {
        $this->status = $status;
        $this->plugin = CouvertsPlugin::getInstance();

        add_action('admin_menu', array($this, 'createMenu'));
        add_action('admin_enqueue_scripts', array($this, 'loadScripts'));
    }

    /**
     * Load the CSS and JS required for the settings page in WP-Admin
     */
    public function loadScripts() {
        wp_register_style('pwsd_admin_style', $this->plugin->url . '/assets/css/admin.index.css', false, $this->plugin->version);
        wp_enqueue_style('pwsd_admin_style');

        wp_register_script('pwsd_admin_tabs', $this->plugin->url . '/assets/js/admin.tabs.js', array('jquery'), $this->plugin->version, true);
        wp_enqueue_script('pwsd_admin_tabs');

        wp_enqueue_script('jquery-ui-datepicker');
    }

    /**
     * Create the menu item for the settings page in WP-Admin
     */
    public function createMenu() {
        add_menu_page(
            'Couverts', 
            'Couverts', 
            'manage_options', 
            'couverts-settings', 
            array($this, 'createSettingsPage'), 
            'dashicons-store'
        );
    }

    /**
     * Load the HTML page for the settings page in WP-Admin
     */
    public function createSettingsPage() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        
        include($this->plugin->path . '/app/views/admin.index.php');
    }

    /**
     * Basic function to display error messages to the wp-admin user.
     * @param  String   $message    The message to be displayed
     * @param  String   $type       (Optional) The type of message
     * @return Response
     */
    public function displayErrorMessage($message, $type = 'updated') {
        $html = '<div id="message" class="message ' . $type . '"><p>' . $message . '</p></div>';
        add_action('admin_notices', function() use ($html) {
            echo $html;
        });
    }
}
