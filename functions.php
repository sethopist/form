<?php
add_action( 'wp_ajax_submit_post_front_end', 'submit_post_front_end_callback' );
	function submit_post_front_end_callback(){

		$fdata = $_POST['formData'];
		$data = array();

		if(!empty($fdata)){
			foreach($fdata as $d){
				$data[$d['name']] = $d['value'];
			}//end foreach
		}//end if

		if($_POST['name_nonce_front_end'] && wp_verify_nonce( $_POST['name_nonce_front_end']  , 'action_front_end_create_post' )){
				
				if (!function_exists('wp_handle_upload')) {
				   require_once(ABSPATH . 'wp-admin/includes/file.php');
				}
				$uploadedfile = $_FILES['post-img'];
				$upload_overrides = array('test_form' => false);

				$filetype = $_FILES["post-img"]["type"];
				$filesize = $_FILES["post-img"]["size"];
			
				if($filetype != "image/png" and $filetype != "image/jpg" and $filetype != "image/jpeg") {
					echo json_encode(array(
						'status' => 'failed',
						'message' => 'File Allow only PNG and JPG'
					 ));
					die();
				}
			
				if($filesize > 1000000){
					echo json_encode(array(
						'status' => 'failed',
						'message' => 'File Size Allow Only 1MB'
					 ));
					die();
				}
			
				/*===================
				INSERT POST
				===================*/
				$arrg = array(
				  'post_title'    => $_POST['post-title'],
				  'post_content'  => $_POST['post-desc'],
				  'post_status'   => 'draft',
				  'post_author'   => $_POST['user-id'],
				  'post_excerpt'  => $_POST['post-ex'],
				  'post_type'     => $_POST['post-type'],
				);
				$post_id = wp_insert_post( $arrg );
				
				if(!is_wp_error($post_id)){
					
					/*===================
					PROCESS IMAGE
					===================*/
					
					

					$movefile = wp_handle_upload($uploadedfile, $upload_overrides);

					
					$filename = $movefile['file'];
					$attachment = array(
						'post_mime_type' => $movefile['type'],
						'post_title' => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
						'post_content' => '',
						'post_status' => 'inherit',
						'guid' => $movefile['url']
					);
					$attachment_id = wp_insert_attachment( $attachment, $movefile['url'] );
					$attachment_data = wp_generate_attachment_metadata( $attachment_id, $filename );
					wp_update_attachment_metadata( $attachment_id, $attachment_data );
					
					update_post_meta($post_id,'_thumbnail_id',$attachment_id);
					
					/*===================
					EMAIL
					===================*/
					$header[] = 'Content-Type: text/html';
					$body = 'New Post.<br>
						<br>
						Please Moderate <a href="'.get_edit_post_link($post_id).'" target="_blank">here</a> 
					';
					wp_mail('dimensionjoker@gmail.com','New Post',$body,$header);

					echo json_encode( array (
						'status' => 'success',
						'message' => 'Horay success added ' . ucfirst($_POST['post-type'])	
					) );
				}
				else{
					echo json_encode( array (
						'status' => 'failed',
						'message' => 'Error, Please try again later!!'	
					) );
				}
			
				

		}
		else{
			echo json_encode( array (
					'status' => 'failed',
					'message' => 'Where are you come from!!!'	
				) );
		}
		die();

	}
  ?>
