<?php 

$datetime_today = date( 'o-m-d H:i:s' );

?>
<h3>
	<?php _e( 'Parcelware Export Orders', 'parcelware' ); ?>
</h3>

<form method="post" action="">
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
		<tr>
			<td>
				<?php _e( 'Export', 'parcelware' ); ?>
			</td>
			<td>
				<input type="checkbox" name="skip-already-exported" id="skip-already-exported" value="true" checked="checked" />

				<label for="skip-already-exported"><?php _e( 'Skip orders that have already been exported.', 'parcelware' ); ?></label>
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