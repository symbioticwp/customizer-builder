<?php
namespace Symbiotic\Customizer;

use \WP_Customize_Manager;
use \Closure;

class CustomizerBuilder
{
	private $wp_customize;

	private $priorityCounter = 0;
	private $noteCounter = 0;
	private $currentSection = null;
	private $currentPanel = null;

	private $queuedControlName = null;
	private $queuedSettingArgumentsArray = [];
	private $queuedControlArgumentsArray = [];
	private $queuedCustomControlClass = null;

	public function __construct(WP_Customize_Manager $wp_customize)
	{
		$this->wp_customize = $wp_customize;
	}

	public function __destruct()
	{
		$this->addQueuedControl();
	}

	private function initializeNewControl($name, $label, $customControlClass = null)
	{
		if(is_null($this->currentSection)) {
			throw new \Exception("You need to add a section before adding a control");
		}

		$this->addQueuedControl();

		$this->queuedControlName = $name;

		if(!is_null($customControlClass)) {
			$this->queuedCustomControlClass = $customControlClass;
		}

		$this->setControlArgument("label", $label);
		$this->setControlArgument("section", $this->currentSection);
		$this->setControlArgument("priority", $this->priorityCounter++);
	}

	private function addQueuedControl()
	{
		if($this->hasQueuedControl()) {
			$this->wp_customize->add_setting($this->queuedControlName, $this->queuedSettingArgumentsArray);

			if(!is_null($this->queuedCustomControlClass)) {
				// Build your classname according the namespace
				//$class = __NAMESPACE__ . '\\Control\\' . $this->queuedCustomControlClass;
				//var_dump($class);
				$class = $this->queuedCustomControlClass;
				$this->wp_customize->add_control(new $class($this->wp_customize, $this->queuedControlName, $this->queuedControlArgumentsArray));
			}
			else {
				$this->wp_customize->add_control($this->queuedControlName, $this->queuedControlArgumentsArray);
			}
		}

		$this->queuedControlName = null;
		$this->queuedSettingArgumentsArray = [];
		$this->queuedControlArgumentsArray = [];
		$this->queuedCustomControlClass = null;
	}

	private function hasQueuedControl()
	{
		return !is_null($this->queuedControlName);
	}

	private function setSettingsArgument($key, $value)
	{
		if(!$this->hasQueuedControl()) {
			throw new \Exception("There is no queued control");
		}

		$this->queuedSettingArgumentsArray[$key] = $value;
	}

	private function setControlArgument($key, $value)
	{
		if(!$this->hasQueuedControl()) {
			throw new \Exception("There is no queued control");
		}

		$this->queuedControlArgumentsArray[$key] = $value;
	}

	/**
	 * Create a new panel and set it as the current panel
	 * @param string $name Unique ID of this panel
	 * @param string $title Title for this panel
	 * @param Closure $closure
	 */
	public function addPanel($name, $title, Closure $closure = null)
	{
		$this->addQueuedControl();

		$this->wp_customize->add_panel($name, [
			'title' => $title,
			'priority' => $this->priorityCounter++,
		]);

		$this->currentPanel = $name;

		if(!is_null($closure)) {
			$closure();
		}
	}

	/**
	 * Create a new section, set it as the current section and add it to the current panel
	 * @param string $name Unique ID of this section
	 * @param string $title Title for this section
	 * @param Closure $closure
	 */
	public function addSection($name, $title, Closure $closure = null)
	{
		$this->addQueuedControl();

		$this->wp_customize->add_section($name, [
			'title' => $title,
			'panel' => $this->currentPanel ? $this->currentPanel : '',
			'priority' => $this->priorityCounter++,
		]);

		$this->currentSection = $name;

		if(!is_null($closure)) {
			$closure();
		}
	}

	/**
	 * Add a single-line text box to the current section
	 * @param string $name Name of this control
	 * @param string $label Label for this control
	 * @param bool $allowHtml
	 */
	public function addTextBox($name, $label, $allowHtml = false)
	{
		$this->initializeNewControl($name, $label);

		if($allowHtml) {
			$this->setSanitizeCallback("wp_kses_post");
		}

		return $this;
	}

	/**
	 * Add a multi-line text box (text area) to the current section
	 * @param string $name Name of this control
	 * @param string $label Label for this control
	 * @param bool $allowHtml
	 */
	public function addTextArea($name, $label, $allowHtml = false)
	{
		$this->initializeNewControl($name, $label, "Symbiotic\Customizer\Control\TextareaControl");

		if($allowHtml) {
			$this->setSanitizeCallback("wp_kses_post");
		}

		return $this;
	}

	/**
	 * Add a text box to the current section that uses 'esc_url_raw' as sanitize callback
	 * @param string $name Name of this control
	 * @param string $label Label for this control
	 */
	public function addUrl($name, $label)
	{
		$this->initializeNewControl($name, $label);

		$this->setSanitizeCallback("esc_url_raw");

		return $this;
	}

	/**
	 * Add a checkbox input to the current section
	 * @param string $name Name of this control
	 * @param string $label Label for this control
	 */
	public function addCheckbox($name, $label)
	{
		$this->initializeNewControl($name, $label);

		$this->setControlArgument("type", "checkbox");
		$this->setDefault(false);

		return $this;
	}

	/**
	 * Add an image control to the current section that saves the image url
	 * @param string $name Name of this control
	 * @param string $label Label for this control
	 */
	public function addImageUrl($name, $label)
	{
		$this->initializeNewControl($name, $label, "WP_Customize_Image_Control");

		$this->setSanitizeCallback("esc_url_raw");

		return $this;
	}

	/**
	 * Add an image control to the current section that saves the image id
	 * @param string $name Name of this control
	 * @param string $label Label for this control
	 */
	public function addImageId($name, $label)
	{
		$this->initializeNewControl($name, $label, "WP_Customize_Media_Control");

		$this->setControlArgument("mime_type", "image");

		return $this;
	}

	/**
	 * Add a number input to the current section
	 * @param string $name Name of this control
	 * @param string $label Label for this control
	 */
	public function addNumber($name, $label)
	{
		$this->initializeNewControl( $name, $label, "Symbiotic\Customizer\Control\NumberControl" );

		$this->setControlArgument("type", "number");

		return $this;
	}

	public function addSelect($name, $label, $choices = []) {
		$this->initializeNewControl( $name, $label, "Symbiotic\Customizer\Control\Select2Control" );

		$this->setControlArgument("type", "select");
		$this->setControlArgument("choices", $choices);

		return $this;
	}

	public function addToggle($name, $label) {
		$this->initializeNewControl( $name, $label, "Symbiotic\Customizer\Control\ToggleControl" );
		$this->setControlArgument("type", "checkbox");
		return $this;
	}

	/**
	 * Display a note in the customizer
	 * @param string $label Label for this note (accepts html)
	 * @param string $content Content of this note (accepts html)
	 */
	public function displayNote($label, $content = "")
	{
		// Every control needs a unique setting, even if the setting wont be used
		$noteName = "cb__" . $this->noteCounter++;

		$this->initializeNewControl($noteName, $label, "Symbiotic\Customizer\Control\HtmlNoteControl");

		$this->setControlArgument("content", $content);
	}

	/**
	 * Display a horizontal rule in the customizer
	 */
	public function displayHr()
	{
		$this->displayNote("", "<hr>");
	}

	/**
	 * Set the description of the currently queued control
	 * @param string $string Description
	 */
	public function setDescription($string)
	{
		$this->setControlArgument("description", $string);

		return $this;
	}

	/**
	 * Set the default value of the currently queued control
	 * @param string $string Default value
	 */
	public function setDefault($string)
	{
		$this->setSettingsArgument("default", $string);

		return $this;
	}

	/**
	 * Set the transport of the currently queued control
	 * @param string $string Transport
	 */
	public function setTransport($string)
	{
		$this->setSettingsArgument("transport", $string);

		return $this;
	}

	/**
	 * Set the capability of the currently queued control
	 * @param string $string Capability
	 */
	public function setCapability($string)
	{
		$this->setSettingsArgument("capability", $string);

		return $this;
	}

	/**
	 * Set the sanitize callback of the currently queued control
	 * @param string $string Sanitize callback
	 */
	public function setSanitizeCallback($string)
	{
		$this->setSettingsArgument("sanitize_callback", $string);

		return $this;
	}

	public function setActiveCallBack($func) {
		$this->setSettingsArgument("active_callback", $func);

		return $this;
	}

}