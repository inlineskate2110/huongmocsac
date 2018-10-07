<?php
/**
 * Widget instance of Bloks Framework.
 *
 * @author: Kend
 */
class Bloks_Widget
{
    private $_types = array();

    /**
     * Bloks_Widget constructor.
     */
    public function __construct()
    {
        add_action('init', array($this, 'init'));
    }

    /**
     * Method to init types
     */
    public function init()
    {
        $widgets = array(array(
            'value' => '',
            'text' => __('-- Select Widget --', BLOKS_TEXTDOMAIN)
        ));

        if (!empty($GLOBALS['wp_widget_factory'])) {
            foreach ($GLOBALS['wp_widget_factory']->widgets as $name => $instance) {
                $widgets[] = array(
                    'value' => $name,
                    'text' => $instance->name
                );
            }
        }
        $this->_types = $widgets;
    }

    /**
     * Method to get widget types
     *
     * @return array
     */
    public function getTypes()
    {
        return $this->_types;
    }
}
