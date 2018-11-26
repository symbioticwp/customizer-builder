<?php
namespace Symbiotic\Customizer\Control;
use WP_Customize_Control;

class Select2Control extends WP_Customize_Control
{
	public $type = 'dropdown_select2';

	private $multiselect = false;
	private $placeholder = 'Please choose...';

	public function __construct( $manager, $id, $args = array(), $options = array() ) {
		parent::__construct( $manager, $id, $args );
		// Check if this is a multi-select field
		if ( isset( $this->input_attrs['multiselect'] ) && $this->input_attrs['multiselect'] ) {
			$this->multiselect = true;
		}
		// Check if a placeholder string has been specified
		if ( isset( $this->input_attrs['placeholder'] ) && $this->input_attrs['placeholder'] ) {
			$this->placeholder = $this->input_attrs['placeholder'];
		}
	}

	public function enqueue() {

	    // @TODO: Be careful with paths
        // We should check
        // is it installed via composer (vendor) or as a plugin.
        // Check if we have a different folder structure etc. make it mostly dynamic

        // First get the library path relative to the theme path (won't work with plugin approach @TODO)
        // @link https://stackoverflow.com/a/43563176
		$symbioticPath =  str_replace(realpath(get_template_directory()),"",dirname(dirname(__FILE__)));
	    // Build the assetPath
		$assetPath = strtolower(get_template_directory_uri() . $symbioticPath . '/assets');

		wp_enqueue_script( 'admin-select2-js', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js', array( 'jquery' ), '4.0.6', true );
		wp_enqueue_script( 'symbiotic-customizer-js', $assetPath . '/scripts/customizer.js', array( 'admin-select2-js' ), '1.0', true );
		wp_enqueue_style( 'symbiotic-customizer-css', $assetPath . '/styles/customizer.css', array(), '1.1', 'all' );
		wp_enqueue_style( 'admin-select2-css', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css', array(), '4.0.6', 'all' );
    }

	public function render_content() {
		$defaultValue = $this->value();
		if ( $this->multiselect ) {
			$defaultValue = explode( ',', $this->value() );
		}
		?>
        <div class="dropdown_select2_control">
			<?php if( !empty( $this->label ) ) { ?>
                <label for="<?php echo esc_attr( $this->id ); ?>" class="customize-control-title">
					<?php echo esc_html( $this->label ); ?>
                </label>
			<?php } ?>
			<?php if( !empty( $this->description ) ) { ?>
                <span class="customize-control-description"><?php echo esc_html( $this->description ); ?></span>
			<?php } ?>
            <input type="hidden" id="<?php echo esc_attr( $this->id ); ?>" class="customize-control-dropdown-select2" value="<?php echo esc_attr( $this->value() ); ?>" name="<?php echo esc_attr( $this->id ); ?>" <?php $this->link(); ?> />
            <select name="select2-list-<?php echo ( $this->multiselect ? 'multi[]' : 'single' ); ?>" class="customize-control-select2" data-placeholder="<?php echo $this->placeholder; ?>" <?php echo ( $this->multiselect ? 'multiple="multiple" ' : '' ); ?>>
				<?php
				if ( !$this->multiselect ) {
					// When using Select2 for single selection, the Placeholder needs an empty <option> at the top of the list for it to work (multi-selects dont need this)
					echo '<option></option>';
				}
				foreach ( $this->choices as $key => $value ) {
					if ( is_array( $value ) ) {
						echo '<optgroup label="' . esc_attr( $key ) . '">';
						foreach ( $value as $optgroupkey => $optgroupvalue ) {
							echo '<option value="' . esc_attr( $optgroupkey ) . '" ' . ( in_array( esc_attr( $optgroupkey ), $defaultValue ) ? 'selected="selected"' : '' ) . '>' . esc_attr( $optgroupvalue ) . '</option>';
						}
						echo '</optgroup>';
					}
					else {
						echo '<option value="' . esc_attr( $key ) . '" ' . selected( esc_attr( $key ), $defaultValue, false )  . '>' . esc_attr( $value ) . '</option>';
					}
				}
				?>
            </select>
        </div>
		<?php
	}
}