<?php
/**
 * Row-layout field template
 */

echo $label;

if ( $template ) {
	if ( Jet_Engine_Tools::is_readable( $template ) ) {
		include $template;
	} else {
		echo $template;
	}
}

echo $desc;