<?php

/**
 * Builder instance of Bloks Framework.
 *
 * @author: Kend
 */
class Bloks_Builder
{
    private $_component = null;

    /**
     * List blocks
     *
     * @var array
     */
    public $categories = array(
        'image'         => 'Image',
        'grid'          => 'Grid',
        'text'          => 'Text',
        'form'          => 'Form',
        'testimonial'   => 'Testimonial',
        'google-maps'   => 'Google Maps',
        'widget'        => 'Widget',
        'tabs'          => 'Tabs'
    );

    /**
     * Bloks_Builder constructor.
     */
    public function __construct()
    {
        /**
         * Hooks to template include
         */
        add_filter(
            'template_include',
            array($this, 'templateInclude')
        );

        /**
         * Add Editor button to edit page form
         */
        add_action(
            'edit_form_after_title',
            array($this, 'addEditorButtonToEditForm')
        );

        /**
         * Hook generate bloks builder link
         */
        add_filter(
            'redirect_post_location',
            array($this, 'redirectPostLocation')
        );

        /**
         * Add builder link to page row actions
         */
        add_filter(
            'page_row_actions',
            array($this, 'addPageRowActions'),
            10,
            2
        );

        /**
         * Replace edit page link to Bloks Builder in admin bar menu
         */
        add_action(
            'admin_bar_menu',
            array($this, 'addAdminBarMenu'),
            999
        );

        /**
         * Hide admin bar in builder page
         */
        add_filter(
            'show_admin_bar',
            '__return_false'
        );

        add_action(
            'wp_enqueue_scripts',
            array($this, 'enqueueScripts')
        );

        /**
         * Hook the_content
         */
        add_filter(
            'the_content',
            array($this, 'filterTheContent')
        );

        /**
         * Register theme actions
         */
        new Bloks_Builder_Actions();
        new Bloks_Builder_CustomBlocks();
        $this->_component = new Bloks_Builder_Component();
    }

    /**
     * Method setup iframe for build layout
     *
     * @param string $template
     *
     * @return string
     */
    public function templateInclude($template)
    {
        if ($this->isActive()) {
            return BLOKS_ROOT_PATH . DS . 'builder.php';
        } else {
            return $template;
        }
    }

    /**
     * Enqueues scripts and styles
     */
    public function enqueueScripts()
    {
        global $pagenow;

        if ($pagenow != 'wp-login.php' && !is_admin()) {
            wp_enqueue_script(
                'bloks-script',
                BLOKS_ROOT_URL . 'assets/js/scripts.js',
                array('jquery'),
                Bloks()->getVersion(),
                true
            );

            wp_enqueue_style(
                'bloks',
                BLOKS_ROOT_URL . 'assets/css/styles.css',
                Bloks()->getVersion()
            );

            if($this->isIframe()) {
                wp_enqueue_script('jquery-ui-core');
                wp_enqueue_script('jquery-ui-slider');
                wp_enqueue_script('jquery-ui-sortable');
                wp_enqueue_media();

                wp_enqueue_script(
                    'bloks-iframe-script',
                    BLOKS_ROOT_URL . 'assets/js/iframe.js',
                    array('jquery'),
                    Bloks()->getVersion()
                );

                wp_enqueue_style(
                    'bloks_builder',
                    BLOKS_ROOT_URL . 'assets/css/builder.css',
                    Bloks()->getVersion()
                );
            }

            wp_enqueue_script(
                'google-map',
                'https://maps.googleapis.com/maps/api/js?key='
                .Bloks_Core_Settings::getOption('google_api')
            );
        }
    }

    /**
     * Method to get Component instance
     *
     * @return null|Bloks_Builder_Component
     */
    public function getComponent()
    {
        return $this->_component;
    }

    /**
     * Builder is active or not
     *
     * @return bool
     */
    public function isActive()
    {
        global $post;

        return (!is_admin()
            && $post
            && ( current_user_can('edit_page', $post->ID) )
            && (!get_option('page_for_posts')
                || $post->ID != get_option('page_for_posts'))
            && isset($_GET['bloks']));
    }

    /**
     * Method to check is in iframe or not
     *
     * @return bool
     */
    public function isIframe()
    {
        global $post;

        return (!is_admin()
            && $post
            && ( current_user_can('edit_page', $post->ID) )
            && (!get_option('page_for_posts')
                || $post->ID != get_option('page_for_posts'))
            && isset($_GET['iframe']));
    }

    /**
     * Add editor button to edit form
     *
     * @param stdClass $post
     */
    public function addEditorButtonToEditForm($post)
    {
        if ($post->post_type == 'page' && $post->ID != get_option('page_for_posts')) {
            ?>
            <input type="submit" name="bloks_builder"
                   value="<?php _e('Bloks Editor', BLOKS_TEXTDOMAIN) ?>"
                   class="button button-primary button-large button-bloks-builder"/>
            <?php

        }
    }

    /**
     * Redirect post
     *
     * @param string $location
     * @return false|string
     */
    public function redirectPostLocation($location)
    {
        if (isset($_GET['post'])) {
            $post_id = $post_ID = (int)$_GET['post'];
        } elseif (isset($_POST['post_ID'])) {
            $post_id = $post_ID = (int)$_POST['post_ID'];
        } else {
            $post_id = $post_ID = 0;
        }
        $link = get_permalink($post_id);
        $link .= strpos($link, '?') === false ? '?bloks' : '&bloks';
        if (isset($_POST['bloks_builder'])) {
            return $link;
        } else {
            return $location;
        }
    }

    /**
     * Method to add Bloks Builder link to page row actions
     *
     * @param array $actions
     * @param WP_Post $post
     * @return mixed
     */
    public function addPageRowActions($actions, $post)
    {
        if ($post->ID != get_option('page_for_posts')) {
            $link = get_permalink($post);
            $link .= strpos($link, '?') === false ? '?bloks' : '&bloks';
            $actions['edit bloks-builder'] = '<a href="' . $link . '" rel="permalink"'
                . 'title="' . __('Edit with Bloks Builder', BLOKS_TEXTDOMAIN) . '">'
                . __('Bloks Builder', BLOKS_TEXTDOMAIN) . '</a>';
        }

        return $actions;
    }

    /**
     * Replace edit page link to Bloks Builder in admin bar menu
     *
     * @param WP_Admin_Bar $wp_admin_bar
     */
    public function addAdminBarMenu($wp_admin_bar)
    {
        global $post;

        if (!is_admin() && $post->post_type == 'page') {
            $link = get_permalink($post);
            $link .= strpos($link, '?') === false ? '?bloks' : '&bloks';

            $args = array(
                'id' => 'edit',
                'title' => __('Bloks Builder', BLOKS_TEXTDOMAIN),
                'href' => $link
            );

            $wp_admin_bar->add_node($args);
        }
    }

    /**
     * Method to get theme color sets
     *
     * @return mixed|array
     */
    public function getColorSets()
    {
        $colorsets = array(
            'dark' => array(
                'title' => 'Dark',
                'class' => 'bloks__colorset-dark',
                'preview' => BLOKS_ROOT_URL
                    . 'assets/images/builder/colorsets/dark.svg'
            ),
            'none' => array(
                'title' => 'Light',
                'class' => 'bloks__colorset-light',
                'preview' => BLOKS_ROOT_URL
                    . 'assets/images/builder/colorsets/none.svg'
            )
        );

        return apply_filters('bloks_get_color_sets', $colorsets);
    }

    /**
     * Method prepare the_content
     *
     * @param string $content
     * @return string
     */
    public function filterTheContent( $content )
    {
        if(get_the_ID() && isset($_GET['iframe'])) {
            return '<div id="bloks-builder-content">'
                .get_post_meta(get_the_ID(), '_builder_content', true)
            .'</div>';
        }

        return $content;
    }
}
