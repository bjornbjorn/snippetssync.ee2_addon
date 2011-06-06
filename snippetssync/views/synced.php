<?
if(!$success)
{
    ?>
        <h2>Error</h2>
    <p><?=$error_message?></p>
    <?
}


if(count($global_variables))
{
    ?>
    <h2><?=$global_variables_count?> Global Variables Synced</h2>
    <table>
<?
    foreach($global_variables as $gv_name)
    {
        ?>
            <tr><td><?=$gv_name?></td></tr>
        <?
    }
?>
    </table>
    <?
}

if(count($snippets))
{
    ?>
    <h2><?=$snippets_count?> Snippets Synced</h2>
    <p><ul>
<?
    foreach($snippets as $snippet_name)
    {
        ?>
            <li><?=$snippet_name?></li>
        <?
    }
?>
    </ul></p>
    <?
}
?>
<br/>

<p>Last synced at: <strong><?=$sync_time?></strong></p>

<p>
    <?=form_open($_form_base.AMP."method=manual_sync", '' )?>
        <?=form_submit('submit',lang('snippetssync_again'),'class="submit"')?>
    <?=form_close()?>
</p>

