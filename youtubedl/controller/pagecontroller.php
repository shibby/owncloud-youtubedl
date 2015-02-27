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

use \OC\Files\Filesystem;
use OC\Cache\UserCache;

class PageController extends Controller
{

    private $userId;

    public function __construct($appName, IRequest $request, $userId)
    {
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
     * @var string $action
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function index($action)
    {
        $cache = new UserCache();
        if ($action == "refreshCache") {
            $cache->remove('youtubedl_dirs');
        }

        $dirs = (array)json_decode($cache->get('youtubedl_dirs'));
        if (!$dirs) {
            $dirs = $this->array_flatten($this->listdir());
            $cache->set('youtubedl_dirs', json_encode($dirs));
        }

        $params = array(
            'user' => $this->userId,
            'dirs' => $dirs,
            'lastDir' => \OCP\Config::getUserValue($this->userId, $this->appName, 'lastDir', '')
        );

        return new TemplateResponse('youtubedl', 'main', $params);  // templates/main.php
    }

    /**
     * Simply method that posts back the payload of the request
     * @NoAdminRequired
     */
    public function doDownload($url, $mp3, $dir)
    {
        \OCP\Config::setUserValue($this->userId, $this->appName, 'lastDir', $dir);
        $output = array();
        $filename = '';
        if (empty($url)) {
            $status = 'error';
            $message = '';
        } else {
            require_once __DIR__ . '/../vendor/autoload.php';
            // First, we will try to get file extension.
            $command = 'youtube-dl ' . $url . ' -o "%(title)s.%(ext)s" --get-filename';
            $process = new \Symfony\Component\Process\Process($command);
            $process->setTimeout(3600);
            $process->run();
            $output[] = '<strong>Run Command:</strong> ';
            $output[] = $command;
            if (!$process->isSuccessful()) {
                $output[] = $process->getErrorOutput();
                $status = 'error';
                $message = 'URL Error.';
            } else {
                //If there is any problem about getting file name, we will urlize it and download it.
                $fileFullName = trim($process->getOutput());
                $path_parts = pathinfo($fileFullName);

                $fileName = $path_parts['filename'];
                $fileNameUrlize = preg_replace(array('/[^ a-zA-Z0-9\.-_\s]/', '/[\s]/'), array('', '-'), $path_parts['filename']); //TODO: Make this function better.
                $fileExtension = $path_parts['extension'];
                $fileLocation = \OCP\config::getSystemValue('datadirectory') . '/' . $this->userId . '/files' . $dir . '/' . $fileNameUrlize . '.' . $fileExtension;

                $command = 'youtube-dl ' . $url . ' -o "' . $fileLocation . '"';
                $process = new \Symfony\Component\Process\Process($command);
                $process->setTimeout(7200);
                $process->run();
                $output[] = '<strong>Run Command:</strong> ';
                $output[] = $command;
                if (!$process->isSuccessful()) {
                    $status = 'error';
                    $message = 'Download error';
                    $output[] = $process->getErrorOutput();
                } else {
                    $status = 'success';
                    $message = 'File downloaded';
                    if ($mp3 == "on") {
                        /* There was a bug in the avconv console command with blank character fileLocations
						* You have to escape them
						*/
						$fileLocation = str_replace(" ","\ ",$fileLocation);
                        $command = 'avconv -i ' . $fileLocation . ' -vn -y ' . $fileLocation . '.mp3';
                        $process = new \Symfony\Component\Process\Process($command);
                        $process->setTimeout(3600);
                        $process->run();
                        $output[] = '<strong>Run Command:</strong> ';
                        $output[] = $command;
                        if (!$process->isSuccessful()) {
                            //throw new RuntimeException($process->getErrorOutput());
                            $status = 'error';
                            $message .= ", but couldn't convert to .mp3 and downloaded youtube file deleted.";
                            $output[] = $process->getErrorOutput();
                        } else {
                            $status = 'success';
                            $message .= ", and converted to .mp3";
                        }
                        /*
                         * Deleting downloaded file, because we converted it to mp3 (or couldnt convert)
                         * TODO: Remove file downloaded youtube file with OwnCloud API
                         * */
                        $command = 'rm -rf ' . $fileLocation;
                        $process = new \Symfony\Component\Process\Process($command);
                        $process->setTimeout(3600);
                        $process->run();
                        $output[] = '<strong>Run Command:</strong> ';
                        $output[] = $command;

                        //TODO: Rename file with OC API
                        rename($fileLocation . '.mp3', \OCP\config::getSystemValue('datadirectory') . '/' . $this->userId . '/files' . $dir . '/' . $fileName . '.mp3');
                    }
                    //TODO: RENAME FILE WITH ORIGINAL NAME
                }

            }

        }

        return array('status' => $status, 'message' => $message, 'output' => $output, 'filename' => $fileNameUrlize . '.' . $fileExtension, 'url' => $url);
    }

    /**
     * Youtube dl update function
     * @NoAdminRequired
     */
    public function doUpdateyoutubedl()
    {
        require_once __DIR__ . '/../vendor/autoload.php';

        /* First getting current version of youtube-dl */
        $command = 'youtube-dl --version';
        $process = new \Symfony\Component\Process\Process($command);
        $process->setTimeout(3600);
        $process->run();
        $output[] = '<strong>Run Command:</strong> ';
        $output[] = $command;
        if (!$process->isSuccessful()) {
            $output[] = $process->getErrorOutput();
            $status = 'error';
            $message = 'Couldn\'t fetch version.';
        } else {
            $output[] = '<strong>Old Version:</strong> '.$process->getOutput();

            $command = 'youtube-dl -U';
            $process = new \Symfony\Component\Process\Process($command);
            $process->setTimeout(3600);
            $process->run();
            $output[] = '<strong>Run Command:</strong> ';
            $output[] = $command;
            if (!$process->isSuccessful()) {
                $output[] = $process->getErrorOutput();
                $status = 'error';
                $message = 'Update Error.';
            } else {
                $updateOutput = $process->getOutput();
                if(substr_count($updateOutput,'no write permissions') > 0){
                    $status = 'error';
                    $message = 'Youtube-dl couldnt update because of permission error. Connect with SSH and use "youtube-dl -U" command. ';
                }else{
                    $status = 'success';
                    $message = 'Youtube-dl update function applied. See output.';
                }
                $output[] = $updateOutput;

                /* Getting new version of youtube-dl */
                $command = 'youtube-dl --version';
                $process = new \Symfony\Component\Process\Process($command);
                $process->setTimeout(3600);
                $process->run();
                $output[] = '<strong>Run Command:</strong> ';
                $output[] = $command;
                $output[] = '<strong>New Version:</strong> '.$process->getOutput();
            }
        }

        return array('status' => $status, 'message' => $message, 'output' => $output);
    }

    function listdir($dir = "")
    {
        $ret = array();
        $dir = stripslashes($dir);
        $list = Filesystem::getdirectorycontent($dir);
        if (sizeof($list) > 0) {
            foreach ($list as $i) {
                if ($i['type'] === 'dir' && $i['name'] !== '.') {
                    $ret[] = $dir . '/' . $i['name'];
                    $subs = $this->listdir($dir . '/' . $i['name']);
                    if (!empty($subs)) {
                        $ret[] = $subs;
                    }
                }
            }
        }
        return $ret;
    }

    function array_flatten($array, $preserve_keys = 1, &$newArray = Array())
    {
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
