<?php

/**
 * Class Bloks_Theme_Admin_Custom
 *
 * @author: Kend
 */
class Bloks_CustomBlocks
{
    /**
     * Bloks theme custom constructor
     */
    public function __construct()
    {
        /**
         * Register post type and taxonomy for Custom Blocks
         */
        add_action('init', array($this, 'register'));

        /**
         * Activation hook
         */
        register_activation_hook(
            BLOKS_ROOT_PATH . DS . 'bloks.php',
            array($this, 'inActivation')
        );

        /**
         * Register theme actions
         */
        new Bloks_Admin_CustomBlocks();
    }

    /**
     * Register activation hook
     */
    public function inActivation()
    {
        $this->register();
        // Create folder theme
        $upload_dir = wp_upload_dir();
        $baseDir = $upload_dir['basedir'] . '/bloks/';
        if (!is_dir($baseDir)) {
            mkdir($baseDir);
        }

        // check block categories default
        $categories = Bloks()->getBuilderFactory()->categories;
        foreach ($categories as $key => $title) {
            $term = get_term_by('slug', $key, 'blocks_category');
            if ($term == false) {
                wp_insert_term(
                    $title,
                    'blocks_category',
                    array(
                        'slug' => $key
                    )
                );
            }
        }
    }

    /**
     * Bloks create post type
     */
    public function register()
    {
        register_taxonomy(
            'blocks_category',
            'custom_blocks', array(
                'hierarchical' => true,
                'labels' => '',
                'show_ui' => true,
                'show_in_nav_menus' => false,
                'show_admin_column' => false,
                'query_var' => true,
                'rewrite' => array('slug' => 'blocks_category'),
            )
        );

        register_post_type(
            'custom_blocks',
            array(
                'labels' => array(
                    'name' => __('Custom Blocks'),
                    'singular_name' => __('CustomBlocks'),
                    'menu_position' => null,
                    'edit_item' => 'Edit Block',
                    'add_new_item' => 'Add New Custom Block'
                ),
                'public' => true,
                'show_in_menu' => false,
                'has_archive' => false,
                'taxonomies' => array('blocks_category'),
                'supports' => array('title', 'thumbnail'),
            )
        );
    }
}
