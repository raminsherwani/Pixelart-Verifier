<?php

/** The user page side functions **/
function px_verify_show_extra_profile_fields($user) { 
	$user_id = $user->ID;
	$result;
	if (isset($_GET['action']) && $_GET['action'] == 'refreshEnvatoData') {
		$result = px_verify_refresh_envato_data($user_id);
	}else if (isset($_GET['action']) && $_GET['action'] == 'upgradeAqua') {
		$result = px_verify_upgrade_aqua($user_id);
	}else {
		$result = '';
	}
	?>
	
	<h3>Envato User Information</h3>

	<table class="form-table">
		<tr>
			<th>
				<label for="px_envato_purchase_code">Purchase Code</label>
			</th>
			<td>
				<input type="text" name="px_envato_purchase_code" id="px_envato_purchase_code" value="<?php echo esc_attr( get_the_author_meta( 'px_envato_purchase_code', $user_id ) ); ?>" class="regular-text" /><br />
			</td>
		</tr>
		
		<tr>
			<th>
				<label for="px_envato_username">Envato Username</label>
			</th>
			<td>
				<input type="text" name="px_envato_username" id="px_envato_username" value="<?php echo esc_attr( get_the_author_meta( 'px_envato_username', $user_id ) ); ?>" class="regular-text"  readonly /><br />
			</td>
		</tr>
		
		<tr>
			<th>
				<label for="px_envato_purchase_date">Purchase Date</label>
			</th>
			<td>
				<input type="text" name="px_envato_purchase_date" id="px_envato_purchase_date" value="<?php echo esc_attr( get_the_author_meta( 'px_envato_purchase_date', $user_id ) ); ?>" class="regular-text"  readonly /><br />
			</td>
		</tr>
		
		<tr>
			<th>
				<label for="px_envato_item">Item Name</label>
			</th>
			<td>
				<input type="text" name="px_envato_item" id="px_envato_item" value="<?php echo esc_attr( get_the_author_meta( 'px_envato_item', $user_id ) ); ?>" class="regular-text"  readonly /><br />
			</td>
		</tr>
		
		<tr>
			<th>
				<label for="px_envato_license">License</label>
			</th>
			<td>
				<input type="text" name="px_envato_license" id="px_envato_license" value="<?php echo esc_attr( get_the_author_meta( 'px_envato_license', $user_id ) ); ?>" class="regular-text"  readonly /><br />
			</td>
		</tr>
		
		<tr>
			<th>
				<label for="px_envato_support_amount">Support Amount</label>
			</th>
			<td>
				<input type="text" name="px_envato_support_amount" id="px_envato_support_amount" value="<?php echo esc_attr( get_the_author_meta( 'px_envato_support_amount', $user_id ) ); ?>" class="regular-text"  readonly /><br />
			</td>
		</tr>
		
		<tr>
			<th>
				<label for="px_envato_support_until">Support Until</label>
			</th>
			<td>
				<input type="text" name="px_envato_support_until" id="px_envato_support_until" value="<?php echo esc_attr( get_the_author_meta( 'px_envato_support_until', $user_id ) ); ?>" class="regular-text"  readonly /><br />
			</td>
		</tr>
		
		<tr>
			<th>
			</th>
			<td>
				<a href="?user_id=<?php echo $user_id; ?>&action=refreshEnvatoData" class="button" >Refresh Envato Data</a>
				<a href="?user_id=<?php echo $user_id; ?>&action=upgradeAqua" class="button" >Upgrade from Aqua Verifier</a>
				<?php echo $result; ?>
			</td>
		</tr>
	</table>
	
<?php
}


// Refresh the Envato Details
function px_verify_refresh_envato_data($user_id) {
	$code = get_the_author_meta('px_envato_purchase_code', $user_id);
	$verify = px_verify_purchase($code, false);
	
	if( !is_wp_error($verify)) {
		update_user_meta( $user_id, 'px_envato_username', $verify['px_envato_username'] );
		update_user_meta( $user_id, 'px_envato_purchase_date', $verify['px_envato_purchase_date'] );
		update_user_meta( $user_id, 'px_envato_purchase_code', $verify['px_envato_purchase_code'] );
		update_user_meta( $user_id, 'px_envato_license', $verify['px_envato_license'] );
		update_user_meta( $user_id, 'px_envato_item', $verify['px_envato_item'] );
		update_user_meta( $user_id, 'px_envato_support_amount', $verify['px_envato_support_amount'] );
		update_user_meta( $user_id, 'px_envato_support_until', $verify['px_envato_support_until'] );
		
		return '<div id="message" class="updated notice is-dismissible"><p><strong>Refreshed successfully!</strong></p></div>';
	}else {
			$error_string = $verify->get_error_message();
			return '<div id="message" class="error notice is-dismissible"><p>' . $error_string . '</p></div>';
	}
}

// Upgrade from the Aqua Verifier
function px_verify_upgrade_aqua($user_id) {
	if ( count(get_user_meta($user_id,  'purchased_items')) === 0) {
		return '<div id="message" class="error notice is-dismissible"><p><strong>Aqua Verifier was not used for this user</strong><br>Enter the purchase code for this user manually</p></div>';
	}else {
		$data = get_user_meta($user_id,  'purchased_items');
		$code = reset($data);
		$code = reset($code);
		$code = $code['purchase_code'];
		$verify = px_verify_purchase($code, false);
		
		if( !is_wp_error($verify)) {
			update_user_meta( $user_id, 'px_envato_username', $verify['px_envato_username'] );
			update_user_meta( $user_id, 'px_envato_purchase_date', $verify['px_envato_purchase_date'] );
			update_user_meta( $user_id, 'px_envato_purchase_code', $verify['px_envato_purchase_code'] );
			update_user_meta( $user_id, 'px_envato_license', $verify['px_envato_license'] );
			update_user_meta( $user_id, 'px_envato_item', $verify['px_envato_item'] );
			update_user_meta( $user_id, 'px_envato_support_amount', $verify['px_envato_support_amount'] );
			update_user_meta( $user_id, 'px_envato_support_until', $verify['px_envato_support_until'] );
			
			return '<div id="message" class="updated notice is-dismissible"><p><strong>Upgraded the data from Aqua Verifier!</strong></p></div>';
		}else {
				$error_string = $verify->get_error_message();
				return '<div id="message" class="error notice is-dismissible"><p>' . $error_string . '</p></div>';
		}
	}
}


function px_verify_save_extra_profile_fields($user_id) {

	if (!current_user_can( 'edit_user', $user_id) ) {
		return false;
	}else {
		update_user_meta( $user_id, 'px_envato_username', $_POST['px_envato_username'] );
		update_user_meta( $user_id, 'px_envato_purchase_date', $_POST['px_envato_purchase_date'] );
		update_user_meta( $user_id, 'px_envato_purchase_code', $_POST['px_envato_purchase_code'] );
		update_user_meta( $user_id, 'px_envato_license', $_POST['px_envato_license'] );
		update_user_meta( $user_id, 'px_envato_item', $_POST['px_envato_item'] );
		update_user_meta( $user_id, 'px_envato_support_amount', $_POST['px_envato_support_amount'] );
		update_user_meta( $user_id, 'px_envato_support_until', $_POST['px_envato_support_until'] );
	}
}


add_action('show_user_profile', 'px_verify_show_extra_profile_fields', 999);
add_action('edit_user_profile', 'px_verify_show_extra_profile_fields', 999);

add_action('personal_options_update', 'px_verify_save_extra_profile_fields');
add_action('edit_user_profile_update', 'px_verify_save_extra_profile_fields');

?>