<?php
/*
 * Filters for block patterns.
 */

/**
 * @param $content
 *
 * @return false|string
 */
function directory_theme_pattern_header_default( $content ) {
	ob_start();

	$home_url = get_home_url(); /* <?php echo esc_url( $home_url ); ?> */
	?>
    <!-- wp:blockstrap/blockstrap-widget-skip-links {"content":""} -->
    [bs_skip_links text1='Skip to main content'  hash1='main'  text2=''  hash2=''  text3=''  hash3=''  text_color=''  text_justify='false'  text_align=''  text_align_md=''  text_align_lg=''  mt=''  mr=''  mb=''  ml=''  mt_md=''  mr_md=''  mb_md=''  ml_md=''  mt_lg=''  mr_lg=''  mb_lg=''  ml_lg=''  pt=''  pr=''  pb=''  pl=''  pt_md=''  pr_md=''  pb_md=''  pl_md=''  pt_lg=''  pr_lg=''  pb_lg=''  pl_lg=''  border=''  rounded=''  rounded_size=''  shadow=''  css_class='' ]
    <!-- /wp:blockstrap/blockstrap-widget-skip-links -->

    <!-- wp:blockstrap/blockstrap-widget-navbar {"bg":"custom-color","bg_color":"rgba(255,255,255,0.93)","bgtus":true,"container":"navbar-light","inner_container":"container","mb_lg":"","shadow":"shadow","position":"fixed-top"} -->
    [bs_navbar bg='custom-color'  bg_color='rgba(255,255,255,0.93)'  bg_gradient='linear-gradient(135deg,rgba(6,147,227,1) 0%,rgb(155,81,224) 100%)'  bg_image_fixed='false'  bg_image_use_featured='false'  bg_image=''  bg_image_id=''  bg_image_xy='{x:undefined,y:undefined}'  bgtus='true'  cscos='false'  container='navbar-light'  inner_container='container'  mt=''  mr=''  mb=''  ml=''  mt_md=''  mr_md=''  mb_md=''  ml_md=''  mt_lg=''  mr_lg=''  mb_lg=''  ml_lg=''  pt=''  pr=''  pb=''  pl=''  pt_md=''  pr_md=''  pb_md=''  pl_md=''  pt_lg=''  pr_lg=''  pb_lg=''  pl_lg=''  border=''  rounded=''  rounded_size=''  shadow='shadow'  position='fixed-top'  sticky_offset_top=''  sticky_offset_bottom='' ]<nav class="navbar navbar-expand-lg bg-custom-color bg-transparent-until-scroll navbar-light fixed-top shadow" style="background-color:rgba(255,255,255,0.93)"><div class="wp-block-blockstrap-blockstrap-widget-navbar container"><!-- wp:blockstrap/blockstrap-widget-navbar-brand {"text":"\u003cspan class=\u0022text-primary\u0022\u003eD\u003c/span\u003eirectory","img_max_width":150,"custom_url":"/","brand_font_size":"h4","brand_font_weight":"font-weight-bold","bg_gradient":"linear-gradient(135deg,rgb(34,227,7) 0%,rgb(245,245,245) 100%)","bg_on_text":true,"mb_lg":"1","pt_lg":"0","pr_lg":"0","pb_lg":"0","rounded_size":"lg"} -->
            [bs_navbar_brand text='<span class="text-primary">D</span>irectory'  icon_image=''  img_max_width='150'  type='home'  custom_url='/'  text_color=''  brand_font_size='h4'  brand_font_weight='font-weight-bold'  brand_font_italic=''  text_justify='false'  text_align=''  text_align_md=''  text_align_lg=''  bg=''  bg_color='#0073aa'  bg_gradient='linear-gradient(135deg,rgb(34,227,7) 0%,rgb(245,245,245) 100%)'  bg_on_text='true'  mt=''  mr=''  mb=''  ml=''  mt_md=''  mr_md=''  mb_md=''  ml_md=''  mt_lg=''  mr_lg=''  mb_lg='1'  ml_lg=''  pt=''  pr=''  pb=''  pl=''  pt_md=''  pr_md=''  pb_md=''  pl_md=''  pt_lg='0'  pr_lg='0'  pb_lg='0'  pl_lg=''  border=''  rounded=''  rounded_size='lg'  shadow=''  css_class='' ]<a class="navbar-brand d-flex align-items-center mb-1 pt-0 pe-0 pb-0 rounded-lg" href="<?php echo esc_url( $home_url ); ?>"><span class="mb-0 props.attributes.brand_font_size props.attributes.brand_font_weight props.attributes.brand_font_italic"><span class="text-primary">D</span>irectory</span></a>[/bs_navbar_brand]
            <!-- /wp:blockstrap/blockstrap-widget-navbar-brand -->
			<?php
			echo directory_theme_get_default_menu(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
			</div></nav>[/bs_navbar]
	<!-- /wp:blockstrap/blockstrap-widget-navbar -->
	<?php

	return ob_get_clean();
}
add_filter( 'blockstrap_pattern_header_default', 'directory_theme_pattern_header_default', 15 );

/**
 * @param $content
 *
 * @return array|false|string|string[]
 */
function directory_theme_pattern_header_transparent( $content ) {

	// Use the default menu and just change the settings @todo we need to find a better way to re-use the same menu
	return str_replace(
		array(
			'"container":"navbar-light"',
			"cscos='false'",
			"container='navbar-light'",
			' navbar-light ',
		),
		array(
			'"cscos":true,"container":"navbar-dark"',
			"cscos='true'",
			"container='navbar-dark'",
			' color-scheme-flip-on-scroll navbar-dark ',
		),
		directory_theme_pattern_header_default( $content )
	);
}
add_filter( 'directory_pattern_header_transparent', 'directory_theme_pattern_header_transparent', 15 );
