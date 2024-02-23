<?php
/**
 * Notifications fields template
 */
?>
<div v-if="'<?php echo $action_slug; ?>' === currentItem.type">
	<keep-alive>
		<jet-rest-notification v-model="currentItem.rest_api" :fields="availableFields"></jet-rest-notification>
	</keep-alive>
</div>
