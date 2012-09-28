<?php 

$datetime_today = date( 'Y-m-d H:i:s' );

?>
<h3>
	<?php _e( 'Parcelware Export Orders', 'parcelware' ); ?>
</h3>

<form method="post" action="">
	<?php wp_nonce_field( 'parcelware_export', 'parcelware_nonce' ); ?>

	<table class="form-table">
		<tr>
			<td>
				<?php _e( 'Date from', 'parcelware' ); ?>
			</td>
			<td>
				<input type="text" id="date-from" name="date-from" value="<?php echo $datetime_today; ?>" />
			</td>
		</tr>
		<tr>
			<td>
				<?php _e( 'Date to', 'parcelware' ); ?>
			</td>
			<td>
				<input type="text" id="date-to" name="date-to" value="<?php echo $datetime_today; ?>" />
			</td>
		</tr>
	</table>

	<?php submit_button( __( 'Download CSV File', 'parcelware' ) ); ?>
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