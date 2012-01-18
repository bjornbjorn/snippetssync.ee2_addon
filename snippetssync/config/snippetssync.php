<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');


/**
 * This is a flag indication whether or not we're currently in PRODUCTION,
 * if this is TRUE snippetssync will NOT sync on page load. There's a button
 * in adodns-> modules -> snippetssync you can click to do a manual sync.
 */
$config['snippetssync_production_mode'] = FALSE;

/**
 * Disable this if you do not want the corresponding file to be deleted from the
 * filesystem when you delete a snippet/global variable from the CP.
 */
$config['snippetssync_enable_auto_delete'] = TRUE;