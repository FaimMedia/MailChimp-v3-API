<?php

namespace FaimMedia\MailChimp;

class CampaignActions extends AbstractItem {

	public function test($testEmails = [], $sendType = 'html') {
		if(!is_array($testEmails)) {
			throw new \UnexpectedValueException('Argument `testEmails` must be an array');
		}

		foreach($testEmails as $key => $email) {
			if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				throw new \UnexpectedValueException('Email address index `'.$key.'` is not valid');
			}
		}

		if(!in_array($sendType, ['html', 'plaintext'])) {
			throw new \UnexpectedValueException('Argument `sendType` is not a valid value');
		}

		return $this->request->request('campaigns/'.$this->getCampaignId().'/actions/test', 'POST', [
			'test_emails' => $testEmails,
			'send_type'   => $sendType,
		]);
	}

	public function send() {
		return $this->request->request('campaigns/'.$this->getCampaignId().'/actions/send', 'POST');
	}

	public function schedule(\DateTime $scheduleTime, $data = []) {

		$scheduleTime->setTimezone(new DateTimeZone('UTC'));

		$data['schedule_time'] = $scheduleTime->format('c');

		return $this->request->request('campaigns/'.$this->getCampaignId().'/actions/schedule', 'POST', $data);
	}

	public function unschedule() {
		return $this->request->request('campaigns/'.$this->getCampaignId().'/actions/unschedule', 'POST', []);
	}

}