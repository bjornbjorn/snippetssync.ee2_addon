<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * AddonBakery Common
 *
 * All classes extend this
 *
 */
 
class Ab_Common {

    /**
     * @var Devkit_code_completion;
     */
    protected $EE;

    public function __construct()
    {
        $this->EE =& $this->get_ee_instance();
        $this->EE->load->add_package_path(PATH_THIRD); // add common lib folder to package path
    }

    /**
     * @return Devkit_code_completion
     */
    private function get_ee_instance()
    {
        return get_instance();
    }
}