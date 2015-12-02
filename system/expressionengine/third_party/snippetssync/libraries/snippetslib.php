<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');


if(!class_exists('Ab_LibBase')) {
    require_once PATH_THIRD.'snippetssync/libraries/ab/ab_libbase.php';
}


/**
 * EE Library - Functionality related to snippets / global variables
 *
 * Developer: Bjorn Borresen / WDA
 * Date: 21.03.11
 * Time: 10:16
 *
 */

class Snippetslib extends Ab_LibBase {

	public $error_message = '';
	public $last_sync_log = array();

	private $sn_path = '';
	private $gv_path = '';
	private $tmpl_basepath = '';
	private $sn_prefix = '';
	private $gv_prefix = '';

	public function __construct() {

		parent::__construct();

		// setup the pathing to all required directories.
		$this->tmpl_basepath = $this->EE->config->slash_item('tmpl_file_basepath') . $this->EE->config->slash_item('site_short_name');
		$this->sn_path = $this->tmpl_basepath . ( $this->EE->config->slash_item('snippetssync_sn_folder') ? $this->EE->config->slash_item('snippetssync_sn_folder') : "snippets/" );
		$this->gv_path = $this->tmpl_basepath . ( $this->EE->config->slash_item('snippetssync_gv_folder') ? $this->EE->config->slash_item('snippetssync_gv_folder') : "global_variables/" );
	}

    private function log_error($msg)
    {
        $this->EE->load->library('logger');
        $this->EE->logger->developer('SnippetsSync: '.$msg, TRUE);
    }

	public function verify_settings()
	{

        if(!($this->tmpl_basepath != $this->EE->config->slash_item('site_short_name') && file_exists($this->tmpl_basepath)))
        {
            $this->error_message = 'Template basepath not defined - or not found ('.$this->tmpl_basepath.')';
            return FALSE;
        }

		// check that parent dir is writeable.
		if ( substr(sprintf('%o', fileperms( $this->tmpl_basepath )) , -4) < DIR_WRITE_MODE )
		{
			( "Your template directory (".$this->tmpl_basepath.") needs to be writable." );
		}

		// check if the global_vars and snippets dirs exists, else create them now.
		// check global vars dir
		if ( !file_exists($this->gv_path) )
		{
			@mkdir( $this->gv_path );
		}
		if ( !$this->mk_perms( $this->gv_path ) )
		{
			$this->log_error( "Could not make the global variables directory writeable." );
		}

		// check snippets dir
		if ( !file_exists($this->sn_path) )
		{
			@mkdir( $this->sn_path );
		}
		if ( !$this->mk_perms( $this->sn_path ) )
		{
			$this->log_error( "Could not make the snippets directory writeable." );
		}

		//check for prefixes set on EECMS config.php
		if ( is_string($this->EE->config->item('snippetssync_snippet_prefix'))  )
		{
			$this->sn_prefix = $this->EE->config->item('snippetssync_snippet_prefix');
		}
		if ( is_string($this->EE->config->item('snippetssync_global_variable_prefix'))  )
		{
			$this->gv_prefix = $this->EE->config->item('snippetssync_global_variable_prefix');
		}

		return TRUE;	// everything OK
	}

	public function sync_all()
	{
		$this->last_sync_log['globals'] = array();
		$this->last_sync_log['snippets'] = array();
        $this->last_sync_log['ignored'] = array();

		if($this->verify_settings())
		{

			$global_variables = $this->get_files($this->gv_path);
			$snippets = $this->get_files($this->sn_path);

			// regex arrays, used to create valid snippet and global variable names.
			$search = array(
				"/\..*$/ui", //strips extension.
			);
			$replace= array(
				"", // replace extension with nothing
			);

			foreach($global_variables as $global_variable_filename)
			{
				$global_variable_name = $this->gv_prefix.preg_replace( $search , $replace , $global_variable_filename );

                if($this->is_legal_global_var_name($global_variable_name)) {
                    $this->EE->db->where('variable_name', $global_variable_name);
                    $this->EE->db->from('global_variables');

                    $global_variable_data = file_get_contents($this->gv_path.$global_variable_filename);

                    if($this->EE->db->count_all_results() == 0)
                    {
                        $this->EE->db->insert('global_variables', array(
                            'site_id' => $this->EE->config->item('site_id'),
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
                } else {
                    $this->last_sync_log['ignored'][] = '/global_variables/'.$global_variable_filename;
                }
			}

			foreach($snippets as $snippet_filename)
			{
				$snippet_name = $this->sn_prefix.preg_replace( $search , $replace , $snippet_filename );

                if($this->is_legal_global_var_name($snippet_name)) {
                    $this->EE->db->where('snippet_name', $snippet_name);
                    $this->EE->db->from('snippets');
                    $snippet_data = file_get_contents($this->sn_path.$snippet_filename);

                    if($this->EE->db->count_all_results() == 0)
                    {
                        $this->EE->db->insert('snippets', array(
                            'site_id' => $this->EE->config->item('site_id'),
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

                } else {
                    $this->last_sync_log['ignored'][] = '/snippets/'.$snippet_filename;
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
     * Check if a string is a legal global var name
     *
     * @param $str
     */
    private function is_legal_global_var_name($str)
    {
        if($str == '') {
            return FALSE;
        }
        return (preg_match("#^[a-zA-Z0-9_\-/]+$#i", $str));
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
					if (strpos($file, '.') !== 0) {
						$files[] = $file;
					}
				}
			}
			closedir($fp);
		}
		else
		{
			$this->log_error("Could not open dir: " . $source_dir);
		}

		return $files;
	}

	/**
	 * Check permissions of dir/file for desired permissions.
	 * If perms differ then set dir/file to desired perms.
	 *
	 * @param $dir
	 * @param $perms (default: 0777)
	 */
	private function mk_perms( $dir , $perms = DIR_WRITE_MODE )
	{
		clearstatcache();

        if ( is_dir($dir) && octdec(substr(sprintf('%o', @fileperms( $dir )), -4)) < $perms )
		{
			if( @chmod( $dir , $perms ) )
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}

		return TRUE;
	}

}
