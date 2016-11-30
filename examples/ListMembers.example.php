#!/usr/bin/php
<?php

/*
	This example shows how to get all lists with all members
*/

require __DIR__ . '/../vendor/autoload.php';

$mailchimp = new FaimMedia\MailChimp($apiKey);

// get all existing lists
	$lists = $mailchimp->lists()->getAll();

// loop through lists
	foreach($lists as $listId => $list) {

	// print the list name
		echo 'List name: '.$list->getName().PHP_EOL;
		echo 'List email: '.$list->getId().PHP_EOL;

	// getting all members of the list
		$members = $list->members()->getAll();

	// loop through members
		foreach($members as $member) {
			echo ' - Email address: '.$member->getEmailAddress().PHP_EOL;
			echo ' - Status: '.$member->getStatus().PHP_EOL;
			echo ' - Last name: '.$member->getLastName().PHP_EOL;
			echo ' - First name: '.$member->getFirstName().PHP_EOL;

			if(!empty($argv[1]) && $argv[1] == 'unsubscribe') {
				$member->setStatus('unsubscribed');
			}

			var_dump($member->toArray());

			var_dump($member->getStatus());

			var_dump($member->getMergeFields()->toArray());

			var_dump($member->getMergeFields()->getFNAME());

			var_dump($member->mergeFields->FNAME);

			var_dump($member->get('merge_fields')->get('FNAME'));
		}
	}