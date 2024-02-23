<?php
/**
 * Field description template
 */

if ( 'heading' === $args['type'] ) {
	$class = 'jet-form__heading-desc';
	$tag   = 'div';
} else {
	$class = 'jet-form__desc';
	$tag   = 'small';
}

printf(
	'<%1$s class="%2$s"><span class="jet-form__desc-text">%3$s</span></%1$s>',
	$tag,
	$class,
	$args['desc']
);
