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
	
	private $sn_path = '';
	private $gv_path = '';
	private $tmpl_basepath = '';

	public function __construct() {
		
		parent::__construct();
		
		// setup the pathing to all required directories.
		$this->tmpl_basepath = $this->EE->config->slash_item('tmpl_file_basepath') . $this->EE->config->slash_item('site_short_name');
		$this->sn_path = $this->tmpl_basepath . ( $this->EE->config->slash_item('snippetssync_sn_folder') ? $this->EE->config->slash_item('snippetssync_sn_folder') : "snippets/" );
		$this->gv_path = $this->tmpl_basepath . ( $this->EE->config->slash_item('snippetssync_gv_folder') ? $this->EE->config->slash_item('snippetssync_gv_folder') : "global_variables/" );
	}

	public function verify_settings()
	{
		// checks if templates are saved as files.
		if($this->EE->config->item('save_tmpl_files') != 'y')
		{
			$this->error_message = 'Save templates as files must be set to Yes in Global Template Preferences';
			return FALSE;
		}
		else
		{
			if(!($this->tmpl_basepath != $this->EE->config->slash_item('site_short_name') && file_exists($this->tmpl_basepath)))
			{
				$this->error_message = 'Template basepath not defined - or not found ('.$this->tmpl_basepath.')';
				return FALSE;
			}
		}
		
		
		// check that parent dir is writeable.
		if ( substr(sprintf('%o', fileperms( $this->tmpl_basepath )) , -4) < DIR_WRITE_MODE )
		{
			show_error( "Your template directory (".$this->tmpl_basepath.") needs to be writable." );
		}
		
		// check if the global_vars and snippets dirs exists, else create them now.
		// check global vars dir
		if ( !file_exists($this->gv_path) )
		{
			mkdir( $this->gv_path );
		}
		if ( !$this->mk_perms( $this->gv_path ) )
		{
			show_error( "Could not make the global variables directory writeable." );
		}
		
		// check snippets dir
		if ( !file_exists($this->sn_path) )
		{
			mkdir( $this->sn_path );
		}
		if ( !$this->mk_perms( $this->sn_path ) )
		{
			show_error( "Could not make the snippets directory writeable." );
		}
		
		return TRUE;	// everything OK
	}

	public function sync_all()
	{
		$this->last_sync_log['globals'] = array();
		$this->last_sync_log['snippets'] = array();

		if($this->verify_settings())
		{

			$global_variables = $this->get_files($this->gv_path);
			$snippets = $this->get_files($this->sn_path);
			
			// regex arrays, used to create valid snippet and global variable names.
			$search = array(
				"/\..*$/ui", //strips extension.
				"/([^a-zA-Z0-9\-\_]+)/i", // remove illegal chars
				"/(^[\.\-\_]*|[\.\-\_]*$)/i"
			);
			$replace= array(
				"", // replace extension with nothing
				"_", // replace illegal chars with an underscore
				"" // if we end up with a special char at the end or beginning of the name remove this char.
			);

			foreach($global_variables as $global_variable_filename)
			{
				$global_variable_name = preg_replace( $search , $replace , $global_variable_filename );
				
				$this->EE->db->where('variable_name', $global_variable_name);
				$this->EE->db->from('global_variables');
				
				$global_variable_data = file_get_contents($this->gv_path.$global_variable_filename);
				
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
				$snippet_name = preg_replace( $search , $replace , $snippet_filename );

				$this->EE->db->where('snippet_name', $snippet_name);
				$this->EE->db->from('snippets');
				$snippet_data = file_get_contents($this->sn_path.$snippet_filename);

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
			
			/*
				DELETE FILES WHEN USER HAS MANUALLY DELETED A SNIPPET FROM THE DB.
			*/
			// if ( isset($_REQUEST['M']) && $_REQUEST['M'] == "snippets_delete" && isset($_REQUEST['delete_confirm']) && $_REQUEST['delete_confirm'] )
			// {
			// 	// remove files from snippets and global vars that have been deleted in the DB.
			// 	
			// 	// select all available snippet_names from DB
			// 	$results = $this->EE->db->query("SELECT snippet_name FROM exp_snippets");
			// 	if ( $results->num_rows() > 0 )
			// 	{
			// 		foreach ($results->result_array() as $key => $value) {
			// 			$snippets_db[] = $value['snippet_name'];
			// 		}
			// 	} else {
			// 		$snippets_db = array();
			// 	}
			// 
			// 	// select all available global_variables from DB
			// 	$results = $this->EE->db->query("SELECT variable_name FROM exp_global_variables");
			// 	if ( $results->num_rows() > 0 )
			// 	{
			// 		foreach ($results->result_array() as $key => $value) {
			// 			$global_variables_db[] = $value['variable_name'];
			// 		}
			// 	} else {
			// 		$global_variables_db = array();
			// 	}
			// 
			// 	foreach ($snippets as $snippet_filename) {
			// 
			// 		$snippet_name = str_replace('.html', '', $snippet_filename);
			// 		$snippet_path = $this->sn_path.$snippet_filename;
			// 		$snippet_octal = substr(sprintf('%o', fileperms( $snippet_path )), -4);
			// 
			// 		if ( !in_array( $snippet_name , $snippets_db ) && $snippet_octal > FILE_READ_MODE )
			// 		{
			// 			// we have not found a matching snippet in the DB to this filename, remove the source file.
			// 			unlink( $this->sn_path.$snippet_filename );
			// 		} 
			// 	}
			// 	foreach ($global_variables as $global_variable_filename) {
			// 
			// 		$global_variable_name = str_replace('.html', '', $global_variable_filename);
			// 		$global_variable_path = $this->gv_path.$global_variable_filename;
			// 		$global_variable_octal = substr(sprintf('%o', fileperms( $global_variable_path )), -4);
			// 
			// 		if ( !in_array( $global_variable_name , $global_variables_db ) && $global_variable_octal > FILE_READ_MODE )
			// 		{
			// 			// we have not found a matching global variable in the DB to this filename, remove the source file.
			// 			unlink( $this->gv_path.$global_variable_filename );
			// 		} 
			// 	}
			// 	
			// 	// since we altered the directory content let's reset these vars.
			// 	$snippets = $this->get_files($this->sn_path);
			// 	$global_variables = $this->get_files($this->gv_path);
			// 	
			// }
			
			
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
		
		if ( substr(sprintf('%o', fileperms( $dir )), -4) != $perms )
		{
			chmod( $dir , $perms );
		}
		else {
			return FALSE;
		}
		
		return TRUE;
	}
	
}