<?php

/**
 * Class Bloks_Core_Settings_Elements_Base
 *
 * @author: Kend
 */
class Bloks_Core_Settings_Elements_Base
{
    protected $type = 'text';
    protected $optionKey = '';
    protected $params = array();

    /**
     * Bloks_Core_Settings_Elements_Base constructor.
     *
     * @param string $optionKey
     * @param array $params
     */
    public function __construct($optionKey = '', $params = array())
    {
        if(!count($params)) {
            wp_die(__('Please enter item params.'));
        }

        $this->optionKey = $optionKey;
        $this->params = $params;
        $this->validate();
    }

    /**
     * Method to validate params
     */
    public function validate()
    {
        if(!isset($this->params['name'])) {
            wp_die(__('Item must be have a name.', BLOKS_TEXTDOMAIN));
        }
    }

    /**
     * Method to render element
     */
    public function render()
    {
        // @codingStandardsIgnoreStart
        ?>
        <tr>
            <th scope="row">
                <label for="<?php echo $this->optionKey?>_<?php echo $this->params['name']?>"><?php echo $this->params['label']?></label>
                <?php if(isset($this->params['description'])):?><p class="description"><?php echo $this->params['description']?></p><?php endif;?>
            </th>
            <td>
                <div class="bloks-input-image">
                    <input type="<?php echo $this->type?>"
                        id="<?php echo $this->optionKey?>_<?php echo $this->params['name']?>"
                        name="<?php echo $this->optionKey?>[<?php echo $this->params['name']?>]"
                        size="<?php echo $this->params['size']?>"
                        value="<?php echo Bloks_Core_Settings::getOption($this->params['name'])?>"
                        class="regular-text"
                    />
                </div>
            </td>
        </tr>
        <?php
        // @codingStandardsIgnoreEnd
    }
}
