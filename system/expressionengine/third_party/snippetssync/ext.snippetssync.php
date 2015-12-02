<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if(!class_exists('Ab_ExtBase')) {
    require_once PATH_THIRD.'snippetssync/libraries/ab/ab_extbase.php';
}

/**
 * SnippetsSync
 *
 * @package		extended_forum_tags
 * @subpackage	ThirdParty
 * @category	Extension
 * @author		Bjorn Borresen
 * @link		http://ee.bybjorn.com/eft
 */
class Snippetssync_ext extends Ab_ExtBase {

    public $settings        = array();

    public $name            = 'SnippetsSync';
    public $version         = '1.2.1';
    public $description     = 'Enables you to work with snippets & global variables as files while developing';
    public $settings_exist  = 'n';
    public $docs_url        = 'http://ee.bybjorn.com/snippetssync';

    protected $register_hooks = array(
			'sessions_start' => 'on_sessions_start',
    );

    /**
     * Constructor
     *
     * @paramarray of settings
     */
    public function __construct($settings='')
    {
		parent::__construct($settings);
    }

    public function activate_extension()
    {
        $this->EE->load->library('snippetslib');
        if(!$this->EE->snippetslib->verify_settings())
        {
            show_error($this->EE->snippetslib->error_message.". Please fix this  before enabling the extension.");
        }
        else
        {
            parent::activate_extension();
        }
    }

	//
	// HOOKS GO HERE
	//

    public function on_sessions_start($ref)
    {
        // load local config if none of these values aren't already specified in a master config
        if(!isset($this->EE->config->config['snippetssync_production_mode'])
            && !isset($this->EE->config->config['snippetssync_snippet_prefix'])
            && !isset($this->EE->config->config['snippetssync_global_variable_prefix'])) {
            $this->EE->load->config('snippetssync');
        }

        // snippetssync_production_mode_override for backwards compatibility
        if(!$this->EE->config->item('snippetssync_production_mode_override') && !$this->EE->config->item('snippetssync_production_mode'))
        {
            $this->EE->load->library('snippetslib');
            $success = $this->EE->snippetslib->sync_all();
        }
    }

}

/* End of file ext.extended_ee.php */
/* Location: ./system/expressionengine/third_party/extended_ee/ext.extended_ee.php */
