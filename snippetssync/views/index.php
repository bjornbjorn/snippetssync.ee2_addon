<p>
    <?
    if($production_mode)
    {?>
            SnippetsSync is currently in production mode. Snippets are only synced when you click the button below.
    <?}
    else
    {?>
            SnippetsSync is in development mode. Snippets are always synced.
    <?}
    ?>
    <em><small>To switch between dev/production mode, edit config/snippetssync.php</small></em>
</p>
<br/>
<p>
    <?=form_open($_form_base.AMP."method=manual_sync", '' )?>
        <?=form_submit('submit',lang('snippetssync_manually'),'class="submit"')?>
    <?=form_close()?>
</p>