<?php
require_once 'ab_common'.EXT;

/**
 * Base for extensions
 *
 */
 
class Ab_ExtBase extends Ab_Common
{
    public $settings        = array();
    protected $register_hooks;  // array of hooks => methods to register

	public function __construct($settings='')
	{
		parent::__construct();  // run constructor which will handle get_instance() etc.
		$this->settings = $settings;
    }

    /**
     * Activate the extension
     *
     * This funciton is run on install and will register all hooks and
     * add custom member fields.
     *
     */
	public function activate_extension()
	{
		 // -------------------------------------------------
		 // Register the hooks needed for this extension
		 // -------------------------------------------------

		$class_name = get_class($this);
		foreach($this->register_hooks as $hook => $method)
		{
			$data = array(
				'class'        => $class_name,
				'method'       => $method,
				'hook'         => $hook,
				'settings'     => "",
				'priority'     => 10,
				'version'      => $this->version,
				'enabled'      => "y"
			);
			$this->EE->db->insert('extensions', $data);
		}
	}

	/**
	 * Update the extension
	 *
	 * @param $current current version number
	 * @return boolean indicating whether or not the extension was updated
	 */
	public function update_extension($current='')
	{
	    if ($current == '' OR $current == $this->version)
	    {
	        return FALSE;
	    }

	    return FALSE;
	    // update code if version differs here
	}

	/**
	 * Settings
	 */
	public function settings()
	{

	}

    /**
	 * Disable the extention
	 *
	 * @return unknown_type
	 */
	public function disable_extension()
	{
		$this->EE->db->delete('extensions', array('class'=>get_class($this)));
	}
}
