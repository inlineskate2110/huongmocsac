<?php
/**
 * Options instance of Bloks Framework.
 *
 * @author: Kend
 */
abstract class Bloks_Core_Settings_Base
{
    const OPTION_KEY = 'bloks-options';

    protected $title = 'Settings';
    protected $items = array();
    protected $settings = array();

    /**
     * Bloks_Core_Settings_Base constructor.
     */
    public function __construct()
    {
        $this->settings = !get_option(self::OPTION_KEY)
            ? array() : get_option(self::OPTION_KEY);
        $this->prepare();
        add_action('admin_menu', array($this, 'registerMenu'));
        add_action('admin_init', array($this, 'initSettings'));
        add_action(
            'admin_enqueue_scripts',
            array($this, 'enqueueScripts')
        );
    }

    /**
     * Enqueues scripts and styles
     */
    public function enqueueScripts()
    {
        global $pagenow;

        if ($pagenow == 'admin.php' && $_GET['page'] == 'bloks_options') {
            wp_enqueue_script('jquery-ui-core');
            wp_enqueue_script('jquery-ui-tabs');
            wp_enqueue_style(
                'bloks-admin',
                BLOKS_ROOT_URL . 'assets/css/admin.css'
            );
        }
    }

    /**
     * Bloks register menu
     */
    public function registerMenu()
    {
        add_menu_page(
            __('Settings', BLOKS_TEXTDOMAIN),
            __('Settings', BLOKS_TEXTDOMAIN),
            'manage_options',
            'bloks_options',
            array($this, 'render'),
            'dashicons-tagcloud',
            100
        );
    }

    /**
     * Register and add settings
     */
    public function initSettings()
    {
        register_setting(
            self::OPTION_KEY, // Option group
            self::OPTION_KEY // Option name
        );
    }

    /**
     * Method to prepare items to settings page
     *
     * @return mixed
     */
    abstract function prepare();

    /**
     * Method to add settings group
     *
     * @param array $group
     */
    public function addGroup($group = array())
    {
        do_action(
            'bloks_settings_before_add_group_'.$group['name'],
            $this->items,
            $group
        );

        if(!isset($group['name'])) {
            wp_die(__('Settings group must be have a name.', BLOKS_TEXTDOMAIN));
        }

        if(isset($this->items[$group['name']])) {
            wp_die(__('Settings group is existed.', BLOKS_TEXTDOMAIN));
        }

        $this->items[$group['name']] = $group;

        do_action(
            'bloks_settings_after_add_group_'.$group['name'],
            $this->items,
            $group
        );
    }

    /**
     * Method to add setting item
     *
     * @param string|bool $group
     * @param array $item
     */
    public function addItem($group = false, $item = array())
    {
        do_action(
            'bloks_settings_before_add_item_'.$item['name'],
            $this->items,
            $item
        );

        if($group) {
            if(!isset($this->items[$group])) {
                wp_die(__('Group doesn\'t exists.', BLOKS_TEXTDOMAIN));
            }

            if(isset($this->items[$group]['items'][$item['name']])) {
                wp_die(__('Item is existed.', BLOKS_TEXTDOMAIN));
            }

            if(!class_exists('Bloks_Core_Settings_Elements_'.ucfirst($item['type']))) {
                wp_die(__('Item type doesn\'t exists.', BLOKS_TEXTDOMAIN));
            }

            if(isset($item['default']) && !$this->settings[$item['name']]) {
                $this->settings[$item['name']] = $item['default'];
                update_option(self::OPTION_KEY, $this->settings);
            }

            $class = 'Bloks_Core_Settings_Elements_'.ucfirst($item['type']);
            $item['instance'] = new $class(self::OPTION_KEY, $item);

            $this->items[$group]['items'][$item['name']] = $item;
        }

        do_action(
            'bloks_settings_after_add_item_'.$item['name'],
            $this->items,
            $item
        );
    }

    /**
     * Method to render settings page
     */
    public function render()
    {
        // @codingStandardsIgnoreStart
        ?>
        <div class="wrap bloks-theme-options">
            <h1><?php _e($this->title, BLOKS_TEXTDOMAIN) ?></h1>
            <form method="post" action="options.php" id="theme_options">
                <?php settings_fields(self::OPTION_KEY); ?>
                <?php do_settings_sections(self::OPTION_KEY); ?>
                <div id="<?php echo self::OPTION_KEY ?>-tabs">
                    <div class="bloks-wrap-tabs-nav">
                        <ul class="bloks-tabs-nav">
                        <?php foreach ($this->items as $group):?>
                            <li>
                                <a href="#<?php echo self::OPTION_KEY ?>-tabs-<?php echo $group['name']?>"><?php echo $group['title']?></a>
                            </li>
                        <?php endforeach;?>
                        </ul>
                    </div>
                    <div class="bloks-tab-content">
                        <div class="bloks-tab-content-header clear">
                            <h2 class="bloks-tab-title"></h2>
                            <button type="submit" class="button button-primary button-large"><?php _e('Update', BLOKS_TEXTDOMAIN) ?></button>
                        </div>
                        <?php foreach ($this->items as $group):?>
                        <div id="<?php echo self::OPTION_KEY ?>-tabs-<?php echo $group['name']?>"
                             class="bloks-tab-item animated fadeIn"
                        >
                            <table class="form-table">
                                <tbody>
                                <?php if(isset($group['items'])):?>
                                <?php foreach ($group['items'] as $item):?>
                                <?php $item['instance']->render();?>
                                <?php endforeach;?>
                                <?php endif;?>
                                </tbody>
                            </table>
                        </div>
                        <?php endforeach;?>
                    </div>
                </div>
            </form>
            <script type="text/javascript">
                jQuery(document).ready(function ($) {
                    $("#bloks-options-tabs").tabs();
                });
            </script>
        </div>
        <?php
        // @codingStandardsIgnoreEnd
    }
}
