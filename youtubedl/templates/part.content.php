<p>Hello <?php p($_['user']) ?></p>
<p></p>

<p>
    <label for="url">
        Youtube Url
    </label>
    <input type="text" id="url">
</p>
<p>
    <label for="mp3">
        Convert To .mp3
    </label>
    <input type="checkbox" value="on" id="mp3">
</p>
<p>
    <label for="dir">
        Where to save?
    </label>
    <select id="dir">
        <?php foreach($_['dirs'] as $dir):?>
            <option value="<?=$dir;?>" <?=($dir == $_['lastDir'])?'selected="selected"':'';?>><?=$dir;?></option>
        <?php endforeach;?>
    </select>
</p>
<p><button id="download">Download</button></p>

<div id="echo-result"></div>

<div id="echo-debug-button" style="display: none;">
    <p>
        <a href="javascript:;" id="showDebug" style="text-decoration: underline;">Show Debug</a>
    </p>
</div>
<div id="echo-debug-content" style="display: none;"></div>

<p>Note from developer of this addon:
Hello everyone, i like this plugin as you :) But i don't have any time to develop this plugin because of my work and other projects :(<br>
    I'm sorry about that. Thanks for your interest.
</p>