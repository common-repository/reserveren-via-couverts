<?php

namespace Tussendoor\Couverts;

if (!defined('ABSPATH')) exit;

class Settings {

    protected $plugin;
    private $options;

    public function __construct()
    {
        $this->plugin = CouvertsPlugin::getInstance();

        $options = array('couvertsBaseUrl', 'couvertsRestaurantId', 'couvertsApiKey');
        $this->options = $options;
    }

    /**
     * Get an option by it's name.
     * @param  String       $option         The name of the option
     * @return Mixed                        Depending on the option to retrieve, default false
     */
    public function get($option)
    {
        return get_option($option, false);
    }

    /**
     * A function to monitor for a set of specific $_POST requests
     * @return Array        Returns an array with a status (true/false) and a message explaining the status
     * @return Boolean      Returns false when there wasn't a POST request we're looking for.
     */
    public function monitorSave()
    {
        if (isset($_POST['couvertsSettingsSave']) && !empty($_POST['couvertsBaseUrl'])) {
            return $this->save();
        }

        return false;
    }

    /**
     * Save a specific set of $_POST fields to the database
     * @return Array        Returns an array with a status (true/false) and a message explaining the status
     */
    private function save()
    {
        if ($this->verifyPost('basicSettings') === false) {
            return array('status' => false, 'message' => 'Niet alle velden zijn (juist) ingevuld.');
        }

        foreach ($_POST as $name => $value) {
            $this->set($name, $value); //$this->set() does the validating and sanitation
        }
        
        return array('status' => true, 'message' => 'De instellingen zijn opgeslagen.');
    }

    /**
     * For updating individual options.
     * @param String        $name       The name of the option
     * @param String        $value      The value of the option
     */
    public function set($name, $value)
    {
        foreach ($this->options as $option) {
            if ($option === $name) {
                update_option($name, sanitize_text_field($value));
                return true;
            }
        }

        return false;
    }

    /**
     * Verify that the required fields are filled in.
     * @param  String           $type   The type of request
     * @return Boolean                  True on success, false on failure
     */
    private function verifyPost($type)
    {
        $requiredFields = array(
            'basicSettings' => array('couvertsBaseUrl', 'couvertsRestaurantId', 'couvertsApiKey'),
        );

        if (isset($requiredFields[$type])) {
            foreach ($requiredFields[$type] as $field) {
                if (empty($_POST[$field])) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

}
