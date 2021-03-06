<?php

if (!defined('CAKEPHP_UNIT_TEST_EXECUTION')) {
	define('CAKEPHP_UNIT_TEST_EXECUTION', 1);
}

define('VALID_TEST_EMAIL', 'graham@grahamweldon.com'); # for testing normal behavior
define('GARBIGE_TEST_EMAIL', 'test@test.de'); # for testing default image behavior

App::uses('HtmlHelper', 'View/Helper');
App::uses('GravatarHelper', 'Tools.View/Helper');
App::uses('MyCakeTestCase', 'Tools.TestSuite');
App::uses('View', 'View');

/**
 * Gravatar Test Case
 *
 * 2010-05-27 ms
 */
class GravatarHelperTest extends MyCakeTestCase {

	/**
	 * setUp method
	 */
	public function setUp() {
		parent::setUp();

		$this->Gravatar = new GravatarHelper(new View(null));
		$this->Gravatar->Html = new HtmlHelper(new View(null));
	}

	/**
	 * tearDown method
	 */
	public function tearDown() {
		parent::tearDown();

		unset($this->Gravatar);
	}

	/**
	 * @access public
	 * @return void
	 * 2009-07-30 ms
	 */
	public function testDefaultImages() {

		$is = $this->Gravatar->defaultImages();
		$expectedCount = 7;

		foreach ($is as $image) {
			$this->out($image.' ');
		}
		$this->assertTrue(is_array($is) && (count($is) === $expectedCount));

	}

	/**
	 * @access public
	 * @return void
	 * 2009-07-30 ms
	 */
	public function testImages() {
		$is = $this->Gravatar->image(GARBIGE_TEST_EMAIL);
		$this->out($is);
		$this->assertTrue(!empty($is));

		$is = $this->Gravatar->image(Configure::read('Config.admin_email'));
		$this->out($is);
		$this->assertTrue(!empty($is));

		$is = $this->Gravatar->image(VALID_TEST_EMAIL);
		$this->out($is);
		$this->assertTrue(!empty($is));

		$is = $this->Gravatar->image(VALID_TEST_EMAIL, array('size'=>'200'));
		$this->out($is);
		$this->assertTrue(!empty($is));

		$is = $this->Gravatar->image(VALID_TEST_EMAIL, array('size'=>'20'));
		$this->out($is);
		$this->assertTrue(!empty($is));

		$is = $this->Gravatar->image(VALID_TEST_EMAIL, array('rating'=>'X')); # note the capit. x
		$this->out($is);
		$this->assertTrue(!empty($is));

		$is = $this->Gravatar->image(VALID_TEST_EMAIL, array('ext'=>true));
		$this->out($is);
		$this->assertTrue(!empty($is));

		$is = $this->Gravatar->image(VALID_TEST_EMAIL, array('default'=>'none'));
		$this->out($is);
		$this->assertTrue(!empty($is));

		$is = $this->Gravatar->image(GARBIGE_TEST_EMAIL, array('default'=>'none'));
		$this->out($is);
		$this->assertTrue(!empty($is));

		$is = $this->Gravatar->image(GARBIGE_TEST_EMAIL, array('default'=>'http://2.gravatar.com/avatar/8379aabc84ecee06f48d8ca48e09eef4?d=identicon'));
		$this->out($is);
		$this->assertTrue(!empty($is));
	}

/** BASE TEST CASES **/

/**
 * testBaseUrlGeneration
 *
 * @return void
 * @access public
 */
	public function testBaseUrlGeneration() {
		$expected = 'http://www.gravatar.com/avatar/' . md5('example@gravatar.com');
		$result = $this->Gravatar->imageUrl('example@gravatar.com', array('ext' => false, 'default' => 'wavatar'));
		list($url, $params) = explode('?', $result);
		$this->assertEquals($expected, $url);
	}

/**
 * testExtensions
 *
 * @return void
 * @access public
 */
	public function testExtensions() {
		$result = $this->Gravatar->imageUrl('example@gravatar.com', array('ext' => true, 'default' => 'wavatar'));
		$this->assertRegExp('/\.jpg(?:$|\?)/', $result);
	}

/**
 * testRating
 *
 * @return void
 * @access public
 */
	public function testRating() {
		$result = $this->Gravatar->imageUrl('example@gravatar.com', array('ext' => true, 'default' => 'wavatar'));
		$this->assertRegExp('/\.jpg(?:$|\?)/', $result);
	}

/**
 * testAlternateDefaultIcon
 *
 * @return void
 * @access public
 */
	public function testAlternateDefaultIcon() {
		$result = $this->Gravatar->imageUrl('example@gravatar.com', array('ext' => false, 'default' => 'wavatar'));
		list($url, $params) = explode('?', $result);
		$this->assertRegExp('/default=wavatar/', $params);
	}

/**
 * testAlternateDefaultIconCorrection
 *
 * @return void
 * @access public
 */
	public function testAlternateDefaultIconCorrection() {
		$result = $this->Gravatar->imageUrl('example@gravatar.com', array('ext' => false, 'default' => '12345'));
		$this->assertRegExp('/[^\?]+/', $result);
	}

/**
 * testSize
 *
 * @return void
 * @access public
 */
	public function testSize() {
		$result = $this->Gravatar->imageUrl('example@gravatar.com', array('size' => '120'));
		list($url, $params) = explode('?', $result);
		$this->assertRegExp('/size=120/', $params);
	}

/**
 * testImageTag
 *
 * @return void
 * @access public
 */
	public function testImageTag() {
		$expected = '<img src="http://www.gravatar.com/avatar/' . md5('example@gravatar.com') . '" alt="" />';
		$result = $this->Gravatar->image('example@gravatar.com', array('ext' => false));
		$this->assertEquals($expected, $result);

		$expected = '<img src="http://www.gravatar.com/avatar/' . md5('example@gravatar.com') . '" alt="Gravatar" />';
		$result = $this->Gravatar->image('example@gravatar.com', array('ext' => false, 'alt' => 'Gravatar'));
		$this->assertEquals($expected, $result);
	}

/**
 * testDefaulting
 *
 * @return void
 * @access public
 */
	public function testDefaulting() {
		$result = $this->Gravatar->imageUrl('example@gravatar.com', array('default' => 'wavatar', 'size' => 'default'));
		list($url, $params) = explode('?', $result);
		$this->assertEquals($params, 'default=wavatar');
	}

/**
 * testNonSecureUrl
 *
 * @return void
 * @access public
 */
	public function testNonSecureUrl() {
		$_SERVER['HTTPS'] = false;

		$expected = 'http://www.gravatar.com/avatar/' . md5('example@gravatar.com');
		$result = $this->Gravatar->imageUrl('example@gravatar.com', array('ext' => false));
		$this->assertEquals($expected, $result);

		$expected = 'http://www.gravatar.com/avatar/' . md5('example@gravatar.com');
		$result = $this->Gravatar->imageUrl('example@gravatar.com', array('ext' => false, 'secure' => false));
		$this->assertEquals($expected, $result);

		$_SERVER['HTTPS'] = true;
		$expected = 'http://www.gravatar.com/avatar/' . md5('example@gravatar.com');
		$result = $this->Gravatar->imageUrl('example@gravatar.com', array('ext' => false, 'secure' => false));
		$this->assertEquals($expected, $result);
	}

/**
 * testSecureUrl
 *
 * @return void
 * @access public
 */
	public function testSecureUrl() {
		$expected = 'https://secure.gravatar.com/avatar/' . md5('example@gravatar.com');
		$result = $this->Gravatar->imageUrl('example@gravatar.com', array('ext' => false, 'secure' => true));
		$this->assertEquals($expected, $result);

		$_SERVER['HTTPS'] = true;

		$this->Gravatar = new GravatarHelper(new View(null));
		$this->Gravatar->Html = new HtmlHelper(new View(null));

		$expected = 'https://secure.gravatar.com/avatar/' . md5('example@gravatar.com');
		$result = $this->Gravatar->imageUrl('example@gravatar.com', array('ext' => false));
		$this->assertEquals($expected, $result);

		$expected = 'https://secure.gravatar.com/avatar/' . md5('example@gravatar.com');
		$result = $this->Gravatar->imageUrl('example@gravatar.com', array('ext' => false, 'secure' => true));
		$this->assertEquals($expected, $result);
	}

}
