<?php
/**
 * The main template file.
 *
 * @package WebForge
 * @author WebForge
 * @link http://webforge.site
 */

$translate['search-placeholder'] = mfn_opts_get('translate') ? mfn_opts_get('translate-search-placeholder','Enter your search') : __('Enter your search','webforge');
?>

<form method="get" id="searchform" action="<?php echo esc_url( home_url( '/' ) ); ?>">
						
	<?php if( mfn_opts_get('header-search') == 'shop' ): ?>
		<input type="hidden" name="post_type" value="product" />
	<?php endif;?>
	
	<i class="icon_search icon-search"></i>
	<a href="#" class="icon_close"><i class="icon-cancel"></i></a>
	
	<input type="text" class="field" name="s" id="s" placeholder="<?php echo $translate['search-placeholder']; ?>" />			
	<input type="submit" class="submit" value="" style="display:none;" />
	
</form>