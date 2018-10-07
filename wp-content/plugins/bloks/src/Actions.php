<?php
/**
 * Plugins Actions
 */
class Bloks_Actions
{
    /**
     * Action constructor.
     */
    public function __construct()
    {
        add_action('wp_ajax_nopriv_bloks-sendmail', array($this, 'ajaxSendmail'));
        add_action('wp_ajax_bloks-sendmail', array($this, 'ajaxSendmail'));
    }

    /**
     * Sendmail method use wp_mail
     */
    public function ajaxSendmail()
    {
        $result = array(
            'succeed' => true,
            'error' => ''
        );

        $emailAdmin = get_option('admin_email');
        $blogname = get_option('blogname', '');
        $data = $_POST;
        $headers = 'From '.$blogname;

        $nonce = $_GET['_wpnonce'];
        if (! wp_verify_nonce($nonce) ) {
            $result['succeed'] = false;
            $result['error'] = wp_verify_nonce($nonce);
            die(json_encode($result));
        }

        $fieldAdd = array();
        $fieldOriginal = array('action');
        foreach ($data as $key => $dt) {
            if (!in_array($key, $fieldOriginal)) {
                $fieldAdd[] = ucfirst(str_replace('_', ' ', $key)) . ': ' . $dt;
            }
        }

        if ($fieldAdd) {
            $html = implode(PHP_EOL, $fieldAdd);
        } else {
            $html = 'Email no content!';
        }

        try {
            wp_mail($emailAdmin, $blogname, $html, $headers);
        } catch (Exception $e) {
            $result['succeed'] = false;
            $result['error'] = $e->getMessage();
        }

        die(json_encode($result));
    }
}
?>
