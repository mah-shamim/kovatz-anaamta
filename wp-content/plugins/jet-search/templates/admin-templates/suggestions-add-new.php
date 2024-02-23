<cx-vui-button
	button-style="accent"
	size="mini"
	@click="callPopup( 'new', item )"
>
	<template slot="label"><?php esc_html_e( 'Add New', 'jet-search' ); ?></template>
</cx-vui-button>