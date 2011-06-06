<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once PATH_THIRD.'libraries/ab/ab_libbase'.EXT;

/**
 * EE Library - Functionality related to snippets / global variables
 *
 * Developer: Bjorn Borresen / AddonBakery
 * Date: 21.03.11
 * Time: 10:16
 *  
 */
 
class Snippetslib extends Ab_LibBase {

    public $error_message = '';
    public $last_sync_log = array();

    public function verify_settings()
    {
        if($this->EE->config->item('save_tmpl_files') != 'y')
        {
            $this->error_message = 'Save templates as files must be set to Yes in Global Template Preferences';
            return FALSE;
        }
        else
        {
            $tmpl_basepath = $this->EE->config->slash_item('tmpl_file_basepath') . $this->EE->config->slash_item('site_short_name');
            if(!($tmpl_basepath != $this->EE->config->slash_item('site_short_name') && file_exists($tmpl_basepath)))
            {
                $this->error_message = 'Template basepath not defined - or not found ('.$tmpl_basepath.')';
                return FALSE;
            }
        }

        return TRUE;    // everything OK
    }

    public function sync_all()
    {
        $this->last_sync_log['globals'] = array();
        $this->last_sync_log['snippets'] = array();

        if($this->verify_settings())
        {
			$tmpl_basepath = $this->EE->config->slash_item('tmpl_file_basepath') . $this->EE->config->slash_item('site_short_name');

            $global_variables = $this->get_files($tmpl_basepath.'global_variables/');
            $snippets = $this->get_files($tmpl_basepath.'snippets/');

            foreach($global_variables as $global_variable_filename)
            {
                $global_variable_name = str_replace('.html', '', $global_variable_filename);

                $this->EE->db->where('variable_name', $global_variable_name);
                $this->EE->db->from('global_variables');
                $global_variable_data = file_get_contents($tmpl_basepath.'global_variables/'.$global_variable_filename);

                if($this->EE->db->count_all_results() == 0)
                {
                    $this->EE->db->insert('global_variables', array(
                        'variable_name' => $global_variable_name,
                        'variable_data' => $global_variable_data,
                    ));
                }
                else
                {
                    $this->EE->db->where('variable_name', $global_variable_name);
                    $this->EE->db->update('global_variables', array('variable_data' => $global_variable_data));
                }

                $this->last_sync_log['globals'][] = $global_variable_name;
            }

            foreach($snippets as $snippet_filename)
            {
                $snippet_name = str_replace('.html', '', $snippet_filename);

                $this->EE->db->like('snippet_name', $snippet_name);
                $this->EE->db->from('snippets');
                $snippet_data = file_get_contents($tmpl_basepath.'snippets/'.$snippet_filename);

                if($this->EE->db->count_all_results() == 0)
                {
                    $this->EE->db->insert('snippets', array(
                        'snippet_name' => $snippet_name,
                        'snippet_contents' => $snippet_data,
                    ));

                }
                else
                {
                    $this->EE->db->where('snippet_name', $snippet_name);
                    $this->EE->db->update('snippets', array('snippet_contents' => $snippet_data));
                }

                $this->last_sync_log['snippets'][] = $snippet_name;
            }

            return TRUE;

        }
        else
        {
            return FALSE;
        }
    }


    /**
	 * Get dirs/files from a source_dir
	 *
	 * @param $source_dir
	 */
	private function get_files($source_dir)
	{
		$files = array();

		$fp = @opendir($source_dir);

		if ($fp)
		{
			while (FALSE !== ($file = readdir($fp)))
			{
				if (is_file($source_dir.$file))
				{
					$files[] = $file;
				}
			}
			closedir($fp);
		}
		else
		{
			show_error("Could not open dir: " . $source_dir);
		}
		return $files;
	}


}