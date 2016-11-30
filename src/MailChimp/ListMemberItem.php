<?php

namespace FaimMedia\MailChimp;

use FaimMedia\MailChimp;

class ListMemberItem extends AbstractItem {

	public function getFirstName() {
		return $this->getMergeFields('FNAME');
	}

	public function getLastName() {
		return $this->getMergeFields('LNAME');
	}

	public function getMergeFields($field = null) {
		if(!empty($this->data['merge_fields'])) {
			if($field !== null) {
				if(!empty($this->data['merge_fields'][$field])) {
					return $this->data['merge_fields'][$field];
				}

				return null;
			}

			return $this->data['merge_fields'];
		}

		return null;
	}

	public function getSubscriberHash() {
		if($this->getId()) {
			return $this->getId();
		} else if(array_key_exists('email_address', $this->data)) {
			return MailChimp::parseEmail($this->data['email_address']);
		}

		return null;
	}

	public function setStatus($status) {
		$this->data['status'] = $status;

		$this->save();
	}

	public function delete() {

		$this->request->request('lists/'.$this->getListId().'/members/'.$this->getSubscriberHash(), 'DELETE');

		$this->data = null;
	}

	public function save() {
		$data = $this->data;

		$data += [
			'status_if_new' => 'subscribed',
			'status'        => 'subscribed',
			'email_type'    => 'html',
		];

		unset($data['list_id']);

	// validate data
		self::validate($data);

		$response = $this->request->request('lists/'.$this->getListId().'/members/'.$this->getSubscriberHash(), 'PUT', $data);

		if($response) {
			$this->isSaved = true;

			return $this->set($response, null, false);
		}

		return false;
	}

	public static function validate($data) {

		if(empty($data['email_address']) || !filter_var($data['email_address'], FILTER_VALIDATE_EMAIL)) {
			throw new \UnexpectedValueException('Field `email_adress` is not a valid email address');
		}

		$allowed = ['subscribed', 'unsubscribed', 'cleaned', 'pending'];
		foreach(['status', 'status_if_new'] as $field) {
			if(empty($data[$field]) || !in_array($data[$field], $allowed)) {
				throw new \UnexpectedValueException('Field `'.$field.'` has an invalid value ('.$data[$field].'), must be: '.join(', ', $allowed));
			}
		}

	}

}