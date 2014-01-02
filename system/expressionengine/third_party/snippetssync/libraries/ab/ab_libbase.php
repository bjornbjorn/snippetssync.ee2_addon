<?php
if(!class_exists('Ab_Common')) { require_once 'ab_common.php'; }

/**
 * AddonBakery - Base for EE libraries; all libraries extend this
 *
 * All your base are belongs to us
 */

class Ab_LibBase extends Ab_Common
{

    const LOGGED_IN_USER = 'logged_in_user';
    const ANYONE = 'anyone';
    const ANYONE_NO_XID_CHECK = 'anyone_no_xid';    // anyone can access, no XID check

    /**
     * All action calls get routed through here
     *
     * @param  $name
     * @param  $arguments
     * @return void
     */
    public function __call($name, $arguments)
    {
        $internal_method_name = 'act_' . $name;
        if(method_exists($this, $internal_method_name))
        {
            if(!isset($this->method_access[$internal_method_name]))
            {
                $this->EE->output->show_user_error('submission', array('no_access'));     // not defined in access array, throw no access error
            }

            $role_needed = $this->method_access[$internal_method_name];
            $member_id = $this->EE->session->userdata('member_id');
            $access_allowed = FALSE;
            switch($role_needed)
            {
                case Ab_LibBase::LOGGED_IN_USER:
                    $access_allowed = ($member_id > 0);
                    break;
                case Ab_LibBase::ANYONE_NO_XID_CHECK:
                case Ab_LibBase::ANYONE:
                    $access_allowed = TRUE;
                    break;
            }

            if(!$access_allowed)
            {
                $this->EE->output->show_user_error('submission', array('no_access'));
            }

            // @todo looks like EE is already checking the XID now - but then ANYONE_NO_XID_CHECK won't work anymore. Fix.

            // XID check - we use EE's new security lib for this now
            /*if($role_needed != Ab_LibBase::ANYONE_NO_XID_CHECK && $this->EE->security->check_xid($this->EE->input->post('XID')) == FALSE)
            {
                $this->EE->output->show_user_error('submission', array(lang('timed_out')));
            }*/

            // if we get this far we're allowed to call the function at least
            $this->$internal_method_name($arguments);
        }
        else
        {
            show_error('No such method', 'That action does not seem to exist');
        }
    }


}