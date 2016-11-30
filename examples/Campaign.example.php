#!/usr/bin/php
<?php

/*
	This example shows how to create a new campaign, and use this following actions:
	- Setting the campaign content;
	- Getting the send checklist;
	- Sending a test email;
	- Send the campaign;
	- Delete the campaign.
*/

require __DIR__ . '/../vendor/autoload.php';

$mailchimp = new FaimMedia\MailChimp($apiKey);

// checking for argument
	if(empty($argv[1])) {
		throw new \Exception('Please provide a list id in the first argument');
	}

// setting an existing list id
	$listId = $argv[1];

// create a new campaign
	$campaign = $mailchimp->campaigns()->create([
		'recipients' => [
			'list_id'      => $listId,
		],
		'settings' => [
			'subject_line' => 'Subject',
			'from_name'    => 'John Doe',
			'reply_to'     => 'noreply@repo.github.com',
		],
		'type'     => 'regular',
	]);

// setting campaign content
	$campaign->setContentItem([
		'html' => 'Test content',
	]);

// get campaign send checklist
	var_dump($campaign->getSendChecklist());

// sending test email
	$campaign->actions()->test(['john.doe@repo.github.com']);

// sending campaign to all subscribers
	$campaign->actions()->send();

// deleting campaign
// (wouldn't directly work in this example, because the campaign would be in sending state)
	$campaign->delete();
