<?php
if( isset( $_POST['submit'] ) ){echo $_POST['date-from'] . ' - ' . $_POST['date-to'] . '<br />';
	add_filter('posts_where', 'wp_parcelware_dates_between');
	$posts = get_posts( array(
		'numberposts' => -1,
		'offset' => 0,
		'orderby' => 'post_date',
		'order' => 'DESC',
		'post_type' => 'shop_order'
	) );
	remove_filter('posts_where', 'wp_parcelware_dates_between');
	
	function wp_parcelware_dates_between( $where ){echo 'llololol';
		global $wpdb;
		
		$where .= $wpdb->prepare(" AND post_date >= '%s'", $_POST['date-from']);
		$where .= $wpdb->prepare(" AND post_date <= '%s'", $_POST['date-to']);
		
		return $where;
	}
	
	foreach($posts as $post){
		var_dump($post);
		echo '<br /><br />';
	}
	
	//if($_POST['date_from'])
}
?>

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
	</table>
	<?php submit_button(); ?>
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