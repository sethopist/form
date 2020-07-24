<?php
if(is_user_logged_in()): 
	$id = rand();
?>
<!-- =========================
MODAL
=========================== -->
<div class="rk-modal">
  <!-- Modal content -->
  <div class="modal-content">
	  <div class="modal-header">
		<h2>Message</h2>
	  </div>
	  <div class="modal-body">

	  </div>
	  <div class="modal-footer">
		<span onClick="rkModalMessageClose()" class="close">&times;</span>
	  </div>
	</div>

</div>
<!-- ==========================
MODAL CSS
=========================== -->
<style>
.rk-modal {   display: none; /* Hidden by default */   position: fixed; /* Stay in place */   z-index: 10000; /* Sit on top */   left: 0;   top: 0;   width: 100%; /* Full width */   height: 100%; /* Full height */   overflow: auto; /* Enable scroll if needed */   background-color: rgb(0,0,0); /* Fallback color */   background-color: rgba(0,0,0,0.4); /* Black w/ opacity */ padding: 10%; }
.rk-modal.open { display: flex; }
/* Modal Content/Box */ 
.rk-modal .modal-content {   margin: 0 auto;  width: 40%; text-align: center;   }
.rk-modal.open .modal-content  { -webkit-animation: pop-swirl 1s ease forwards;  }
.rk-modal .close {   color: #FFF; background-color: #B60003;   display: inline-block; padding: 0 10px 3px; font-size: 28px; line-height: 35px;   font-weight: bold;  } 
.rk-modal .close:hover, .close:focus {   color: #FFF; background-color: #000;   text-decoration: none;   cursor: pointer; }
/* Modal Header */ .rk-modal .modal-header {   padding: 2px 16px;   background-color: #268059;   color: white; }  
/* Modal Body */.rk-modal .modal-body {padding: 15px 16px; background-color: #FFF; font-size: 18px; }  /* Modal Footer */ 
.rk-modal .modal-footer {   padding: 10px 15px;   background-color: #268059;   color: white; text-align: right; min-height: 30px; }  /* Modal Content */ 
.rk-modal.wait .modal-footer { display: none; }
/* Add Animation */ 
@keyframes pop-swirl {
  0% {
    transform: scale(0) rotate(360deg);
  }
  60% {
    transform: scale(0.8) rotate(-10deg);
  }
  100% {
    transform: scale(1) rotate(0deg);
  }
}
</style>
<!-- ==========================
FORM
=========================== -->
<div class="shrt-front-end-create-post-wrap"> 
	<form id="form-<?php echo $id ?>">
		<input type="hidden" value="" name="user-id">
		<?php wp_nonce_field( 'action_front_end_create_post', 'name_nonce_front_end' ); ?>
		<div class="form-group">
			<label>Post Title</label>
			<input required type="text" name="post-title" value="" class="form-control">
		</div>
		<div class="form-group">
			<label>Post Type</label>
			<select required name="post-type" class="form-control">
				<?php 
				$args = array(
				   'public'   => true
				);

				$post_types = get_post_types($args);
				foreach($post_types as $post){
					echo '<option value="'.$post.'">'.ucfirst($post).'</option>';
				}
				?>
			</select>
		</div>
		<div class="form-group">
			<label>Description</label>
			<textarea required name="post-decs" value="" class="form-control"></textarea>
		</div>
		<div class="form-group">
			<label>Excerpt</label>
			<textarea required name="post-ex" value="" class="form-control"></textarea>
		</div>
		<div class="form-group">
			<label>Featured image</label>
			<input type="file" required name="post-img" value="" class="form-control">
		</div>
		<button type="submit" class="button">Submit</button>
	</form>
	
	<script>
	
	/* ======================
	MODAL FUNCTION
	====================== */
		
	function rkModalMessageClose(){
		jQuery('.rk-modal').removeClass('wait');
		jQuery('.rk-modal').removeClass('open');
	}
	function showModalMessage(message,mode){

		if(!message) return;

		mode = typeof mode !== 'undefined' ? mode : '';

		jQuery('.rk-modal .modal-body').html(message);
		jQuery('.rk-modal').addClass(mode);
		jQuery('.rk-modal').addClass('open');

	}	
	
	/* ======================
	FORM SUBMIT
	====================== */
		
	jQuery('#form-<?php echo $id ?>').submit(
	function(e){
		e.preventDefault();	
		var form = jQuery(this);
		var form_data = new FormData();
		
		var postTitle = jQuery('[name="post-title"]',form).val();
		var postType = jQuery('[name="post-type"]',form).val();
		var postDecs = jQuery('[name="post-decs"]',form).val();
		var postEx = jQuery('[name="post-ex"]',form).val();
		var submitPostFrontEnd = jQuery('[name="name_nonce_front_end"]',form).val();
		var action = 'submit_post_front_end';
		var userID = <?php echo  get_current_user_id(); ?>;
        var file_data = jQuery('[name="post-img"]',form).prop('files')[0];
		
		
		form_data.append('post-title', postTitle);
		form_data.append('post-type', postType);
		form_data.append('post-desc', postDecs);
		form_data.append('post-ex', postEx);
		
		form_data.append('name_nonce_front_end', submitPostFrontEnd);
		form_data.append('post-img', file_data);
		form_data.append('user-id', userID);
		form_data.append('action', action);
		
		jQuery.ajax({
			url: "<?php echo admin_url( 'admin-ajax.php' ) ?>",
			type: 'POST',
			dataType: 'json',
			data: form_data,
			cache: false,
			contentType: false,
			processData: false,
			beforeSend: function(){
				showModalMessage('Processing....','wait');	
			},
			error: function(){
				rkModalMessageClose();
				showModalMessage('failed');	
			},
			success: function(data){
				rkModalMessageClose();
				showModalMessage(data.message);	
				jQuery('#form-<?php echo $id ?>').remove();
				jQuery('.shrt-front-end-create-post-wrap').html('<h2>Done</h2>');
			}
		})
	})
	</script>
</div><!-- shrt-front-end-create-post-wrap -->
<?php
else: 
	echo '<h2>You are not allow to enter this page</h2>';
endif;
