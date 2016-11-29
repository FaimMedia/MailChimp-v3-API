<?php

namespace FaimMedia\MailChimp;

use FaimMedia\MailChimp;

class CampaignItem extends AbstractItem {

	private $actions;
	private $content;

	public function getContentItem() {
		if(!($this->content instanceof CampaignContentItem)) {
			$response = $this->request->request('/campaigns/'.$this->getId().'/content', 'GET');

			$this->content = new CampaignContentItem($this->request, $response);
		}

		return $this->content->get();
	}

	public function setContentItem(array $data = []) {
		if(!isset($data['campaign_id'])) {
			$data['campaign_id'] = $this->getId();
		}

		$this->content = new CampaignContentItem($this->request, $data);

		$this->content->save();

		return $this->content;
	}

	public function actions() {
		$this->validateSaved();

		if(!($this->actions instanceof CampaignActions)) {
			$this->actions = new CampaignActions($this->request, [
				'campaign_id' => $this->getId(),
			]);
		}

		return $this->actions;
	}

	public function getSendChecklist() {
		return $this->request->request('/campaigns/'.$this->getId().'/send-checklist', 'GET');
	}

	public function delete() {
		$this->request->request('campaigns/'.$this->getId(), 'DELETE');

		$this->data = null;
	}

	public function save() {
		$data = $this->data;

	// validate data
		$this->validate($data);

		if($this->getId()) {
			$response = $this->request->request('campaigns/'.$this->getId(), 'PATCH', $data);
		} else {
			$response = $this->request->request('campaigns', 'POST', $data);
		}

		if($response) {
			$this->isSaved = true;

			$this->data = $response;

			return $this;
		}

		return false;
	}

	private function validate($data) {

		if(empty($data['settings'])) {
			throw new \UnexpectedValueException('Field `settings` must be a valid array');
		}

		foreach(['subject_line', 'from_name', 'reply_to'] as $field) {
			if(empty($data['settings'][$field])) {
				throw new \UnexpectedValueException('Field `settings`.`'.$field.'` has an invalid value');
			}
		}

		if(!filter_var($data['settings']['reply_to'], FILTER_VALIDATE_EMAIL)) {
			throw new \UnexpectedValueException('Field `settings`.`reply_to` is not a valid email address');
		}

	}

}