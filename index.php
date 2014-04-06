<?php
function url_title($str, $separator = 'dash', $lowercase = FALSE)
{
    if ($separator == 'dash')
    {
        $search = '_';
        $replace = '-';
    }
    else
    {
        $search = '-';
        $replace = '_';
    }

    $trans = array(
        '&\#\d+?;' => '',
        '&\S+?;' => '',
        '\s+' => $replace,
        '\.' => $replace,
        '[^a-z0-9\-_]' => '',
        $replace . '+' => $replace,
        $replace . '$' => $replace,
        '^' . $replace => $replace,
        '\.+$' => ''
    );

    $search_tr = array('ı', 'İ', 'Ğ', 'ğ', 'Ü', 'ü', 'Ş', 'ş', 'Ö', 'ö', 'Ç', 'ç');
    $replace_tr = array('i', 'I', 'G', 'g', 'U', 'u', 'S', 's', 'O', 'o', 'C', 'c');
    $str = str_replace($search_tr, $replace_tr, $str);

    $str = strip_tags($str);

    foreach ($trans as $key => $val)
    {
        $str = preg_replace("#" . $key . "#i", $val, $str);
    }

    if ($lowercase === TRUE)
    {
        $str = strtolower($str);
    }

    return strtolower(trim(stripslashes($str)));
}

// Look up other security checks in the docs!
\OCP\User::checkLoggedIn();
\OCP\App::checkAppEnabled('youtubedl');
$user = \OCP\User::getUser();

$tpl = new OCP\Template("youtubedl", "main", "user");

//lets see if link posted from form area?
if(isset($_POST['youtube'])){
    $url = $_POST['youtube'];

    //lets control that we will download video and convert to ogg or just download video?

    //i will not explain anything else, i hate writing documentation and i'm suck.
    //do what do you want to do. that addon works for me :)
    if($_POST['ogg'] == "on"){
        $sourcecode = file_get_contents($url);
        preg_match('@<title>(.*?)</title>@si',$sourcecode,$title);
        $name = str_replace(" - youtube","",strtolower($title[1]));
        $name = url_title($name);
        exec('youtube-dl '.$url.' -o "/var/www/owncloud/data/'.$user.'/files/music/'.$name.'.%(ext)s" --get-filename ',$o);
        exec('youtube-dl '.$url.' -o "/var/www/owncloud/data/'.$user.'/files/music/'.$name.'.%(ext)s" ');
        exec('ffmpeg -i '.$o[0].' -vn -acodec libvorbis "'.$o[0].'.ogg"');
        exec("rm ".$o[0]);
    }else{
        $sourcecode = file_get_contents($url);
        preg_match('@<title>(.*?)</title>@si',$sourcecode,$title);
        $name = str_replace(" - youtube","",strtolower($title[1]));
        //exec('youtube-dl '.$url.' -o "/var/www/owncloud/data/shibby/files/videos/'.$name.'.%(ext)s" --get-filename ',$o);
        exec('youtube-dl '.$url.' -o "/var/www/owncloud/data/'.$user.'/files/videos/'.$name.'.%(ext)s" ');
    }

    $tpl->assign('msg','ok');
}


//$tpl->assign('msg',$folder);

$tpl->printPage();
