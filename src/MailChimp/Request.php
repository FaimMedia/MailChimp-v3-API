<?php

namespace FaimMedia\MailChimp;

use FaimMedia\MailChimp\Exception\RequestException;

class Request {

	const API_URL_SUFFIX = '.api.mailchimp.com/3.0/';

	private $dataCenter;
	private $apiKey;

	private $fullApiKey;

	public function __construct($apiKey) {
		if(!preg_match('/[a-f0-9]{32}\-[a-z]{1,2}[0-9]{1,2}/i', $apiKey)) {
			throw new MailChimp\Exception\MailChimpException('The provided API key format is invalid');
		}

		$minusPos = strrpos($apiKey, '-');

		$this->apiKey = substr($apiKey, 0, $minusPos);
		$this->dataCenter = substr($apiKey, $minusPos+1);

		$this->fullApiKey = $apiKey;
	}

	public function getDataCenter() {
		return $this->dataCenter;
	}

	private function getApiKey() {
		return $this->apiKey;
	}

	private function getFullApiKey() {
		return $this->fullApiKey;
	}

	public function request($uri, $type = 'GET', $data = []) {

		$options = [
			CURLOPT_CUSTOMREQUEST  => strtoupper($type),
			CURLOPT_HTTPAUTH       => CURLAUTH_BASIC,
			CURLOPT_USERPWD        => 'mailchimp-api:'.$this->getFullApiKey(),
			CURLOPT_HEADER         => true,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_USERAGENT      => 'FaimMedia/MailChimp-v3-PHP-API',
			//CURLOPT_FAILONERROR    => true,
			CURLOPT_HTTPHEADER     => [
				'Content-Type: application/json; charset=UTF-8',
			],
		];

		if($type != 'GET' && !empty($data)) {
			$json = json_encode($data);

			$options += [
				CURLOPT_POSTFIELDS => $json,
			];
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->createUrl($uri));
		curl_setopt_array($ch, $options);

		$response = curl_exec($ch);

		$info = curl_getinfo($ch);
		$error = curl_error($ch);

		$httpCode = (int)$info['http_code'];

		$isError = ((int)substr((string)$httpCode, 0, 2) !== 20);

		@list($header, $body) = explode("\r\n\r\n", $response, 2);

		$json = json_decode($body, true);

		if(json_last_error() !== JSON_ERROR_NONE) {
			if($error) {
				throw new RequestException('Invalid HTTP status code: '.$httpCode);
			}

			if(!$isError) {
				return true;
			}

			throw new RequestException('The response did not return a valid JSON string');
		}

		if($isError) {
			if(!empty($json['errors'])) {
				error_log(json_encode($json['errors']));
			}

			if(is_array($json) && (!empty($json['detail']) || !empty($json['title']))) {
				throw new RequestException('MailChimp API error: '.(!empty($json['detail']) ? $json['detail'] : $json['title']));
			}

			throw new RequestException('Undefined MailChimp API error, HTTP status code: '.$httpCode);
		}

		return $json;
	}

	public function createUrl($uri = null) {
		return 'https://'.$this->getDataCenter().self::API_URL_SUFFIX.$uri;
	}

}