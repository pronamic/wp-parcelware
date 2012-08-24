<h3><?php _e('Parcelware Order Exporter', 'wp-parcelware-plugin'); ?></h3>

<form method="post" action="<?php echo $form_action; ?>">
	<table class="wide-fat">
		<tr>
			<td><?php _e('Date from', 'wp-parcelware-plugin'); ?></td>
			<td><input type="text" id="date-from" name="date-from" value="<?php echo $datetime_today; ?>" /></td>
		</tr>
		<tr>
			<td><?php _e('Date to', 'wp-parcelware-plugin'); ?></td>
			<td><input type="text" id="date-to" name="date-to" value="<?php echo $datetime_today; ?>" /></td>
		</tr>
		<tr>
			<td><input type="checkbox" name="skip-already-exported" value="true" checked="checked" /></td>
			<td><?php _e('Skip orders that have already been exported.', 'wp-parcelware-plugin'); ?></td>
		</tr>
	</table>
	<?php submit_button( __('Download CSV File', 'wp-parcelware-plugin') ); ?>
</form>

<script type="text/javascript">
	var options = {
		timeFormat: 'hh:mm:ss',
		separator: ' ',
		dateFormat: 'yy-mm-dd'
	};
	
	jQuery('#date-from').datetimepicker(options);
	jQuery('#date-to').datetimepicker(options);
</script>