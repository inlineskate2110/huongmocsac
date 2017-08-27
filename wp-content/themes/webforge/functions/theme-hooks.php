<?php
/**
 * Hooks
 *
 * @package WebForge
 * @author WebForge
 * @link http://webforge.site
 */


/* ---------------------------------------------------------------------------
 * Hook | Top
 * --------------------------------------------------------------------------- */
function mfn_hook_top()
{
	echo '<!-- webforge_top -->';
		echo do_shortcode( mfn_opts_get( 'hook-top' ) );
	echo '<!-- webforge_top -->';
}
add_action( 'mfn_hook_top', 'mfn_hook_top' );


/* ---------------------------------------------------------------------------
 * Hook | Content before
 * --------------------------------------------------------------------------- */
function mfn_hook_content_before()
{
	echo '<!-- webforge_content_before -->';
		echo do_shortcode( mfn_opts_get( 'hook-content-before' ) );
	echo '<!-- webforge_content_before -->';
}
add_action( 'mfn_hook_content_before', 'mfn_hook_content_before' );


/* ---------------------------------------------------------------------------
 * Hook | Content after
 * --------------------------------------------------------------------------- */
function mfn_hook_content_after()
{
	echo '<!-- webforge_content_after -->';
		echo do_shortcode( mfn_opts_get( 'hook-content-after' ) );
	echo '<!-- webforge_content_after -->';
}
add_action( 'mfn_hook_content_after', 'mfn_hook_content_after' );


/* ---------------------------------------------------------------------------
 * Hook | Bottom
 * --------------------------------------------------------------------------- */
function mfn_hook_bottom()
{
	echo '<!-- webforge_bottom -->';
	echo do_shortcode( mfn_opts_get( 'hook-bottom' ) );
	echo '<!-- webforge_bottom -->';
}
add_action( 'mfn_hook_bottom', 'mfn_hook_bottom' );
