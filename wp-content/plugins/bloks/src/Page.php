<?php
/**
 * Class Bloks_Page
 *
 * @author: Kid
 */
class Bloks_Page
{
    /**
     * Bloks page settings constructor
     */
    public function __construct()
    {
        add_action('wp_footer', array($this, 'addParamFrontend'));
        /*
        * add meta in page
        */
        add_filter('wp_head', array($this, 'addMetaData'));
    }

    /**
     * Create params for frontend
     */
    public function addParamFrontend()
    {
        ?>
        <script type="text/javascript">
            var Bloks = Bloks || {};
            Bloks.Params = {
                baseUrl: '<?php echo get_option('siteurl');?>',
                wpnonce: '<?php echo wp_create_nonce();?>'
            };
        </script>
        <?php
    }
    /**
     * Get meta for page
     */
    public function addMetaData()
    {
        global $post;

        $result = '';
        if ($post && $post->ID) {
            $meta_keywords = get_post_meta($post->ID, '_meta_keywords', true);
            if (!empty($meta_keywords)) {
                $result .= '<meta name="keywords"'
                        .' content="'.$meta_keywords.'" />'.PHP_EOL;
            }

            $meta_description = get_post_meta($post->ID, '_meta_description', true);
            if (!empty($meta_description)) {
                $result .= '<meta name="description"'
                        .' content="'.$meta_description.'" />'.PHP_EOL;
            }

            $custom_css = get_post_meta($post->ID, '_custom_css', true);
            if (!empty($custom_css)) {
                $result .= '<style type="text/css">'
                        ."\n". $custom_css ."\n".'</style>'.PHP_EOL;
            }
        }

        echo $result;
    }
}
