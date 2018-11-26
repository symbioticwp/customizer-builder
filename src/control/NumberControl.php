<?php
namespace Symbiotic\Customizer\Control;
use WP_Customize_Control;

class NumberControl extends WP_Customize_Control
{
	public $type = 'number';

	public function render_content()
	{	?>
		<label>
			<span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
			<input type="number" <?php $this->link(); ?> value="<?php echo intval($this->value()); ?>"/>
		</label>
		<?php
	}

}