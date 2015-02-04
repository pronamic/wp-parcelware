<div class="wrap">
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<?php 
	
	$providers = array(
		'parcelware-web.nl' => array(
			'name'      => 'Parcelware|Web',
			'url'       => 'https://www.parcelware-web.nl/',
			'resources' => array(
					
			)
		),
		'postnlpakketten.nl' => array(
			'name'      => 'PostNL Pakketten',
			'url'       => 'http://www.postnlpakketten.nl/', 
			'resources' => array(
				array(
					'url'     => 'http://pronamic.nl/wp-content/uploads/2013/06/XMLImporteercodesParcelware_tcm204-614724.pdf',
					'name'    => 'XML importeercodes voor Parcelware',
					'date'    => new DateTime( '01-08-2012' )
				)
			)
		)
	);
	
	?>
	
	<table class="wp-list-table widefat" cellspacing="0">

		<?php foreach ( array( 'thead', 'tfoot' ) as $tag ): ?>

			<<?php echo $tag; ?>>
				<tr>
					<th scope="col" class="manage-column"><?php _e( 'Title', 'parcelware' ); ?></th>
					<th scope="col" class="manage-column"><?php _e( 'Date', 'parcelware' );  ?></th>
					<th scope="col" class="manage-column"><?php _e( 'Version', 'parcelware' );  ?></th>
				</tr>
			</<?php echo $tag; ?>>

		<?php endforeach; ?>

		<tobdy>
			
			<?php foreach ( $providers as $provider ): ?>

				<tr>
					<td colspan="4">
						<strong><?php echo $provider['name']; ?></strong>
						<small><a href="<?php echo $provider['url']; ?>"><?php echo $provider['url']; ?></a></small>					
					</td>
				</tr>

				<?php foreach ( $provider['resources'] as $resource ): ?>
		
					<?php 
					
					$href = null;
		
					if ( isset( $resource['path'] ) ) {
						$href = plugins_url( $resource['path'], Pronamic_WordPress_IDeal_Plugin::$file );
					}
		
					if ( isset( $resource['url'] ) ) {
						$href = $resource['url'];
					}
					
					$classes = array();
					
					if ( isset( $resource['deprecated'] ) ) {
						$classes[] = 'deprecated';
					}
					
					?>
					<tr class="<?php echo implode( ' ', $classes ); ?>">
						<td>
							<a href="<?php echo $href; ?>">
								<?php echo $resource['name']; ?>
							</a>
						</td>
						<td>
							<?php if ( isset( $resource['date'] ) ): ?>
								<?php echo $resource['date']->format( 'd-m-Y' ); ?>
							<?php endif; ?>
						</td>
						<td>
							<?php if ( isset( $resource['version'] ) ): ?>
								<?php echo $resource['version']; ?>
							<?php endif; ?>
						</td>
					</tr>
		
				<?php endforeach; ?>
				
			<?php endforeach; ?>
	
		</tobdy>
	</table>
	

	<?php include 'pronamic.php'; ?>
</div>