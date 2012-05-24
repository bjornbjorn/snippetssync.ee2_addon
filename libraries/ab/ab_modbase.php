<?php
require_once 'ab_common'.EXT;

/**
 * All your base are belongs to us
 *
 * AddonBakery - Base for mod files; all mod.* files extend this
 */

class Ab_ModBase extends Ab_Common
{
    public $return_data;

    /**
     * Helper function for getting a tag parameter (or a default value)
     * 
     * @param string $key the name of the parameter
     * @param mixed $default_value the default value if no value is given
     * @param string $backup_key if val not found in key try backup_key
     */
    protected function get_param($key, $default_value = '', $backup_key = '')
    {
        $val = $this->EE->TMPL->fetch_param($key);
        if(!$val)
        {
            $val = $this->EE->TMPL->fetch_param($backup_key);
        }

        if(!$val) {
            return $default_value;
        }
        return $val;
    }

    /**
     * Will parse a boolean if {if statement}
     *
     * @param $cond_name the conditional name, e.g. in {if has_completed} this would be 'has_completed'
     * @param $cond_value boolean value (TRUE/FALSE)
     * @param $tagdata the tagdata to work with
     * @returns new tagdata with if parsed ready for the EE Template Engine
     */
    function parse_if($cond_name, $cond_value, $tagdata)
    {
        if($cond_value)
        {
            $cond_value = 'TRUE';
        }
        else
        {
            $cond_value = 'FALSE';
        }
        return preg_replace("/\{if\s+".$cond_name."\}/si", "{if $cond_value}", $tagdata);
    }

}