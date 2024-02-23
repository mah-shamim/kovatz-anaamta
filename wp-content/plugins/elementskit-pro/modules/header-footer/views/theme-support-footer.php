<?php do_action('elementskit/template/before_footer'); ?>
<div class="ekit-template-content-markup ekit-template-content-footer ekit-template-content-theme-support">
<?php
	$template = \ElementsKit\Modules\Header_Footer\Activator::template_ids();
	echo \ElementsKit\Utils::render_elementor_content($template[1]);
?>
</div>
<?php do_action('elementskit/template/after_footer'); ?>
<?php wp_footer(); ?>

</body>
</html>
