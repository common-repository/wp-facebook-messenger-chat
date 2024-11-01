<?php
/**
 * @var $field array
 * @var $settings array
 */
?>

<?php
/* Checking the field type to determine which HTML to output */
switch ( $field['type'] ) {
	case 'text':
		?>
        <input id="<?php esc_attr_e( 'wpfm_settings[general][' . $field['label_for'] . ']' ); ?>"
               name="<?php esc_attr_e( 'wpfm_settings[general][' . $field['label_for'] . ']' ); ?>"
               class="regular-text" value="<?php esc_attr_e( $settings['general'][$field['label_for']] ); ?>"/>
		<?php
		break;
	case 'checkbox':
		$checked = '';
		foreach ( $field['options'] as $option_value => $option_name ) {
			$value = get_option( 'wpfm_settings' )['general'][$field['label_for']];
			if ( $value[0] == $option_value ) { /*Check if value is the same as field value to check the box or not */
				$checked = 'checked';
			}
			?>
            <label>
            <input id="<?php esc_attr_e( 'wpfm_settings[general][' . $field['label_for'] . ']' ); ?>"
                   name="<?php esc_attr_e( 'wpfm_settings[general][' . $field['label_for'] . ']' ); ?>[]"
                   type="checkbox"
                   value="<?php echo $option_value ?>"
                   <?php echo $checked == 'checked' ? 'checked="checked"' : '' ?>>
                <?php echo $option_name; ?>
            </label>
			<?php
		}
		break;
}
if(isset($field['description']) && $field['description'] != '') {
    ?>
    <i><?php esc_attr_e($field['description']); ?></i>
    <?php
    if (isset($field['link']) && $field['link'] != '') {
        echo $field['link'];
    }
}
?>
