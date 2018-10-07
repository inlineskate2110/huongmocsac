<?php

/**
 * Class Bloks_Theme_Admin_Custom
 *
 * @author: Kend
 */
class Bloks_Admin_CustomBlocks
{
    /**
     * Bloks theme custom constructor
     */
    public function __construct()
    {
        add_filter('transition_post_status', array($this, 'blocksStatus'), 10, 3);
        add_filter('admin_post_thumbnail_html', array($this, 'blocksThumbail'), 1, 3);
        add_action('save_post_custom_blocks', array($this, 'savePostBlocks'));
        add_action('edit_form_after_editor', array($this, 'blocksEditor'));
        add_action('edit_terms', array($this, 'blocksEditedCategory'), 10, 2);
        add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
        add_filter('submenu_file', array( $this, 'blockActiveSubMenu'));
        add_action('admin_menu', array( $this, 'registerMenu' ));
        add_filter('parent_file', array( $this, 'blockActiveParentMenu'));
    }

    /**
     * Enqueues scripts and styles
     */
    public function enqueueScripts()
    {
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-tabs');
        if (is_admin()) {
            wp_enqueue_script(
                'bloks-admin',
                BLOKS_ROOT_URL . 'assets/js/customblocks.js',
                array('jquery'),
                Bloks()->getVersion(),
                true
            );
            wp_enqueue_style(
                'bloks-admin',
                BLOKS_ROOT_URL . 'assets/css/admin.css',
                Bloks()->getVersion()
            );
        }
    }

    /**
     * Custom editor
     */
    public function blocksEditor()
    {
        $info = get_post();
        if (is_object($info) && isset($info->post_type)
            && ($info->post_type == 'custom_blocks')
        ) {

            $upload_dir = wp_upload_dir();
            $baseDir = $upload_dir['basedir'] . '/bloks/';
            $baseFolder = $baseDir . $info->ID;
            $dataHtml = $dataCss = $dataJs = '';
            // check folder
            if (is_dir($baseFolder)) {
                if (file_exists($baseFolder . '/index' . $info->ID . '.html')) {
                    $helper = new Bloks_Core_Block_Template_Processor(
                        $baseFolder . '/index' . $info->ID . '.html'
                    );
                    $dataHtml = $helper->getContent();
                }
                if (file_exists($baseFolder . '/stylesheet' . $info->ID . '.css')) {
                    $dataCss = file_get_contents(
                        $baseFolder . '/stylesheet' . $info->ID . '.css'
                    );
                }
                if (file_exists($baseFolder . '/javascript' . $info->ID . '.js')) {
                    $dataJs = file_get_contents(
                        $baseFolder . '/javascript' . $info->ID . '.js'
                    );
                }
            }

            // @codingStandardsIgnoreStart
            ?>
            <div class="blocks-custom-editor">
                <div id="blocks-custom-tabs">
                    <ul class="bloks-tabs-nav">
                        <li><a href="#blocks-custom-html"><?php _e('Html', BLOKS_TEXTDOMAIN) ?></a></li>
                        <li><a href="#blocks-custom-css"><?php _e('StyleSheet', BLOKS_TEXTDOMAIN) ?></a></li>
                        <li><a href="#blocks-custom-js"><?php _e('JavaScript', BLOKS_TEXTDOMAIN) ?></a></li>
                    </ul>
                    <div class="bloks-tab-content">
                        <div id="blocks-custom-html">
			            <textarea name="blocks_editor_html" id="" rows="60"
                                  class="large-text code-editor-html"><?php echo $dataHtml ?></textarea>
                        </div>
                        <div id="blocks-custom-css">
			            <textarea name="blocks_editor_css" id="" rows="60"
                                  class="large-text code-editor-css"><?php echo $dataCss ?></textarea>
                        </div>
                        <div id="blocks-custom-js">
			            <textarea name="blocks_editor_js" id="" rows="60"
                                  class="large-text code-editor-js"><?php echo $dataJs ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            // @codingStandardsIgnoreEnd
        }
    }

    /**
     * Hook function status post
     *
     * @param string $newStatus
     * @param string $oldStatus
     * @param object $post
     */
    public function blocksStatus($newStatus, $oldStatus, $post)
    {
        if (isset($post) && ($post->post_type == 'custom_blocks')
            && ($newStatus == 'publish')
        ) {
            $post->post_status = 'private';
            wp_update_post($post);
        }
    }

    /**
     * Hook function thumbnail post
     *
     * @param string $html
     * @param int $post_id
     * @param int $post_image_id
     *
     * @return string
     */
    public function blocksThumbail($html, $post_id, $post_image_id)
    {
        $change = 1;
        $upload_dir = wp_upload_dir();
        $baseDir = $upload_dir['basedir'] . '/bloks/';
        $baseFolder = $baseDir . $post_id;
        $screenshot = $baseFolder . '/screenshot.png';
        if (file_exists($screenshot)) {
            $id = get_post_thumbnail_id($post_id);
            $change = ($id == $post_image_id) ? 0 : 1;
        }
        if ($post_image_id && !empty($post_image_id)) {
            $html .= '<input  name="blocks_thumbnails_change" type="hidden" value="'
                . $change . '"/>';
        }

        return $html;
    }

    /**
     * Check save category
     *
     * @param int $term_id  Term ID.
     * @param string $taxonomy Taxonomy slug.
     */
    public function blocksEditedCategory($term_id, $taxonomy)
    {
        $term = get_term($term_id, $taxonomy);
        $categories = Bloks()->getBuilderFactory()->categories;
        if (!is_wp_error($term)) {
            if (in_array($term->slug, array_keys($categories))) {
                if ($taxonomy == 'blocks_category') {
                    wp_redirect(admin_url('edit.php?post_type=custom_blocks'));
                    exit;
                }
            }
        }
    }

    /**
     * Hook function save
     */
    public function savePostBlocks()
    {
        $data = $_POST;
        if (is_array($data) && isset($data['post_type'])
            && ($data['post_type'] == 'custom_blocks')
        ) {
            //start update data in files
            $upload_dir = wp_upload_dir();
            $baseDir = $upload_dir['basedir'] . '/bloks/';
            $baseFolder = $baseDir . $data['post_ID'];

            if (!is_dir($baseFolder)) {
                mkdir($baseFolder);
            }
            if (is_dir($baseFolder)) {
                $html = '<!--@name ' . $data['post_title'] . ' @-->' . PHP_EOL;
                if (isset($data['_thumbnail_id']) && ($data['_thumbnail_id'] != -1)) {
                    $html .= '<!--@screenshot ' . $upload_dir['baseurl']
                        . '/bloks/' . $data['post_ID']
                        . '/screenshot.png @-->' . PHP_EOL;
                }
                $html .= stripslashes($data['blocks_editor_html']);
                $openHtml = fopen(
                    $baseFolder . '/index' . $data['post_ID'] . '.html', 'w'
                );
                $openCss = fopen(
                    $baseFolder . '/stylesheet' . $data['post_ID'] . '.css', 'w'
                );
                $openJs = fopen(
                    $baseFolder . '/javascript' . $data['post_ID'] . '.js', 'w'
                );
                fwrite($openHtml, $html);
                fwrite($openCss, stripslashes($data['blocks_editor_css']));
                fwrite($openJs, stripslashes($data['blocks_editor_js']));
                fclose($openHtml);
                fclose($openCss);
                fclose($openJs);
                // check image thumbnails and copy
                if (isset($data['_thumbnail_id'])
                    && isset($data['blocks_thumbnails_change'])
                    && $data['blocks_thumbnails_change']
                ) {
                    $screenshot = $baseFolder . '/screenshot.png';
                    $infoThumbnail = image_get_intermediate_size($data['_thumbnail_id']);
                    if($infoThumbnail
                        && is_array($infoThumbnail)
                        && isset($infoThumbnail['path'])) {
                        $pathImage = $upload_dir['basedir'] . DS.$infoThumbnail['path'];
                    }
                    if (isset($pathImage)&& file_exists($pathImage)) {
                        copy($pathImage, $screenshot);
                    }
                }
            }
        }
    }
    /**
     * Custom active submenu
     *
     * @param string $submenu_file
     *
     * @return string
     */
    public function blockActiveSubMenu( $submenu_file )
    {
        if ($submenu_file == 'post-new.php?post_type=custom_blocks' ) {
            return 'edit.php?post_type=custom_blocks';
        } else {
            return $submenu_file;
        }
    }
    /**
     * Custom active Parent menu
     *
     * @param string $parent_file
     *
     * @return string
     */
    public function blockActiveParentMenu( $parent_file )
    {
        if ($parent_file == 'edit.php?post_type=custom_blocks' ) {
            return 'bloks_options';
        } else {
            return $parent_file;
        }
    }
    /**
     * Bloks register menu
     */
    public function registerMenu()
    {
        add_submenu_page(
            'bloks_options',
            __('Custom Blocks', BLOKS_TEXTDOMAIN),
            __('Custom Blocks', BLOKS_TEXTDOMAIN),
            'manage_options',
            'edit.php?post_type=custom_blocks'
        );
    }
}
