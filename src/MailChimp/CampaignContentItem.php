<?php

namespace FaimMedia\MailChimp;

use FaimMedia\MailChimp;

class CampaignContentItem extends AbstractItem {

	public function save() {
		$data = $this->data;

		unset($data['campaign_id']);

	// validate data
		$this->validate($data);

		$response = $this->request->request('campaigns/'.$this->getCampaignId().'/content', 'PUT', $data);

		if($response) {
			$this->isSaved = true;

			return new self($this->request, $response);
		}

		return false;
	}

}