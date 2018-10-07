<?php
/**
 * Class Bloks_Builder_CustomBlocks
 *
 * @author: Kend
 */
class Bloks_Builder_CustomBlocks
{
    /**
     * Bloks_Builder_CustomBlocks constructor.
     */
    public function __construct()
    {
        add_filter('bloks_builder_get_templates', array( $this, 'blocksBuilderCustom' ));
        add_action('wp_enqueue_scripts', array($this, 'blocksEnqueueScripts'));
    }

    /**
     * Builder custom blocks template
     *
     * @param string $templates
     *
     * @return mixed
     */
    public function blocksBuilderCustom( $templates )
    {
        $args       = array(
         'posts_per_page'   => - 1,
         'post_type'        => 'custom_blocks',
         'post_status'      => 'private',
         'suppress_filters' => true
        );
        $postsArray = get_posts($args);
        $upload_dir = wp_upload_dir();
        $baseDir    = $upload_dir['basedir'] . '/bloks/';
        if ($postsArray && is_array($postsArray) ) {
            foreach ( $postsArray as $key => $post ) {
                $types = get_the_terms($post->ID, 'blocks_category');
                if(!is_wp_error($types)&&$types) {
                    foreach ($types as $type){
                        $files = Bloks()->getBuilderFactory()
                                        ->getComponent()
                                        ->getTemplateFiles($baseDir . $post->ID);
                        foreach ($files as $filename => $file) {
                            $instance = new Bloks_Core_Block_Template_Processor($file);
                            $instance->is_custom = true;
                            $templates[$type->slug]['templates'][$filename] = $instance;
                        }
                    }
                }
            }
        }

        return $templates;
    }
    /**
     * Builder custom blocks style and javascript
     */
    public function blocksEnqueueScripts()
    {
        if(!is_admin()) {
            $args       = array(
             'posts_per_page'   => - 1,
             'post_type'        => 'custom_blocks',
             'post_status'      => 'private',
             'suppress_filters' => true,
            );
            $postsArray = get_posts($args);
            $upload_dir = wp_upload_dir();
            $baseDir = $upload_dir['baseurl'].'/bloks/';
            if ($postsArray && is_array($postsArray) ) {
                foreach ( $postsArray as $key => $post ) {
                    if(taxonomy_exists('blocks_category')) {
                        $type = get_the_terms($post->ID, 'blocks_category');
                        if (isset($type[0])) {
                            wp_enqueue_script(
                             'bloks-custom-block-'.$post->ID,
                             $baseDir.$post->ID.'/javascript'.$post->ID.'.js'
                            );
                            wp_enqueue_style(
                             'bloks-custom-block-'.$post->ID,
                             $baseDir.$post->ID.'/stylesheet'.$post->ID.'.css'
                            );
                        }
                    }
                }
            }
        }

    }
}
