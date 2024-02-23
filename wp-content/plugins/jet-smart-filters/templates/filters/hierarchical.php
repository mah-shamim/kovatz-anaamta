<?php

if ( empty( $levels ) ) {
	return;
}

?>
<div class="jet-filters-group">
<?php
foreach ( $levels as $level ) {
	echo $level;
}
?>
</div>
