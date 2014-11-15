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
            <option value="<?=$dir;?>"><?=$dir;?></option>
        <?php endforeach;?>
    </select>
</p>
<p><button id="download">Download</button></p>

<div id="echo-result">

</div>