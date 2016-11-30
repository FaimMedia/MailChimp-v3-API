# MailChimp PHP v3 API library by FaimMedia.nl #

I couldn't find a good simple PHP library for the MailChimp v3 API, so decided to created one.  
Currently it has basic support for `campaigns` (including some `actions`), `lists` and corresponding `members`. No tests are included at this moment, so please create an issue for any unexpected results you might run into.

## Installation ##

You can install this library by editing (or creating) your `composer.json`:

	{
		"require": {
			"faimmedia/mailchimp-v3-api": "*"
		}
	}

And run `composer update` or `composer install`.

## Usage ##

There are some more examples in the `example` folder. All methods can be found in the section below.

All examples below require the initialization of the MailChimp class with your API key. The API key is a 32-length hash followed by your MailChimp datacenter location, separated with a minus. An API key can be obtained here: [https://us14.admin.mailchimp.com/account/api/](https://us14.admin.mailchimp.com/account/api/).

	$apiKey = '02699f222d95d4dfc53b9f7960b2fa6e-us1';

	$mailchimp = new FaimMedia\MailChimp($apiKey);

For all API end-points, required and allowed fields, please check out the MailChimp API documentation page: [https://developer.mailchimp.com/documentation/mailchimp/reference/overview/](https://developer.mailchimp.com/documentation/mailchimp/reference/overview/).

### Campaigns ###

#### Create a new campaign ####

	$campaign = $mailchimp->campaigns()->create([
		'settings' => [
			'subject_line' => 'My newsletter',
			'from_name'    => 'My company',
			'reply_to'     => 'newsletter@mycompany.com',
		],
		'type'     => 'regular',
	]);

#### Get all existing campaigns ####

	$campaigns = $mailchimp->campaigns()->getAll();

#### Get campaign by id ####

	$campaign = $mailchimp->campaigns()->getById($campaignId);
	// or
	$campaign = $mailchimp->campaigns($campaignId);

### Campaign content ####

#### Set content of a campaign ####

	$campaign->setContentItem([
		'html' => 'HTML content of my campaign',
		'text' => 'Text content of my campaign'
	]);

#### Get content of a campaign ####

	$content = $campaign->getContentItem();
	echo $content->getHtml();
	echo $content->getText();

### Campaign actions ###

#### Send a campaign ####

	$campaign->actions()->send();

#### Send test mail for campaign ####

	$campaign->actions()->test();

#### Schedule a campaign ####

The schedule method only excepts a `DateTime` object as first argument.

	$datetime = new \DateTime('2018-02-01 01:00:00');

	$campaign->actions()->schedule($datetime);

#### Unschedule a campaign ####

	$campaign->actions->unschedule();

### Lists ###

#### Create a new list

	$list = $mailchimp->lists()->create([
		'name'     => 'Test newsletter list',
		'contact'  => [
			'company'  => 'FaimMedia.nl',
			'address1' => 'PO Box 1540',
			'city'     => 'NIJMEGEN',
			'state'    => 'GL',
			'zip'      => '6501 BM',
			'country'  => 'NL',
		],
		'permission_reminder' => 'You signed up for updates on our website',
		'campaign_defaults'   => [
			'from_name'    => 'FaimMedia.nl',
			'from_email'   => 'john.doe@repo.github.com',
			'subject'      => 'Newsletter',
			'language'     => 'NL',
		],
		'email_type_option' => false,
	]);

_Returns:_ `ListItem` object

#### Get all existing lists ####

	$lists = $mailchimp->lists()->getAll();

_Returns:_ `Lists` object

#### Get list by id ####

	$list = $mailchimp->lists()->getById($listId);
	// or
	$list = $mailchimp->campaigns($listId);

_Returns:_ `ListItem` object

### List members ###

This class and methods can be used to subscribe or unsubscribe list members. When adding a member or updating the subscription status of an existing member, it's not being checked if the member exists. If it already exists the provided data will be overridden, if it doesn't exists the member will be created with the provided subscription status.

#### Add one list member (subscriber) ####

Subscribe can simple add one subscriber to the list, by using the `add` method. If you wish to subscribe multiple members to a list at once, it is recommended to use the `multi` method instead of the `add` method. The `add` method will make a new request for every email address, were `multi` can send a whole batch of email addresses at once.

	$member = $list->members()->add('johndoe@repo.github.com');

_Returns:_ `ListMemberItem` object

If you wish to include additional fields, you can specify an array as first attribute.

	$member = $list->members()->add([
		'email_address' => 'john.doe@repo.github.com',
		'merge_fields'  => [
			'FNAME' => 'John',
			'LNAME' => 'Doe',
		],
	]);

_Returns:_ `ListMemberItem` object

#### Subscribe multiple list members ####

If you wish to add multiple members to a list, you can use the `multi` method.

	$members = $list->members()->multi([
		'john.doe@repo.github.com',
		'jane.doe@repo.githib.com',
	]);

This method also provides a way to included multiple fields, you can simple specify the email address as array key in that case:

	$members = $list->members()->multi([
		'john.doe@repo.github.com' => [
			'merge_fields' => [
				'FNAME' => 'John',
				'LNAME' => 'Doe',
			],
		],
		'jane.doe@repo.github.com' => [
			'merge_fields' => [
				'FNAME' => 'Jane',
				'LNAME' => 'Doe',
			],
		],
	]);

_Returns:_ `ListMembers` object

#### Get a list member ####

	$member = $list->members()->getByEmail('john.doe@repo.github.com');

_Returns:_ `ListMemberItem` object

#### Change status of a list member ####

This method changes the list member status and immidiatly saves it.

	$member->setStatus('unsubscribed');

_Returns:_ `ListMemberItem` object

### Items ###

#### Getting properties ####

All objects returning a `AbstractItem` instance (`CampaignItem`, `ListItem`, `ListMemberItem`) can easily return the properties. All properties are accessable by calling the camelcased field prefix with get. In example, if you'd wish to get the `email_address` field, you could use the `getEmailAddress` method.

	foreach($members as $member) {
		echo $member->getId();
		echo $member->getEmailAddress();
		echo $member->getMergeFields()->getFNAME();
		echo $member->getMergeFields()->getLNAME();

	// or

		echo $member->id;
		echo $member->emailAddress;
		echo $member->mergeFields->FNAME;
		echo $member->mergeFields->LNAME;
	}

If the value is an array, you could pass the first argument of the method to get that key of the array:

	$member->getMergeFields('FNAME');

If you would like to get the property by it's `snakecase` key, you could use the `get` method.

	$member->get('id');
	$member->get('email_address');
	$member->get('merge_fields')->get('FNAME');
	$member->get('merge_fields')->get('LNAME');

To get the name of the subscriber more easily there are a few build-in methods:

	$member->getFirstName();
	// alias of $member->getMergeFields()->getFNAME();

	$member->getLastName();
	// alias of $member->getMergeFields()->getLNAME();

#### Get all properties ####

Use the `toArray` method, to get an array of all properties.

	$campaign->toArray();
	$member->toArray();
	$list->toArray();

#### Delete an item ####

You can delete items by using the `delete` method. This method returns `true` on success or throws an `Exception` on failure. Campaigns that are on `sending` status can not be deleted. Also lists that are linked to a campaign can't be deleted, in that case you need to delete the campaign first.

	$list->delete();

	$campaign->delete();

Subscribe members can be deleted, however it's not recommended according to the MailChimp API documentation. It's better to update the current member and change it's `status` to `unsubscribed`.

	$member->delete();

	// better to unsubscribe

	$member->setStatus('unsubscribed');

### Update items

First get the item you would like to update:

	// campaigns
	$campaign = $mailchimp->campaigns()->getById();

	// lists
	$list = $mailchimp->lists()->getById('ag43fe');

	// members
	$list->members()->getByEmail('john.doe@repo.github.com');

Update the fields that need changes. If you use the overloading property (`$campaign->type = 'html'`), it expects a camelized field, were the `set` method expects the regular snakecased fields.

	// campaigns
		$campaign->type = 'plaintext';
		// or
		$campaign->set('type', 'plaintext');
		// or
		$campaign->set([
			'settings' => [
				'subject_line' => 'New subject line',
				'title'        => 'Updated campaign title',
			]
		]);

	// lists
		$list->name = 'Updated list name';
		// or
		$list->set('name', 'Updated list name');
		// or
		$list->set([
			'name'       => 'Updated list name',
			'visibility' => 'prv',
		]);

	// members
		$member->status = 'cleaned';
		// or
		$member->set('status', 'cleaned');
		// or
		$member->set([
			'status'       => 'cleaned',
			'merge_fields' => [
				'FNAME' => 'Test',
			],
		]);

*Please note* that modifing multi-dimensional properties this way will not work, in example:

	$campaign->settings->subjectLine = 'Updated campaign title';

You'll need to provide an array to the first property:

	$campaign->settings = [
		'subject_line' => 'Updated campaign subject line',
	];

The new array will be merged with the old one, to if you ommit a certain field (in this case, in example, the `title` field) the original `title` will be kept.

If you don't want this behaviour you could pass an extra argument to the `set` method. This way the array would not be merged, but overwritten:

	$campaign->set('settings', [
		'subject_line' => 'Updated campaign subject line',
	], false);

Save the item:

	$campaign->save();

	$list->save();

	$member->save();


## Methods ##

Will be provided soon...  
See all examples for now, they included most methods.

## Exceptions ##

All exceptions are extended from `MailChimpException`.

### ItemException ###

Throw when an item could not be found.

### SaveException ###

Thrown when there was an error saving the item.

### RequestException ###

Thrown when there was a problem with the MailChimp API request, could be when the request itselfs fails or when the MailChimp API returns an unexpected response.