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
use \OCP\AppFramework\Http\JSONResponse;

use \OCA\YoutubeDl\AppInfo\Application;


class PageControllerTest extends \PHPUnit_Framework_TestCase {

	private $container;

	public function setUp () {
		$app = new Application();
		$phpunit = $this;
		$this->container = $app->getContainer();
		$this->container->registerService('Request', function($c) use ($phpunit) {
			return $phpunit->getMockBuilder('\OCP\IRequest')->getMock();
		});
		$this->container->registerParameter('UserId', 'john');
	}


	public function testIndex () {
		$result = $this->container->query('PageController')->index();

		$this->assertEquals(array('user' => 'john'), $result->getParams());
		$this->assertEquals('main', $result->getTemplateName());
		$this->assertTrue($result instanceof TemplateResponse);
	}


	public function testEcho () {
		$result = $this->container->query('PageController')->doEcho('hi');

		$this->assertEquals(array('echo' => 'hi'), $result);
	}


}