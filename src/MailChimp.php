<?php

namespace FaimMedia;

class MailChimp {

	private $isBatch = false;

	private $request;
	private $apiKey;

	public function __construct($apiKey = null) {
		$this->request = new MailChimp\Request($apiKey);

		$this->apiKey = $apiKey;
	}

	public function startBatch() {
		$this->isBatch = true;
	}

	public function endBatch() {
		$this->isBatch = false;

		// do save
	}

	public function lists($id = null) {
		$list = new MailChimp\Lists($this->request);

		if($id) {
			return $list->getById($id);
		}

		return $list;
	}

	public function campaigns($id = null) {
		$campaign = new MailChimp\Campaigns($this->request);

		if($id) {
			return $campaign->getById($id);
		}

		return $campaign;
	}

	public static function parseEmail($email) {
		return strtolower(md5($email));
	}

	public static function uncamelize($str) {
		$str = preg_replace(
			["/([A-Z]+)/", "/_([A-Z]+)([A-Z][a-z])/"],
			["_$1", "_$1_$2"],
			lcfirst($str)
		);

		return strtolower($str);
	}

}