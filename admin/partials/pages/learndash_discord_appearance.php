<?php
$ets_learndash_discord_connect_button_bg_color    = sanitize_text_field( trim( get_option( 'ets_learndash_discord_connect_button_bg_color' ) ) );
$ets_learndash_discord_disconnect_button_bg_color = sanitize_text_field( trim( get_option( 'ets_learndash_discord_disconnect_button_bg_color' ) ) );
$ets_learndash_discord_loggedin_button_text = sanitize_text_field( trim( get_option( 'ets_learndash_discord_loggedin_button_text' ) ) );
$ets_learndash_discord_non_login_button_text = sanitize_text_field( trim( get_option( 'ets_learndash_discord_non_login_button_text' ) ) );
$ets_learndash_discord_disconnect_button_text = sanitize_text_field( trim( get_option( 'ets_learndash_discord_disconnect_button_text' ) ) );
?>
<form method="post" action="<?php echo esc_url( get_site_url().'/wp-admin/admin-post.php' ) ?>">
 <input type="hidden" name="action" value="learndash_discord_save_appearance_settings">
 <input type="hidden" name="current_url" value="<?php echo esc_url( ets_learndash_discord_get_current_screen_url() )?>" />
<?php wp_nonce_field( 'learndash_discord_appearance_settings_nonce', 'ets_learndash_discord_appearance_settings_nonce' ); ?>
  <table class="form-table" role="presentation">
	<tbody>
	<tr>
		<th scope="row"><?php esc_html_e( 'Connect/Login Button background color', 'connect-learndash-and-discord' ); ?></th>
		<td> <fieldset>
                        <input name="ets_learndash_discord_connect_button_bg_color" type="text" id="ets_learndash_discord_connect_button_bg_color"  class="wp-color-picker-field" value="<?php if ( isset ( $ets_learndash_discord_connect_button_bg_color ) ) { echo esc_attr( $ets_learndash_discord_connect_button_bg_color ); } else{   echo '#7bbc36'; } ?>" data-default-color="#7bbc36">
		</fieldset></td> 
	</tr>
	<tr>        
		<th scope="row"><?php esc_html_e( 'Disconnect Button background color', 'connect-learndash-and-discord' ); ?></th>
		<td> <fieldset>
		<input name="ets_learndash_discord_disconnect_button_bg_color" type="text" id="ets_learndash_discord_disconnect_button_bg_color" value="<?php if ( isset ( $ets_learndash_discord_disconnect_button_bg_color ) ) { echo esc_attr( $ets_learndash_discord_disconnect_button_bg_color ); }?>" data-default-color="#ff0000">
		</fieldset></td> 
	</tr>
	<tr>
		<th scope="row"><?php esc_html_e( 'Text on the Button for logged-in users', 'connect-learndash-and-discord' ); ?></th>
		<td> <fieldset>
		<input name="ets_learndash_discord_loggedin_button_text" type="text" id="ets_learndash_discord_loggedin_button_text" value="<?php if ( isset ( $ets_learndash_discord_loggedin_button_text ) ) { echo esc_attr( $ets_learndash_discord_loggedin_button_text ); }?>">
		</fieldset></td> 
	</tr>         
	<tr>
		<th scope="row"><?php esc_html_e( 'Text on the Button for non-login users', 'connect-learndash-and-discord' ); ?></th>
		<td> <fieldset>
		<input name="ets_learndash_discord_non_login_button_text" type="text" id="ets_learndash_discord_non_login_button_text" value="<?php if ( isset ( $ets_learndash_discord_non_login_button_text ) ) { echo esc_attr( $ets_learndash_discord_non_login_button_text ); } ?>">
		</fieldset></td> 
	</tr>	
	<tr>
		<th scope="row"><?php esc_html_e( 'Text on the Disconnect Button', 'connect-learndash-and-discord' ); ?></th>
		<td> <fieldset>
		<input name="ets_learndash_discord_disconnect_button_text" type="text" id="ets_learndash_discord_disconnect_button_text" value="<?php if ( isset ( $ets_learndash_discord_disconnect_button_text ) ) { echo esc_attr( $ets_learndash_discord_disconnect_button_text ); } ?>">
		</fieldset></td> 
	</tr>	
	</tbody>
  </table>
  <div class="bottom-btn">
	<button type="submit" name="appearance_submit" value="ets_submit" class="ets-submit ets-bg-green">
	  <?php esc_html_e( 'Save Settings', 'connect-learndash-and-discord' ); ?>
	</button>
  </div>
</form>
