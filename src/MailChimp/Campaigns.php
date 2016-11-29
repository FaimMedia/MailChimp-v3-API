<?php

namespace FaimMedia\MailChimp;

use FaimMedia\MailChimp\Exception\ItemException;

class Campaigns extends AbstractArray {

	public function getById($id) {

		if(array_key_exists($id, $this->data)) {
			return $this->data[$id];
		}

		$response = $this->request->request('campaigns/'.$id);

		if($response) {
			return new CampaignItem($this->request, $response);
		}

		throw new ItemException('Campaign does not exists');
	}

	public function getAll($cache = true) {
		if(!$this->data || !$cache) {
			$response = $this->request->request('campaigns');

			foreach($response['campaigns'] as $campaigns) {
				$campaigns = new ListItem($this->request, $campaigns);

				$this->data[$campaigns->getCampaignId()] = $campaigns;
			}
		}

		return $this->data;
	}

	public function create($data) {
		$campaign = new CampaignItem($this->request, $data);

		return $campaign->save();
	}

}