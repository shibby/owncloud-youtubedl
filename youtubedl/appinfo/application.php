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
namespace OCA\YoutubeDl\AppInfo;


use \OCP\AppFramework\App;
use \OCP\IContainer;
use \OCA\YoutubeDl\Controller\PageController;


class Application extends App {


	public function __construct (array $urlParams=array()) {
		parent::__construct('youtubedl', $urlParams);

		$container = $this->getContainer();

		/**
		 * Controllers
		 */
		$container->registerService('PageController', function(IContainer $c) {
			return new PageController(
				$c->query('AppName'), 
				$c->query('Request'),
				$c->query('UserId'),
                $c->query('Config')
			);
		});

        $container->registerService('Config', function($c) {
            return $c->query('ServerContainer')->getConfig();
        });

		/**
		 * Core
		 */
		$container->registerService('UserId', function(IContainer $c) {
			return \OCP\User::getUser();
		});

        $container->registerService('AuthorStorage', function($c) {
            return new AuthorStorage($c->query('RootStorage'));
        });

        $container->registerService('RootStorage', function($c) {
            return $c->query('ServerContainer')->getRootFolder();
        });
        $container->registerService('AboutStorage', function($c) {
            $folder = \Folder::getStorage();
            return $folder;
        });

	}


}