<?php
$posts = get_posts( array(
	'numberposts' => -1,
	'offset' => 0,
	'post_type' => 'shop_order'
) );

foreach($posts as $post){
	
}
?>