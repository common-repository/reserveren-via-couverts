<?php

namespace Tussendoor\Couverts;

if (!defined('ABSPATH')) exit;

class CouvertsWidget extends \WP_Widget {

    protected $plugin;

    public function __construct()
    {
        $this->plugin = CouvertsPlugin::getInstance();
        $widget_ops = array(
            'classname' => 'CouvertsWidget', 
            'description' => 'Het formulier van Couverts ingesloten in een widget.'
        );
        parent::__construct('CouvertsWidget', 'Couverts Widget', $widget_ops);
    }

    /**
     * Infomation displayed when activating the widget in wp-admin
     * @param  Array        $values         N/A
     * @return String
     */
    public function form($values) {
        ?>
            <p>De widget gebruikt de instellingen die je hebt ingevuld op de <a href="<?php echo admin_url('admin.php?page=couverts-settings&tab=settings-doc'); ?>">plugin pagina</a>.</p>
            <p>Aanvullende instellingen zijn niet nodig.</p>
        <?php 
    }

    /**
     * Displays the widget on the frondend. Basically a wrapper for the shortcode ;-),
     * @param  Array        $args           N/A
     * @param  Array        $values         N/A
     * @return String         
     */
    public function widget($args, $values)
    {
        extract($args, EXTR_SKIP);
        $widget = 'widget_'.$widget_id;
        echo do_shortcode('[couvertsForm]');
    }
}

add_action( 'widgets_init', create_function('', 'return register_widget("Tussendoor\Couverts\CouvertsWidget");') );

