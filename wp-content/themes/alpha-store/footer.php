</div> <!-- end main container -->
<div class="container-fluid rsrc-footer">    
	<?php if ( is_active_sidebar( 'alpha-store-footer-area' ) ) { ?>
		<div class="container">
			<div id="content-footer-section" class="row clearfix">
				<?php dynamic_sidebar( 'alpha-store-footer-area' ); ?>
			</div>
		</div>
	<?php } ?>
    <div class="rsrc-copyright">    
		<footer id="colophon" class="container" role="contentinfo" itemscope itemtype="http://schema.org/WPFooter">
			<div class="row rsrc-author-credits">
				<p class="text-center">
					<?php printf( __( '© Bản quyền thuộc về %s', 'alpha-store' ), '<a href="' . esc_url( "https://www.facebook.com/minhrekindle/" ) . '">huongmocsac.com</a>' ); ?>
					<span class="sep"> | </span>
					<?php printf( __( 'Cung cấp bởi: %1$s ', 'alpha-store' ), '<a href="' . esc_url( "https://www.facebook.com/minhrekindle/" ) . '" title="website created by minhdv">Minhdv</a>' ); ?>
				</p>  
			</div>
		</footer>
		<div id="back-top">
			<a href="#top"><span></span></a>
		</div>
    </div>
</div>
</div>
<!-- end footer container -->

<?php wp_footer(); ?>
</body>
</html>