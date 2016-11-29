<?php

namespace FaimMedia\MailChimp;

use FaimMedia\MailChimp\Exception\ItemException;

class Lists extends AbstractArray {

	public function getById($id) {

		if(array_key_exists($id, $this->data)) {
			return $this->data[$id];
		}

		$response = $this->request->request('lists/'.$id);

		if($response) {
			return new ListItem($this->request, $response);
		}

		throw new ItemException('List does not exists');
	}

	public function getAll($cache = true) {
		if(!$this->data || !$cache) {
			$response = $this->request->request('lists');

			foreach($response['lists'] as $list) {
				$list = new ListItem($this->request, $list);

				$this->data[$list->getId()] = $list;
			}
		}

		return $this;
	}

	public function create($list) {

		if(is_array($list)) {
			$list = new ListItem($this->request, $list);
		}

		if(!($list instanceof ListItem)) {
			throw new \InvalidArgumentException('First argument must be an array or an instanceof ListItem');
		}

		return $list->save();
	}

}