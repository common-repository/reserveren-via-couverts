<?php
/**
 * Plugin Name:       Couverts
 * Plugin URI:        http://www.tussendoor.nl 
 * Description:       Wordpress Couverts Plugin for making reservations
 * Version:           1.0.5
 * Author:            Tussendoor internet & marketing
 * Author URI:        http://www.tussendoor.nl
 * Text Domain:       wp-couverts
 * Domain Path:       /lang
 * Tested up to:      4.8
 */

namespace Tussendoor\Couverts;

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */
/*   _______                           _                                                             */
/*  |__   __|                         | |                                                            */
/*     | |_   _ ___ ___  ___ _ __   __| | ___   ___  _ __                                            */
/*     | | | | / __/ __|/ _ \ '_ \ / _` |/ _ \ / _ \| '__|                                           */
/*     | | |_| \__ \__ \  __/ | | | (_| | (_) | (_) | |                                              */
/*     |_|\__,_|___/___/\___|_| |_|\__,_|\___/ \___/|_|                                              */
/*   _       _                       _                                   _        _   _              */
/*  (_)     | |                     | |     ___                         | |      | | (_)             */
/*   _ _ __ | |_ ___ _ __ _ __   ___| |_   ( _ )    _ __ ___   __ _ _ __| | _____| |_ _ _ __   __ _  */
/*  | | '_ \| __/ _ \ '__| '_ \ / _ \ __|  / _ \/\ | '_ ` _ \ / _` | '__| |/ / _ \ __| | '_ \ / _` | */
/*  | | | | | ||  __/ |  | | | |  __/ |_  | (_>  < | | | | | | (_| | |  |   <  __/ |_| | | | | (_| | */
/*  |_|_| |_|\__\___|_|  |_| |_|\___|\__|  \___/\/ |_| |_| |_|\__,_|_|  |_|\_\___|\__|_|_| |_|\__, | */
/*                                                                                             __/ | */
/*                                       www.tussendoor.nl                                    |___/  */
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

class CouvertsPlugin {

    public $url;
    public $name;
    public $path;
    public $version;
    public $settings;
    public $partial_path;
    public static $textDomain;

    /**
     * Return one and only instance of this class.
     * @return CouvertsPlugin           Object
     */ 
    public static function getInstance()
    {
        static $instance = null;

        if ($instance === null) {
            $instance = new CouvertsPlugin();
            $instance->checkCompatibility();
            $instance->setProperties();
            $instance->setupPartials();
            $instance->loadDependencies();
            $instance->setupActions();
        }

        return $instance;
    }

    /**
     * Check the compatibility of the plugin with the current enviroment.
     */
    private function checkCompatibility()
    {
        if (!function_exists('phpversion') || phpversion() < 5.3) {
            $this->displayAdminMessage('<p><strong>Waarschuwing:</strong> Je gebruikt een lagere PHP versie dan aangeraden wordt voor de <em>Couverts plugin</em>. Hierdoor kan de plugin mogelijk niet (goed) werken.</p>');
        }

        if (!function_exists('curl_version')) {
            $this->displayAdminMessage('<p><strong>Waarschuwing:</strong> Het lijkt er op dat er geen cURL ge&iuml;nstalleerd is op deze server. Hierdoor kan de <em>Couverts plugin</em> mogelijk niet (goed) werken.</p>');
        }
    }

    /**
     * Setup the class properties
     */
    private function setProperties()
    {
        $this->url = plugins_url('reserveren-via-couverts');
        $this->name = 'Couverts';
        $this->path = __DIR__;
        $this->version = '1.0.5';
        self::$textDomain = 'tussendoorCouverts';
        $this->partial_path = $this->getPartialPath();
    }

    
    /** 
     * Loads all models/controllers automatically.
     */
    private function loadDependencies()
    {
        foreach (glob($this->path.'/app/models/*.php') as $filename) {
            require_once $filename;
        }

        foreach (glob($this->path.'/app/controllers/*.php') as $filename) {
            require_once $filename;
        }
    }

    /**
     * Setup the plugin. Load the settings model and watch for a change in settings.
     * After that register the shortcode.
     */
    private function setupActions()
    {
        $this->settings = new Settings();
        $admin = new AdminController($this->settings->monitorSave());
        
        $controller = new CouvertsController();
        $controller->registerShortcode();
    }

    /**
     * Setup the partials folder when needed. Checks if the partials folder exists and creates it if it doesn't.
     * This method temporarily changes the default error handler to our own. See $this->changeErrorHandler for more info.
     */
    private function setupPartials()
    {
        $themeDirectory = get_template_directory();
        $viewDirectory = $this->path.'/app/views/couverts-form-partials';
        set_error_handler(array($this, 'changeErrorHandler'));

        if (file_exists($themeDirectory.'/couverts-form-partials')) {
            restore_error_handler();
            return true;
        }

        $fileNames = array(
            'form.date.php',
            'form.error.php',
            'form.information.php',
            'form.selecttime.php',
            'form.thankyou.php',
        );

        try {
            if (mkdir($themeDirectory.'/couverts-form-partials', 0777, true)) {
                foreach ($fileNames as $fileName) {
                    copy($viewDirectory.'/'.$fileName, $themeDirectory.'/couverts-form-partials/'.$fileName);
                }
            }
        } catch (\Exception $e) {
            if (file_exists($themeDirectory.'/couverts-form-partials')) {
                rmdir($themeDirectory.'/couverts-form-partials');
            }
            $this->displayAdminMessage('<p>De <strong>Couverts plugin</strong> kon de template bestanden niet in je thema map installeren. Bekijk de <a href="'.admin_url('admin.php?page=couverts-settings&tab=settings-doc').'">plugin instellingen</a> voor meer informatie.</p>');
        }
        
        restore_error_handler();
    }

    /**
     * Return the active partial path. 
     * @return String                   The path to the partials we'll be using
     */ 
    public function getPartialPath()
    {
        $theme_dir = get_template_directory();

        if (file_exists($theme_dir.'/couverts-form-partials')) {
            return $theme_dir.'/couverts-form-partials';
        } else {
            return plugin_dir_path(__FILE__).'app/views/couverts-form-partials';
        }
    }

    /**
     * Unfortunatly, mkdir returns a warning when it can't create the directory and those are not catchable.
     * By changing the error handler we can throw and catch an Exception. Don't worry, we'll restore the default
     * error handler by the end of $this->setupPartials();
     * @param  Integer          $errno              The error number
     * @param  String           $errstr             Error message
     * @param  String           $errfile            The file that has created the error
     * @param  Integer          $errline            The line on which the error was encountered
     * @param  array            $errcontext 
     * @return Exception                            Throws an Exception when an error was encountered. Returns false when error_reporting is off.
     */
    public function changeErrorHandler($errno, $errstr, $errfile, $errline, array $errcontext)
    {
        if (error_reporting() === 0) {
            return false;
        }

        throw new \Exception($errstr, 0);
    }

    /**
     * Wrapper method for displaying message in the admin area.
     * @param  String $message The message to display. 
     * @param  String $type    The type of message, changing the display of the message. For example 'error'.
     */
    private function displayAdminMessage($message, $type = 'error')
    {
        $message = '<div id="message" class="message '.$type.'">'.$message.'</div>';
        add_action('admin_notices', function() use ($message) {
            echo $message;
        });
    }

}

$plugin = CouvertsPlugin::getInstance();
