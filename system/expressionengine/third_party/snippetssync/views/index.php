<p>
    <?php
    if($production_mode)
    {?>
            SnippetsSync is currently in production mode. Snippets are only synced when you click the button below.
    <?php
    }
    else
    {?>
            SnippetsSync is in development mode. Snippets are always synced.
    <?php
    }
    ?>
    <em><small>To switch between dev/production mode, edit config/snippetssync.php</small></em>
</p>
<br/>
<p>
    <?php echo form_open($_form_base.AMP."method=manual_sync", '' )?>
        <?php echo form_submit('submit',lang('snippetssync_manually'),'class="submit"')?>
    <?php echo form_close()?>
</p>