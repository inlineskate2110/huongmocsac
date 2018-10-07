<?php

/**
 * Class Bloks_Admin_Settings
 *
 * @author: Kend
 */
class Bloks_Admin_Settings extends Bloks_Core_Settings_Base
{
    /**
     * Bloks_Admin_Settings constructor.
     */
    public function __construct()
    {
        parent::__construct();
        add_action('admin_menu', array($this, 'changeMenuLabel'), 9999);
    }

    /**
     * Method to change settings menu label
     */
    public function changeMenuLabel()
    {
        global $menu;

        $menu[100][0] = __('Bloks', BLOKS_TEXTDOMAIN);
    }

    /**
     * Method to prepare items to settings page
     */
    public function prepare()
    {
        $this->addGroup(
            array(
                'name'  => 'general',
                'title' => 'General'
            )
        );
        
        $this->addItem(
            'general',
            array(
                'name'      => 'google_api',
                'label'     => 'Google API',
                'type'      => 'text',
                'size'      => '60',
                'default'   => 'AIzaSyDxSO87LidBPfrBwcaQ4zCCXakxCrxuTFM'
            )
        );
    }
}
