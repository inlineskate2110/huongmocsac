<?php
/**
 * Html processor
 *
 * @author: Kend
 */
class Bloks_Core_Block_Template_Processor
{
    private $_content = false;
    private $_name = false;
    private $_screenshot = false;
    private $_not_post_types = false;
    private $_vars = false;
    private $_params = array();

    /**
     * Bloks_Core_Block_Template_Processor constructor.
     *
     * @param string $filename
     */
    public function __construct($filename)
    {
        $content = file_get_contents($filename);

        if (preg_match_all(
            '/<!--@(\w+)\s*(.*?)\s*@-->/s',
            $content,
            $matches
        )) {
            foreach ($matches[1] as $index => $match) {
                switch ($match) {
                case 'name':
                    $this->_name = $matches[2][$index];
                    break;
                case 'screenshot':
                    $this->_screenshot = $matches[2][$index];
                    break;
                case 'not_post_types':
                    $this->_not_post_types = json_decode($matches[2][$index]);
                    break;
                default:
                    $this->_params[$match] = $matches[2][$index];
                }

                if($match == 'name' 
                    || $match == 'screenshot' 
                    || $match == 'not_post_types') {
                    $content = str_replace($matches[0][$index], '', $content);
                }
            }

            $this->_vars = $matches;
        }

        if(!preg_match('/v-settings/', $content)) {
            $content = '<div v-bind:style="{ ' .
                'paddingTop: styles.paddingTop + \'px\', ' .
                'paddingBottom: styles.paddingBottom + \'px\', ' .
                'backgroundImage: url(\' + styles.backgroundImage + \') ' .
                'v-settings>' . $content . '</div>';
        }

        $this->_content = trim($content);
    }

    /**
     * Method to get screenshot of html file
     *
     * @return bool|string
     */
    public function getScreenshot()
    {
        if($this->_screenshot) {
            if(strpos($this->_screenshot, 'http://') !== false
                || strpos($this->_screenshot, 'https://') !== false
            ) {
                return $this->_screenshot;
            } else {
                return BLOKS_ROOT_URL . $this->_screenshot;
            }
        }

        return BLOKS_ROOT_URL . 'assets/images/builder/blocks/default.png';
    }

    /**
     * Method to get name of html file
     *
     * @return bool|string
     */
    public function getName()
    {
        if($this->_name) {
            return $this->_name;
        }

        return false;
    }

    /**
     * Method to check post type is accept or not
     *
     * @param string $postType
     * @return bool
     */
    public function isAcceptPostType($postType)
    {
        if($this->_not_post_types === false 
            || (is_array($this->_not_post_types)
            && (!count($this->_not_post_types)
            || !in_array($postType, $this->_not_post_types)))) {
            return true;
        }

        return false;
    }

    /**
     * Method to get variables in template
     *
     * @return array
     */
    public function getParams()
    {
        return $this->_params;
    }

    /**
     * Method to parse params to template string
     *
     * @return string
     */
    public function getParamsString()
    {
        $string = '';

        foreach ($this->_vars[1] as $index => $var) {
            if(in_array($var, array_keys($this->_params))) {
                $string .= $this->_vars[0][$index] . "\n";
            }
        }

        return $string;
    }

    /**
     * Method to get content of html file
     *
     * @param bool $removeVars
     * @param bool $minify
     * @return bool|string
     */
    public function getContent($removeVars = false, $minify = false)
    {
        $content = $this->_content;

        foreach ($this->_vars[1] as $index => $var) {
            if($var == 'name' || $var == 'screenshot' || $var == 'not_post_types') {
                $content = str_replace($this->_vars[0][$index], '', $content);
            }

            if($removeVars) {
                $content = str_replace($this->_vars[0][$index], '', $content);
            }
        }

        if($minify) {
            $content = preg_replace(
                array('/>\s+</i', '/[\r\n]/i'),
                array('><', ''),
                $content
            );
        }

        return trim($content);
    }
}
