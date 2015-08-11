<?php
global $wpdb;

//getting all settings
$vccs_auto_slide= get_option('vccs_auto_slide');
$vccs_slide_speed= get_option('vccs_slide_speed');

//if setting is naull then initial setting
if($vccs_auto_slide == ''){ $vccs_auto_slide= 'false';}
if($vccs_slide_speed == ''){ $vccs_slide_speed= 1000;}

//sanitize all post values
$add_opt_submit= sanitize_text_field( $_POST['add_opt_submit'] );
if($add_opt_submit!='') { 
    
	$vccs_auto_slide= sanitize_text_field( $_POST['vccs_auto_slide'] );
	$vccs_slide_speed= sanitize_text_field( $_POST['vccs_slide_speed'] );
	
	$saved= sanitize_text_field( $_POST['saved'] );


    if(isset($vccs_auto_slide) ) {
		update_option('vccs_auto_slide', $vccs_auto_slide);
    }
	
	if(isset($vccs_slide_speed) ) {
		update_option('vccs_slide_speed', $vccs_slide_speed);
    }
	
	if($saved==true) {
		
		$message='saved';
	} 
}
  
?>
  <?php
        if ( $message == 'saved' ) {
		echo ' <div class="added-success"><p><strong>Settings Saved.</strong></p></div>';
		}
   ?>
   
    <div class="wrap netgo-facebook-post-setting">
        <form method="post" id="settingForm" action="">
		<h2><?php _e('Client Carousel Setting','');?></h2>
		<table class="form-table">
		
	
	    <tr valign="top">
			<th scope="row" style="width: 370px;">
				<label for="vccs_munber_of_images"><?php _e('Carousel Auto scroll','');?></label>
			</th>
			<td>
			<select style="width:120px" name="vccs_auto_slide" id="vccs_auto_slide">
			<option value='true' <?php if($vccs_auto_slide == 'true') { echo "selected='selected'" ; } ?>>Enable</option>
			<option value='false' <?php if($vccs_auto_slide == 'false') { echo "selected='selected'" ; } ?>>Disable</option>
		   </select>
		   <br />
		   <em><?php _e('Enable/disable auto scroll.', ''); ?></em>
			</td>
		</tr>
		
		
		
		<tr valign="top">
			<th scope="row" style="width: 370px;">
				<label for="vccs_slide_speed"><?php _e('Carousel Slide Speed','');?></label>
			</th>
			<td>
			<input type="text" name="vccs_slide_speed" size="10" value="<?php echo $vccs_slide_speed; ?>" />
		   <br />
		   <em><?php _e('Slide Speed in millisecond. (Ex: 1000)', ''); ?></em>
			</td>
		</tr>
	   
		<tr>
		  <td>
		  <p class="submit">
		<input type="hidden" name="saved"  value="saved"/>
        <input type="submit" name="add_opt_submit" class="button-primary" value="Save Changes" />
		  <?php if(function_exists('wp_nonce_field')) wp_nonce_field('add_opt_submit', 'add_opt_submit'); ?>
        </p></td>
		</tr>
		</table>
		
        
       </form>
      
    </div>

