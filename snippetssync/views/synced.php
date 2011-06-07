<?php
if(!$success)
{
    ?>
        <h2>Error</h2>
    <p><?php echo $error_message; ?></p>
    <?php
}


if(count($global_variables))
{
    ?>
    <h2><?php echo $global_variables_count; ?> Global Variables Synced</h2>
    <table>

<?php
    foreach($global_variables as $gv_name)
    {
        ?>
            <tr><td><?php echo $gv_name; ?></td></tr>
        <?php
    }
?>
    </table>
    <?php
}

if(count($snippets))
{
    ?>
    <h2><?php echo $snippets_count; ?> Snippets Synced</h2>
    <p><ul>
<?php
    foreach($snippets as $snippet_name)
    {
        ?>
            <li><?php echo $snippet_name; ?></li>
        <?php
    }
?>
    </ul></p>
    <?php
}
?>
<br/>

<p>Last synced at: <strong><?php echo $sync_time?></strong></p>

<p>
    <?php echo form_open($_form_base.AMP."method=manual_sync", '' )?>
        <?php echo form_submit('submit',lang('snippetssync_again'),'class="submit"')?>
    <?php echo form_close()?>
</p>

