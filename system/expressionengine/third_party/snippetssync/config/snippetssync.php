<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * This is a flag indication whether or not we're currently in PRODUCTION,
 * if this is TRUE snippetssync will NOT sync on page load. There's a button
 * in addons-> modules -> snippetssync you can click to do a manual sync.
 */
$config['snippetssync_production_mode'] = FALSE;
$config['snippetssync_snippet_prefix'] = '';            // for example: 'sn_'
$config['snippetssync_global_variable_prefix'] = '';    // for example: 'gv_'
$config['snippetssync_sync_var'] = 'CHANGEME';	    	// any random string to pass through on a URL sync

// NOTE: if you set these variables in your master config those will be used instead of these :-)