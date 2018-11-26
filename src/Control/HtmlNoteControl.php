<?php
namespace Symbiotic\Customizer\Control;
use WP_Customize_Control;

class HtmlNoteControl extends WP_Customize_Control
{
	public $content = '';

	public function render_content()
	{
		if (isset($this->label)) {
			echo "<span class='customize-control-title'>{$this->label}</span>";
		}

		if (isset($this->content)) {
			echo $this->content;
		}

		if (isset($this->description)) {
			echo "<span class='description customize-control-description'>{$this->description}</span>";
		}

	}
}