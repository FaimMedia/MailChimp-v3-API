<?php

namespace FaimMedia\MailChimp;

class AbstractArray implements \ArrayAccess, \Iterator, \Countable {

	protected $request;
	protected $data;

	public function __construct($request, $data = []) {
		$this->request = $request;

		$this->data = $data;
	}

	public function offsetSet($offset, $value) {
		if (is_null($offset)) {
			$this->data[] = $value;
		} else {
			$this->data[$offset] = $value;
		}
	}

	public function offsetExists($offset) {
		return isset($this->data[$offset]);
	}

	public function offsetUnset($offset) {
		unset($this->data[$offset]);
	}

	public function offsetGet($offset) {
		return isset($this->data[$offset]) ? $this->data[$offset] : null;
	}

	public function count() {
		return count($this->data);
	}

	public function rewind() {
		return reset($this->data);
	}

	public function current() {
			return current($this->data);
	}

	public function key() {
		return key($this->data);
	}

	public function next() {
		return next($this->data);
	}

	public function valid() {
		return isset($this->data[$this->key()]);
	}

	public function getFirst() {
		foreach($this->data as $item) {
			return $item;
		}
	}

	public function getLast() {
		$array = $this->data;

		if(!$array) {
			return null;
		}

		end($array);
		$key = key($array);

		return $this->data[$key];
	}
}