<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * SnippetsSync
 *
 * @package		extended_forum_tags
 * @subpackage	ThirdParty
 * @category	Extension
 * @author		Bjorn Borresen / Elliot Lewis
 * @link		http://ee.bybjorn.com/eft
 */

class Snippetssync {

	var $module_name	= "Snippetssync";
	var $return_data	= '';
	
	public function Snippetssync()
	{
		// Make a local reference to the ExpressionEngine super object
		$this->EE =& get_instance();
	}
	
	public function url_sync()
	{

		$output		= '';
		$ajax_output= [];
		
		$ajax		= FALSE;
		if( !empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest') $ajax = TRUE;
		
		// Check for unique var in URL, error if not present
		// Check if matches config, error if not
		$snippetssync_sync_var	= $this->EE->config->item('snippetssync_sync_var');
		$sync_var				= $this->EE->input->get('sync', TRUE);

		if (!$sync_var || $sync_var == '' || $sync_var == 'CHANGEME' || $sync_var != $snippetssync_sync_var)
		{
			if ($ajax)
			{
				$ajax_output['success'] = FALSE;
				$ajax_output['error'] = 'URL incorrect, check the config folder in '.$this->module_name.'.';
				echo json_encode($ajax_output);
				exit;
			}
			else
			{
				$this->EE->output->show_user_error('submission', 'Check the <b>config</b> folder in '.$this->module_name.'.', 'Unique URL error');
			}
		}
		else
		{
			// run sync
			$this->EE->load->library('snippetslib');
			$success = $this->EE->snippetslib->sync_all();

			if(!$success)
			{
				if ($ajax)
				{
					$ajax_output['success'] = FALSE;
					$ajax_output['error'] = 'Sync failed: ' . $this->EE->snippetslib->error_message;
					echo json_encode($ajax_output);
					exit;
				}
				else
				{
					$this->EE->output->show_user_error('submission', 'Sync failed: ' . $this->EE->snippetslib->error_message, 'Sync error');
				}
			}
			else
			{
				if ($ajax)
				{
					$ajax_output['success'] = TRUE;
					echo json_encode($ajax_output);
					exit;
				}
				else
				{
					$this->EE->output->show_message(
					array(
						'title'		=> $this->module_name,
						'heading'	=> 'Snippets Sync via URL',
						'content'	=> 'Success'
						)
					);
				}
			}
		}

		return $output;
	}

}
/* End of file mod.snippetssync.php */
/* Location: ./system/expressionengine/third_party/download/mod.snippetssync.php */