<div id="app-settings">
	<div id="app-settings-header">
		<button class="settings-button">Settings</button>
	</div>
	<div id="app-settings-content" style="display:block;">
        <ul>
            <li><a href="javascript:;" id="updateLink">Update Youtube-DL</a></li>
        </ul>
        <ul>
            <li><a href="?action=refreshCache">Refresh Folder List (Delete Cache)</a></li>
        </ul>
        <?php if($_['converter'] === "ffmpeg"):?>
        <ul>
            <li><a href="?action=changeConverter&converter=avconv">Use avconv instead of ffmpeg</a></li>
        </ul>
        <?php elseif($_['converter'] === "avconv"):?>
        <ul>
            <li><a href="?action=changeConverter&converter=ffmpeg">Use ffmpeg instead of avconv</a></li>
        </ul>
        <?php endif;?> 
        
	</div>
</div>