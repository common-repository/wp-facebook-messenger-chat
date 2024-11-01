<div class="wrap">
	<div id="icon-options-general" class="icon32"><br /></div>
    <h1><?php esc_html_e( WPFM_NAME ); ?> Settings</h1>
    
    <form method="post" action="options.php">
        <?php settings_fields( 'wpfm_settings' ); ?>
        <?php do_settings_sections( 'wpfm_settings' ); ?>

        <a href="https://wordpress.org/plugins/wp-facebook-messenger-chat/">For detailed instructions click here.</a>
        <p class="submit">
			<input type="submit" name="submit" id="submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes' ); ?>" />
		</p>
	</form>
</div> <!-- .wrap -->
