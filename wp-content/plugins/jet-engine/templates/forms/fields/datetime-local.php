<?php
/**
 * input[type="datetime-local"] template
 */

$this->add_attribute( 'placeholder', $args['placeholder'] );
$this->add_attribute( 'value', $args['default'] );
$this->add_attribute( 'required', $this->get_required_val( $args ) );
$this->add_attribute( 'name', $this->get_field_name( $args['name'] ) );
$this->add_attribute( 'type', 'datetime-local' );
$this->add_attribute( 'data-field-name', $args['name'] );
$this->add_attribute( 'id', $this->get_field_id( $args ) );

?>
<input class="jet-form__field datetime-field"<?php $this->render_attributes_string(); ?>>