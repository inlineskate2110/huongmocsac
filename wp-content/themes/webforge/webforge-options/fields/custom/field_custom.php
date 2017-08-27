<?php
class MFN_Options_custom extends MFN_Options{	
	
	/**
	 * Field Constructor.
	 */
	function __construct( $field = array(), $value ='', $parent = NULL ){
		if( is_object($parent) ) parent::__construct($parent->sections, $parent->args, $parent->extra_tabs);
		$this->field = $field;
		$this->value = $value;		
	}
	
	/**
	 * Field Render Function.
	 */
	function render( $meta = false ){
		
		$action = isset( $this->field['action'] ) ? $this->field['action'] : '';
		
		echo '<div class="mfn-custom-field">';
		
			if( $action == 'wpml' ){
				// WPML Installer -----------------------------
				
				echo '<p>WebForge is <a href="http://wpml.org/" target="_blank">fully compatible with WPML</a> - the WordPress Multilingual Plugin. WPML lets you add languages to your existing sites and includes advanced translation management.</p>';
			
				echo '<div class="mfn-custom-buttons">';
				
					echo '<a class="btn-blue btn-green" href="http://wpml.org/" target="_blank">'. __('Buy and Download', 'mfn-opts') .'</a> ';
					echo '<a class="btn-blue" href="http://wpml.org/features/" target="_blank">'. __('WPML Features', 'mfn-opts') .'</a>';
				
				echo '</div>';
				
			} else {
				// Default ------------------------------------
				
				echo '<p>This is "field_custom" and requires "action" parameter</p>';
						
			}
		
		echo '</div>';

	}
	
}
