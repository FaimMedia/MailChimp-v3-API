#!/usr/bin/php
<?php

/*
	This example shows how to create a new list and multiple methods to add subscribers
*/

require __DIR__ . '/../vendor/autoload.php';

// initialize class
	$mailchimp = new FaimMedia\MailChimp($apiKey);

// create list
	$list = $mailchimp->lists()->create([
		'name'     => 'Example list',
		'contact'  => [
			'company'  => 'FaimMedia.nl',
			'address1' => 'PO Box 1540',
			'city'     => 'NIJMEGEN',
			'state'    => 'GL',
			'zip'      => '6501 BM',
			'country'  => 'NL',
		],
		'permission_reminder' => 'You have signed up for this email on our website',
		'campaign_defaults'   => [
			'from_name'    => 'FaimMedia.nl',
			'from_email'   => 'john.doe@repo.github.com',
			'subject'      => 'Example list subject',
			'language'     => 'NL',
		],
		'email_type_option' => false,
	]);

// add one subscriber
	$list->members()->add('john.doe@repo.github.com');

// unsubscribe one subscriber
	$list->members()->add('jane.doe@repo.github.com', 'unsubscribed');

// add multiple subscribers
	$list->members()->multi([
		'john.roe@repo.github.com',
		'jane.roe@repo.github.com',
	], 'subscribed');

// add multiple subscribers with additional fields
	$list->members()->multi([
		'john.appleseed@repo.github.com' => [
			'email_type'   => 'html',
			'merge_fields' => [
				'FNAME'      => 'John',
				'LNAME'      => 'Appleseed',
			],
			'language'    => 'NL',
		],
	]);

// print list id
	echo 'List created: '.$list->getId();