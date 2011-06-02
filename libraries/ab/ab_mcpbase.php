<?php
require_once 'ab_common'.EXT;
/**
 * AddonBakery base for MCP file.
 *
 * All your base are belongs to us
 */

class Ab_McpBase extends Ab_Common {

    const FLASHDATA_MESSAGE_SUCCESS = 'message_success';
    const FLASHDATA_MESSAGE_FAILURE = 'message_failure';

    public function __construct( $switch = TRUE )
    {
        parent::__construct();
    }

    /**
     * Display a message to the user (flash)
     *
     * @param  $url url to redirect to
     * @param  $message the message
     * @param string $message_type success/failure?
     * @return void
     */
    protected function display_message($url, $message, $message_type=Ab_McpBase::FLASHDATA_MESSAGE_SUCCESS)
    {
        $this->EE->session->set_flashdata($message_type, $message);
        $this->EE->functions->redirect($url);
    }
}