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
        if($this->verify_settings())
        {
			$tmpl_basepath = $this->EE->config->slash_item('tmpl_file_basepath') . $this->EE->config->slash_item('site_short_name');


            $global_variables = $this->get_files($tmpl_basepath.'global_variables/');
            $snippets = $this->get_files($tmpl_basepath.'snippets/');

            foreach($global_variables as $global_variable_filename)
            {
                $this->EE->db->where('variable_name', $global_variable_filename);
                $this->EE->db->from('global_variables');
                $global_variable_data = file_get_contents($tmpl_basepath.'global_variables/'.$global_variable_filename);
                if($this->EE->db->count_all_results() == 0)
                {
                    $this->EE->db->insert('global_variables', array(
                        'variable_name' => $global_variable_filename,
                        'variable_data' => $global_variable_data,
                    ));
                }
                else
                {
                    $this->EE->db->where('variable_name', $global_variable_filename);
                    $this->EE->db->update('global_variables', array('variable_data' => $global_variable_data));
                }
            }


            foreach($snippets as $snippet_filename)
            {

                $this->EE->db->like('snippet_name', $snippet_filename);
                $this->EE->db->from('snippets');
                $snippet_data = file_get_contents($tmpl_basepath.'snippets/'.$snippet_filename);
                if($this->EE->db->count_all_results() == 0)
                {
                    $this->EE->db->insert('snippets', array(
                        'snippet_name' => $snippet_filename,
                        'snippet_contents' => $snippet_data,
                    ));

                }
                else
                {
                    $this->EE->db->where('snippet_name', $snippet_filename);
                    $this->EE->db->update('snippets', array('snippet_contents' => $snippet_data));
                }
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