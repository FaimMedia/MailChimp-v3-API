<?php

namespace FaimMedia\MailChimp;

use FaimMedia\MailChimp;
use FaimMedia\MailChimp\Exception\ItemException;

class ListMembers extends AbstractArray {

	private $listId;

	public function getListId() {
		return $this->listId;
	}

	public function setListId($id) {
		$this->listId = $id;
	}

	public function add($member, $status = 'subscribed') {

		if(is_string($member)) {
			$member = [
				'email_address' => $member,
				'status'        => $status,
				'status_if_new' => $status,
			];
		}

		if(is_array($member)) {
			if(!array_key_exists('list_id', $member)) {
				$member['list_id'] = $this->getListId();
			}

			$member = new ListMemberItem($this->request, $member);
		}

		if(!($member instanceof ListMemberItem)) {
			throw new \InvalidArgumentException('First argument must be an array or an instanceof ListMemberItem');
		}

		return $member->save();
	}

	public function multi(array $members = [], $status = 'subscribed') {

		$data = [];
		foreach($members as $email => $member) {
			if(!is_array($member)) {
				$member = [
					'email_address' => $member,
				];
			}

			if(!is_int($email)) {
				$member['email_address'] = $email;
			}

			$member += [
				'status'        => $status,
				'status_if_new' => (!empty($member['status']) ? $member['status'] : $status),
			];

			ListMemberItem::validate($member);

			$data[] = $member;
		}

		$response = $this->request->request('lists/'.$this->getListId(), 'POST', [
			'members'         => $data,
			'update_existing' => true,
		]);

		return $this->parseMultiResponse($response);
	}

	private function parseMultiResponse($response) {
		if($response) {
			$addedMembers = [];

			foreach(['new_members', 'updated_members'] as $field) {
				foreach($response[$field] as $member) {
					$member += [
						'is_new'     => ($field == 'new_members'),
						'is_updated' => ($field == 'updated_members'),
					];

					$memberItem = new ListMemberItem($this->request, $member);

					$addedMembers[$memberItem->getSubscriberHash()] = $memberItem;
				}
			}

			return new self($this->request, $addedMembers);
		}

		return false;
	}

	public function getByEmail($email) {

		$subscriberHash = MailChimp::parseEmail($email);

		if(array_key_exists($subscriberHash, $this->data)) {
			return $this->data[$subscriberHash];
		}

		$response = $this->request->request('lists/'.$this->getListId().'/members/'.$subscriberHash);

		if($response) {
			return new ListMemberItem($this->request, $response);
		}

		throw new ItemException('List member does not exists');
	}

	public function getAll($cache = true) {
		if(!$this->data || !$cache) {
			$response = $this->request->request('lists/'.$this->getListId().'/members', 'GET');

			foreach($response['members'] as $member) {
				$member = new ListMemberItem($this->request, $member);

				$this->data[$member->getSubscriberHash()] = $member;
			}
		}

		return $this;
	}

}