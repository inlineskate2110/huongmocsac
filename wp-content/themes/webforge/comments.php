<?php
/**
 * The template for displaying Comments.
 */
?>
		
<div id="comments">
	<?php if ( post_password_required() ) : ?>
		<p class="nopassword"><?php _e( 'This post is password protected. Enter the password to view any comments.', 'webforge' ); ?></p>
		</div>
		<?php return;
	endif; ?>

	<?php if ( have_comments() ) : ?>
		<h4 id="comments-title">
			<?php
				if( get_comments_number() == 1 ){
					echo get_comments_number() .' '. __('Comment','webforge');
				} else {
					echo get_comments_number() .' '. __('Comments','webforge');
				}
			?>
		</h4>

		<ol class="commentlist">
			<?php wp_list_comments('avatar_size=64'); ?>
		</ol>

		<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : // are there comments to navigate through ?>
		<nav id="comment-nav">
			<div class="nav-previous"><?php previous_comments_link( __( '&larr; Older Comments', 'webforge' ) ); ?></div>
			<div class="nav-next"><?php next_comments_link( __( 'Newer Comments &rarr;', 'webforge' ) ); ?></div>
		</nav>
		<?php endif; ?>

	<?php
		elseif ( ! comments_open() && ! is_page() && post_type_supports( get_post_type(), 'comments' ) ) :
	?>
		<p class="nocomments"><?php _e( 'Comments are closed.', 'webforge' ); ?></p>
	<?php endif; ?>

	<?php comment_form(); ?>

</div><!-- #comments -->
