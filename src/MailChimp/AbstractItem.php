<?php

namespace FaimMedia\MailChimp;

use FaimMedia\MailChimp;
use FaimMedia\MailChimp\Exception\SaveException;

class AbstractItem {

	protected $request;
	protected $data = [];

	protected $isSaved = false;

	public function __construct(Request $request, $data = []) {
		$this->request = $request;

		$this->data = $data;
	}

	public function __call($name, $arguments) {

		if(substr($name, 0, 3) == 'get') {
			$key = MailChimp::uncamelize(substr($name, 3));

			if(array_key_exists($key, $this->data)) {
				return $this->data[$key];
			}

			return null;
		}
	}

	protected function isSaved() {
		return $this->isSaved;
	}

	protected function validateSaved() {
		if(!$this->isSaved()) {
			throw new SaveException('This item is not saved and could not be modified');
		}
	}

	public function getData() {
		return $this->toArray();
	}

	public function toArray() {
		return $this->data;
	}

	public function getStats() {
		return $this->stats;
	}

	public function getLinks() {
		return $this->_links;
	}

}