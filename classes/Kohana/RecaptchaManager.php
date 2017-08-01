<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Kohana wrapper for Googles reCAPTCHA library
 *
 * @author     Butch Stine <bullet@test.com>
 * @copyright  Copyright (c) 2017 Butch Stine
 * @license    BSD 3-Clause License, see LICENSE file
 */
class Kohana_RecaptchaManager {

	/**
	 * Public key
	 * @var string
	 */
	protected $_public_key;

	/**
	 * Private key
	 * @var string
	 */
	protected $_private_key;

	/**
	 * Error code returned when checking the answer
	 * @var string
	 */
	protected $_error;

	public static $instance;
	public static function instance(){
		if(!self::$instance instanceof RecaptchaManager)
			self::$instance = new RecaptchaManager();
		return self::$instance;
	}

	/**
	 * Load the reCAPTCHA PHP library and configure the keys from the config
	 * file or the provided array argument.
	 *
	 * Kohana_RecaptchaManager constructor.
	 */
	public function __construct()
	{
		require_once Kohana::find_file('vendor', 'autoload');
		$config = Kohana::$config->load('recaptcha');
		$this->_public_key = $config['public_key'];
		$this->_private_key = $config['private_key'];
	}

	/**
	 * Generate the HTML to display to the client
	 * @param array $options
	 * @return string
	 * @throws View_Exception
	 */
	public function get_html($options = array())
	{
		$html = View::factory('recaptcha')->set(array(
			'public_key' => $this->_public_key,
			'options' => $options,
		))->render();
		return $html;
	}

	/**
	 * Returns bool true if successful, bool false if not.
	 * @return  bool
	 */
	public function checkCaptcha()
	{
		$client = new ReCaptcha\ReCaptcha($this->_private_key);
		$response = Arr::get($_POST, 'g-recaptcha-response');
		$remote_ip = $_SERVER['REMOTE_ADDR'];
		$result = $client->verify($response, $remote_ip);
		return $result->isSuccess();
	}

	/**
	 * @return string
	 */
	public function __toString(){
		return $this->get_html();
	}

	/**
	 * Check recaptcha result
	 * @return bool
	 */
	public static function check($value, $validation, $field){
		if(!RecaptchaManager::instance()->checkCaptcha()){
			$validation->error($field, 'recaptcha_mismatch');
		}
		return true;
	}
}
