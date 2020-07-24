<?php
/* ---------------------------------- */
/* [START] [front-end-create-post]
/* ---------------------------------- */
add_shortcode( 'front-end-create-post', 'shrtcd_front_end_create_post_func' );
function shrtcd_front_end_create_post_func($atts,$content){
	@extract($atts);
	ob_start();
	include('shortcodes/front-end-create-post.php');
	return ob_get_clean();
} 
?>
