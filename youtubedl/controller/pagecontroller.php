<?php
/**
 * ownCloud - youtubedl
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Guven Atbakan <guvenatbakan@gmail.com>
 * @copyright Guven Atbakan 2014
 */

namespace OCA\YoutubeDl\Controller;


use \OCP\IRequest;
use \OCP\AppFramework\Http\TemplateResponse;
use \OCP\AppFramework\Controller;

class PageController extends Controller {

    private $userId;

    public function __construct($appName, IRequest $request, $userId){
        parent::__construct($appName, $request);
        $this->userId = $userId;
    }


    /**
     * CAUTION: the @Stuff turn off security checks, for this page no admin is
     *          required and no CSRF check. If you don't know what CSRF is, read
     *          it up in the docs or you might create a security hole. This is
     *          basically the only required method to add this exemption, don't
     *          add it to any other method if you don't exactly know what it does
     *
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function index() {

        $params = array(
            'user' => $this->userId,
            'dirs' => $this->array_flatten($this->listdir())
        );

        //TODO: GET DEFAULT OPTIONS, SUCH AS DEFAULT DIR

        return new TemplateResponse('youtubedl', 'main', $params);  // templates/main.php
    }

    /**
     * Simply method that posts back the payload of the request
     * @NoAdminRequired
     */
    public function doDownload($url,$mp3,$dir) {
        //TODO: SET DEFAULT OPTIONS, SUCH AS DEFAULT DIR
        $output='';$filename='';
        if(empty($url)){
            $status = 'error';
            $message = '';
        }else{
            require_once __DIR__.'/../vendor/autoload.php';
            // First, we will try to get file extension.
            $process = new \Symfony\Component\Process\Process('youtube-dl '.$url.' -o "%(title)s.%(ext)s" --get-filename');
            $process->setTimeout(3600);
            $process->run();
            if (!$process->isSuccessful()) {
                $output = $process->getErrorOutput();
                $status = 'error';
                $message = 'URL Error.';
            }else{
                //If there is any problem about getting file name, we will urlize it and download it.
                $fileFullName = trim($process->getOutput());
                $fileFullName = explode('.',$fileFullName);

                $fileName = $fileFullName[count($fileFullName)-2];
                $fileNameUrlize = strtolower( preg_replace( array( '/[^-a-zA-Z0-9\s]/', '/[\s]/' ), array( '', '-' ), $fileName ) ); //TODO: Make this function better.
                $fileExtension = $fileFullName[count($fileFullName)-1];
                $fileLocation = 'data/'.$this->userId.'/files'.$dir.'/'.$fileNameUrlize.'.'.$fileExtension;

                $process = new \Symfony\Component\Process\Process('youtube-dl '.$url.' -o "'.$fileLocation.'"');
                $process->setTimeout(7200);
                $process->run();
                if (!$process->isSuccessful()) {
                    $status = 'error';
                    $message = 'Download error';
                    $output = $process->getErrorOutput();
                }else{
                    $status = 'success';
                    $message = 'File downloaded';
                    if($mp3 == "on"){

                        /*$process = new \Symfony\Component\Process\Process('ffmpeg -i "data/'.$this->userId.'/files'.$dir.'/'.$filename.'" -vn -acodec libvorbis "'.$filename.'.mp3"');*/
                        $process = new \Symfony\Component\Process\Process('avconv -i '.$fileLocation.' -vn -y '.$fileLocation.'.mp3');
                        $process->setTimeout(3600);
                        $process->run();
                        if (!$process->isSuccessful()) {
                            //throw new RuntimeException($process->getErrorOutput());
                            $status = 'error';
                            $message .= ", but couldn't convert to .mp3 and downloaded youtube file deleted.";
                            $output = $process->getErrorOutput();
                        }else{
                            $status = 'success';
                            $message .= ", and converted to .mp3";
                        }
                        /*
                         * Deleting downloaded file, because we converted it to mp3 (or couldnt convert)
                         * TODO: Remove file downloaded youtube file with OwnCloud API
                         * */
                        $process = new \Symfony\Component\Process\Process('rm -rf '.$fileLocation);$process->setTimeout(3600);$process->run();
                    }
                    //TODO: RENAME FILE WITH ORIGINAL NAME
                }

            }

        }

        return array('status'=>$status,'message'=>$message,'output'=>$output,'filename'=>$fileNameUrlize.'.'.$fileExtension,'url'=>$url);
    }

    function listdir($dir = ""){
        //TODO: MAKE SOME CACHE!!!!
        $dir = stripslashes($dir);
        $list = \OC\Files\Filesystem::getdirectorycontent($dir);
        if(sizeof($list)>0){
            $ret=[];
            foreach( $list as $i ) {
                if($i['type'] === 'dir' && $i['name'] !== '.') {
                    $ret[] = $dir.'/'.$i['name'];
                    $subs = $this->listdir($dir.'/'.$i['name']);
                    if(!empty($subs)){
                        $ret[] = $subs;
                    }
                }
            }
            return $ret;
        }
    }
    function array_flatten($array, $preserve_keys = 1, &$newArray = Array()) {
        foreach ($array as $key => $child) {
            if (is_array($child)) {
                $newArray =& $this->array_flatten($child, $preserve_keys, $newArray);
            } elseif ($preserve_keys + is_string($key) > 1) {
                $newArray[$key] = $child;
            } else {
                $newArray[] = $child;
            }
        }
        return $newArray;
    }




}