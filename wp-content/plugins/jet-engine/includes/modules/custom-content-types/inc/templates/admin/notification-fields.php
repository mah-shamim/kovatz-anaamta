<?php
/**
 * Notifications fields template
 */
?>
<div v-if="'<?php echo $action_slug; ?>' === currentItem.type">
	<keep-alive>
		<jet-cct-notification v-model="currentItem.cct" :fields="availableFields" :statuses="<?php echo $statuses; ?>" :content-types="<?php echo $content_types; ?>" fetch-path="<?php echo $fetch_path; ?>"></jet-cct-notification>
	</keep-alive>
</div>
