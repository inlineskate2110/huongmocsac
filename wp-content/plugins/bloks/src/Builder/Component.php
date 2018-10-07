<?php

/**
 * Class Bloks_Builder_Component
 *
 * @author: Kend
 */
class Bloks_Builder_Component
{
    private $_types = array();

    /**
     * Method to get types
     *
     * @return array
     */
    public function getTypes()
    {
        $categories = get_terms(
            array(
                'taxonomy'      => 'blocks_category',
                'hide_empty'    => false,
                'orderby'       => 'id'
            )
        );

        if (!is_wp_error($categories) && $categories) {
            foreach ($categories as $category) {
                $this->_types[$category->slug] = $category->name;
            }
        }

        return $this->_types;
    }

    /**
     * Method to get templates instance
     *
     * @return array
     */
    public function getTemplates()
    {
        $templates = array();
        foreach ($this->getTypes() as $name => $title) {
            $templates[$name]['title'] = $title;
            $files = $this->getTemplateFiles(
                BLOKS_ROOT_PATH . DS . 'blocks' . DS . $name
            );

            foreach ($files as $filename => $file) {
                $instance = new Bloks_Core_Block_Template_Processor($file);
                $templates[$name]['templates'][$filename] = $instance;
            }
        }

        $templates = apply_filters('bloks_builder_get_templates', $templates);
        return $templates;
    }

    /**
     * Method to get the full path of all files
     * in the directoryand all subdirectories of a directory
     *
     * @param string $dir
     * @return array
     */
    public function getTemplateFiles($dir)
    {
        $result = array();

        $files = glob($dir . DS . '*.html');
        foreach ($files as $file) {
            $fileInfo = pathinfo($file);
            $result[$fileInfo['filename']] = $file;
        }

        return $result;
    }
}
