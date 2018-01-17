<?php
/**
 * Builder sidebar view template
 * @since   [version]
 * @version [version]
 */
?>

<script type="text/html" id="tmpl-llms-sidebar-template">

	<div class="llms-elements" id="llms-elements"></div>
	<div class="llms-utilities" id="llms-utilities"></div>

	<div class="llms-editor" id="llms-editor"></div>

	<footer class="llms-builder-save">

		<button class="llms-button-primary full" data-status="saved" id="llms-save-button" disabled="disabled">
			<i></i>
			<span class="llms-status-indicator status--saved"><?php _e( 'Saved', 'lifterlms' ); ?></span>
			<span class="llms-status-indicator status--unsaved"><?php _e( 'Save changes', 'lifterlms' ); ?></span>
			<span class="llms-status-indicator status--saving"><?php _e( 'Saving changes...', 'lifterlms' ); ?></span>
			<span class="llms-status-indicator status--error"><?php _e( 'Error saving changes...', 'lifterlms' ); ?></span>
		</button>

	</footer class="llms-builder-save">

</script>
