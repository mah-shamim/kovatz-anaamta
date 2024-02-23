<?php
/**
 * Templates types tabs template.
 */
?>

<div class="nav-tab-wrapper jet-woo-library-tabs">
	<?php
	foreach ( $tabs as $tab => $label ) {
		$class = 'nav-tab';

		if ( $tab === $active_tab ) {
			$class .= ' nav-tab-active';
		}

		if ( 'all' !== $tab ) {
			$link = add_query_arg( [ $this->type_tax => $tab ], $page_link );
		} else {
			$link = $page_link;
		}

		printf( '<a href="%s" class="%s">%s</a>', $link, $class, $label );
	}
	?>
</div>