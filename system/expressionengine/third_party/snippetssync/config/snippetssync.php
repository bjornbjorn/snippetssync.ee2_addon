<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * This is a flag indication whether or not we're currently in PRODUCTION,
 * if this is TRUE snippetssync will NOT sync on page load. There's a button
 * in addons-> modules -> snippetssync you can click to do a manual sync.
 */
$config['snippetssync_production_mode'] = FALSE;
$config['snippetssync_snippet_prefix'] = '';                    // for example: 'sn_'
$config['snippetssync_global_variable_prefix'] = '';            // for example: 'gv_'

// you can uncomment the above and put it in your master config if you prefer.