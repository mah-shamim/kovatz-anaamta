<div class="messages-list"><?php

	foreach ( jet_engine()->forms->get_message_types() as $name => $data ) {
		$value = isset( $messages[ $name ] ) ? $messages[ $name ] : $data['default'];

		?>
		<div class="jet-form-editor__row">
			<div class="jet-form-editor__row-label"><?php echo $data['label']; ?></div>
			<div class="jet-form-editor__row-control">
				<input type="text" name="_messages[<?php echo $name; ?>]" value="<?php echo $data['default']; ?>">
			</div>
		</div>
		<?php
	}

?></div>