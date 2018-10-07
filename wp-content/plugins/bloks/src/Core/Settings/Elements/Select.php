<?php

/**
 * Class Bloks_Settings_Core_Elements_Select
 *
 * @author: Kend
 */
class Bloks_Core_Settings_Elements_Select extends Bloks_Core_Settings_Elements_Base
{
    protected $type = 'select';

    /**
     * Method to validate params
     */
    public function validate()
    {
        parent::validate();
        if(!isset($this->params['options'])) {
            wp_die(
                'Please enter options for "'.$this->params['label'].'" element',
                BLOKS_TEXTDOMAIN
            );
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
                <p class="description"><?php echo $this->params['description']?></p>
            </th>
            <td>
                <div class="bloks-input-image">
                    <select type="<?php echo $this->type?>"
                           id="<?php echo $this->optionKey?>_<?php echo $this->params['name']?>"
                           name="<?php echo $this->optionKey?>[<?php echo $this->params['name']?>]"
                    >
                        <?php foreach ($this->params['options'] as $option):?>
                        <option value="<?php echo $option['value']?>"
                                <?php if(Bloks_Core_Settings::getOption($this->params['name']) == $option['value']):?>selected<?php endif?>
                        ><?php echo $option['label']?></option>
                        <?php endforeach;?>
                    </select>
                </div>
            </td>
        </tr>
        <?php
        // @codingStandardsIgnoreEnd
    }
}
