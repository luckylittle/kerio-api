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
 * Administration API for Kerio Workspace.
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
 * require_once(dirname(__FILE__) . '/src/KerioWorkspaceApi.php');
 *
 * $api = new KerioWorkspaceApi('Sample application', 'Company Ltd.', '1.0');
 *
 * try {
 *     $api->login('workspace.company.tld', 'admin', 'SecretPassword');
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
class KerioWorkspaceApi extends KerioApi {

	/**
	 * Defines default product-specific JSON-RPC settings
	 * @var array
	 */
	protected $jsonRpc = array(
		'version'	=> '2.0',
		'port'		=> 4060,
		'api'		=> '/admin/api/jsonrpc/'
	);
	
	private $file = array();

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
		$this->setJsonRpc('2.0', 4060, '/admin/api/jsonrpc/');
	}

	/**
	 * Set component Workspace Client.
	 * 
	 * @param	void
	 * @return	void
	 */
	public function setComponentClient() {
		$this->setJsonRpc('2.0', 443, '/server/data');
	}

	/**
	 * Get constants defined by product.
	 *
	 * @param	void
	 * @return	array	Array of constants
	 */
	public function getConstants() {
		$response = $this->sendRequest('Server.getProductInfo');
		return $response['constants'];
	}

	/**
	 * Get headers for PUT request.
	 *
	 * @param	string	Request body
	 * @return	string	Request body
	 */
	protected function getHttpPutRequest($data) {
		$this->headers['POST']			= sprintf('%s?method=PutFile&filename=%s&parentId=%d&lenght=%d HTTP/1.1', $this->jsonRpc['api'], rawurlencode($this->file['filename']), $this->file['parentId'], $this->file['lenght']);
		$this->headers['Accept:']		= '*/*';
		$this->headers['Content-Type:']	= sprintf('application/k-upload');
	
		return $data;
	}
	
	/**
	 * Put a file to server.
	 *
	 * @param	string	Absolute path to file
	 * @param	integer	Reference ID where uploaded file belongs to
	 * @return	array	Result
	 * @throws	KerioApiException
	 */
	public function uploadFile($filename, $id = null) {
		$data = @file_get_contents($filename);
		
		$this->file['filename'] = basename($filename);
		$this->file['parentId'] = $id;
		$this->file['lenght']	= strlen($data);
	
		if ($data) {
			$this->debug(sprintf('Uploading file %s to item %d', $filename, $id));
			$json_response = $this->send('PUT', $data);
		}
		else {
			throw new KerioApiException(sprintf('Unable to open file %s', $filename));
		}
	
		$response = json_decode($json_response, TRUE);
		return $response['result'];
	}
}
