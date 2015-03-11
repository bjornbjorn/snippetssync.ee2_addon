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

<h3 style="margin-top: 30px;">Sync via URL</h3>

<p>Unique URL when sync&rsquo;ing over HTTP.</p>
<p>This can be used as a post-deploy hook to sync snippets on a production server.</p>

<?php if ($sync_var == '' || $sync_var == 'CHANGEME') : ?>

	<p>Please change &lsquo;<b>snippetssync_sync_var</b>&rsquo; in <em>config/snippetssync.php</em> to generate a unique URL.</p>

<?php else : ?>

	<p>
	<?php
		$input_data = 'sync_url';
		$input_value = $sync_url;
	
		echo lang('snippetssync_sync_url', 'sync_url');
	
		echo form_input($input_data, $input_value, 'id="sync_url"' );
	?>
	</p>

<?php endif; ?>