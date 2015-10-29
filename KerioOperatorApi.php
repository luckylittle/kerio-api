<?php
/**
 * This file is part of the kerio-api-php.
 *
 * Copyright (c) Kerio Technologies s.r.o.
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code
 * or visit Developer Zone. (http://www.kerio.com/developers)
 *
 * Do not modify this source code.
 * Any changes may be overwritten by a new version.
 */

require_once(dirname(__FILE__) . '/class/KerioApi.php');

/**
 * Administration API for Kerio Operator.
 * STATUS: In progress, might change in the future
 *
 * This class implements product-specific methods and properties and currently is under development.
 * Class is not intended for stable use yet.
 * Functionality might not be fully verified, documented, or even supported.
 *
 * Please note that changes can be made without further notice.
 *
 * Example:
 * <code>
 * <?php
 * require_once(dirname(__FILE__) . '/src/KerioOperatorApi.php');
 *
 * $api = new KerioOperatorApi('Sample application', 'Company Ltd.', '1.0');
 *
 * try {
 *     $api->login('operator.company.tld', 'admin', 'SecretPassword');
 *     $api->sendRequest('...');
 *     $api->logout();
 * } catch (KerioApiException $error) {
 *     print $error->getMessage();
 * }
 * ?>
 * </code>
 *
 * @copyright	Copyright &copy; 2012-2012 Kerio Technologies s.r.o.
 * @license		http://www.kerio.com/developers/license/sdk-agreement
 * @version		1.3.0.62
 */
class KerioOperatorApi extends KerioApi {

	/**
	 * Defines default product-specific JSON-RPC settings
	 * @var array
	 */
	protected $jsonRpc = array(
		'version'	=> '2.0',
		'port'		=> 4021,
		'api'		=> '/admin/api/jsonrpc/'
	);

	/**
	 * Class constructor.
	 *
	 * @param	string	Application name
	 * @param	string	Application vendor
	 * @param	string	Application version
	 * @return	void
	 * @throws	KerioApiException
	 */
	public function __construct($name, $vendor, $version) {
		parent::__construct($name, $vendor, $version);
	}

	/**
	 * Set component Web Administration.
	 * 
	 * @param	void
	 * @return	void
	 */
	public function setComponentAdmin() {
		$this->setJsonRpc('2.0', 4021, '/admin/api/jsonrpc/');
	}

	/**
	 * Set component Client aka MyPhone.
	 * 
	 * @param	void
	 * @return	void
	 */
	public function setComponentClient() {
		$this->setJsonRpc('2.0', 443, '/myphone/api/jsonrpc/');
	}

	/**
	 * Set component MyPhone.
	 * 
	 * @param	void
	 * @return	void
	 * @deprecated
	 */
	public function setComponentMyphone() {
		trigger_error("Deprecated function setComponentMyphone(), use setComponentClient() instead", E_USER_NOTICE);
		$this->setComponentClient();
	}

	/**
	 * Get constants defined by product.
	 *
	 * @param	void
	 * @return	array	Array of constants
	 */
	public function getConstants() {
		$response = $this->sendRequest('Server.getConstantList');
		return $response['constantList'];
	}
}
