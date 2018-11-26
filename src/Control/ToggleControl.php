<?php
namespace Symbiotic\Customizer\Control;
use WP_Customize_Control;

class ToggleControl extends WP_Customize_Control {
	/**
	 * The type of control being rendered
	 */
	public $type = 'toogle_switch';
	/**
	 * Enqueue our scripts and styles
	 */
	public function enqueue(){

		// @TODO: Be careful with paths
		// We should check
		// is it installed via composer (vendor) or as a plugin.
		// Check if we have a different folder structure etc. make it mostly dynamic

		// First get the library path relative to the theme path (won't work with plugin approach @TODO)
		// @link https://stackoverflow.com/a/43563176
		$symbioticPath =  str_replace(realpath(get_template_directory()),"",dirname(dirname(__FILE__)));
		// Build the assetPath
		$assetPath = strtolower(get_template_directory_uri() . $symbioticPath . '/assets');

		wp_enqueue_style( 'symbiotic-customizer-css', $assetPath . '/styles/customizer.css', array(), '1.1', 'all' );
	}
	/**
	 * Render the control in the customizer
	 */
	public function render_content(){
		?>
		<div class="toggle-switch-control">
			<div class="toggle-switch">
				<input type="checkbox" id="<?php echo esc_attr($this->id); ?>" name="<?php echo esc_attr($this->id); ?>" class="toggle-switch-checkbox" value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); checked( $this->value() ); ?>>
				<label class="toggle-switch-label" for="<?php echo esc_attr( $this->id ); ?>">
					<span class="toggle-switch-inner"></span>
					<span class="toggle-switch-switch"></span>
				</label>
			</div>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php if( !empty( $this->description ) ) { ?>
				<span class="customize-control-description"><?php echo esc_html( $this->description ); ?></span>
			<?php } ?>
		</div>
		<?php
	}
}