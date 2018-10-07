<?php

/**
* Builder Actions instance of Bloks Framework.
*
* @author: Kend
*/
class Bloks_Builder_Actions
{
    /**
     * Bloks_Builder_Actions constructor.
     */
    public function __construct()
    {
        add_action('wp_ajax_bloks-update-options', array($this, 'updateOptions'));
        add_action('wp_ajax_bloks-update-page', array($this, 'updatePage'));
        add_action('wp_ajax_bloks-get-widget-form', array($this, 'getWidgetForm'));
        add_action('wp_ajax_bloks-get-widget-content', array($this, 'getWidgetContent'));
        add_action('wp_ajax_bloks-get-sample-link', array($this, 'getSampleLink'));
    }

    /**
     * Method to get sample link action
     */
    public function getSampleLink()
    {
        $result = array();
        if (!isset($_POST['page_id']) || !$_POST['page_id']) {
            $result['error'] = true;
            $result['error_msg'] = __('Not exists data to update.', BLOKS_TEXTDOMAIN);
        } else {
            $id = $_POST['page_id'];
            $title = isset($_POST['new_title'])? $_POST['new_title'] : '';
            $slug = isset($_POST['new_slug'])? $_POST['new_slug'] : null;
            $result['link'] = get_sample_permalink($id, $title, $slug);
        }

        die(json_encode($result));
    }

    /**
     * Update options method
     */
    public function updateOptions()
    {
        $result = array();
        $data = $_POST;
        unset($data['action']);

        if (!count($data)) {
            $result['error'] = true;
            $result['error_msg'] = __('Not exists data to update.', BLOKS_TEXTDOMAIN);
        } else {
            if (Bloks_Core_Settings::updateOptions($data)) {
                $result['success'] = true;
            } else {
                $result['error'] = true;
                $result['error_msg'] = __(
                    'Have an error when try to update options.',
                    BLOKS_TEXTDOMAIN
                );
            }
        }

        die(json_encode($result));
    }

    /**
     * Update page method
     */
    public function updatePage()
    {
        $result = array();
        if (!isset($_POST['page_id']) || !$_POST['page_id']
            || !isset($_POST['content']) || !$_POST['content']) {
            $result['error'] = true;
            $result['error_msg'] = __('Not exists data to update.', BLOKS_TEXTDOMAIN);
        } else {
            $post = get_post($_POST['page_id']);
            if (!$post) {
                $result['error'] = true;
                $result['error_msg'] = __('Page doesn\'t exists.', BLOKS_TEXTDOMAIN);
            } else {
                $post->post_title = $_POST['title'];
                $content = stripslashes($_POST['content']);
                $post->post_content = $content;
                $post->post_name = $_POST['slug'];

                if(isset($_POST['status'])&&($_POST['status'] =='publish')) {
                    $post->post_status = 'publish';
                }

                if (!wp_update_post($post)) {
                    $result['error'] = true;
                    $result['error_msg'] = __(
                        'Have an error when try to update page content.',
                        BLOKS_TEXTDOMAIN
                    );
                } else {
                    $builder = $_POST['builder'];
                    delete_post_meta($post->ID, '_builder_content');
                    if (add_post_meta($post->ID, '_builder_content', $builder)) {
                        foreach ($_POST['meta'] as $key => $value) {
                            delete_post_meta($post->ID, $key);
                            add_post_meta($post->ID, $key, $value);
                        }
                        if ($_POST['status'] == 'save') {
                            $result['redirect'] = get_page_link($post->ID) . '&bloks';
                        } elseif ($_POST['status'] == 'publish') {
                            $result['redirect'] = admin_url('edit.php?post_type=page');
                        } else {
                            $result['redirect'] = get_page_link($post->ID);
                        }
                    } else {
                        $result['error'] = true;
                        $result['error_msg'] = __(
                            'Have an error when try to update post meta.',
                            BLOKS_TEXTDOMAIN
                        );
                    }
                }
            }
        }

        die(json_encode($result));
    }

    /**
     * Get widget form method
     */
    public function getWidgetForm()
    {
        $type = $_POST['type'];
        $default = !is_null($_POST['default']) && is_array($_POST['default'])
            ? $_POST['default'] : array();
        try {
            /**
             * Widget instance
             *
             * @var WP_Widget $widget
             */
            $widget = new $type();
            ob_start();
            $widget->form($default);
            $content = ob_get_clean();
            ob_end_clean();

            preg_match_all('/name="(.+?)"/is', $content, $matches);
            if (count($matches[1])) {
                foreach ($matches[1] as $name) {
                    $replaceName = str_replace(
                        array('[', ']', 'widget-' . $widget->id_base),
                        '',
                        $name
                    );
                    $content = str_replace($name, $replaceName, $content);
                }
            }

            $result =  array(
                'name' => $widget->name,
                'html'  => $content
            );
        } catch (Exception $e) {
            $result = array(
                'error'     => true,
                'error_msg' => $e->getMessage()
            );
        }

        die(json_encode($result));
    }

    /**
     * Get Widget content method
     */
    public function getWidgetContent()
    {
        $type = $_POST['type'];
        $data = $_POST;
        unset($data['type']);
        unset($data['action']);

        try {
            /**
             * Widget instance
             *
             * @var WP_Widget $widget
             */
            $widget = new $type();
            ob_start();
            $widget->widget(array(), $data);
            $content = ob_get_clean();
            ob_end_clean();

            $content = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $content);

            $result = array(
                'html'      => $content,
                'tokenize'  => $data
            );
        } catch (Exception $e) {
            $result = array(
                'error'     => true,
                'error_msg' => $e->getMessage()
            );
        }

        die(json_encode($result));
    }
}
