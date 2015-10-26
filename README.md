# ABOUT Push Error To Slack

This package is send error to slack by webhook.
Only need to install and setting slack webhook.

## install
1.Clone this repo into PKGPATH/push-error-to-slack
2.Copy APPPATH/config/pets.php from PKGPATH/push-error-to-slack/config/pets.php
3.Access incoming-webhook (https://my.slack.com/services/new/incoming-webhook/),get webhook url
4.Set webhook url config

## APPPATH/config/config.php
```
'always_load'  => array(
		'packages' =>array(
			'push-error-to-slack',
		),
	),
```

## APPPATH/config/pets.php
```
return array(
	'webhook_url' => 'This is webhook url',
	'channel' => 'set channel name stating with # or @',
	'icon_url' => 'your service icon url', //your service icon url
	'mode' => 'attachments' //payload or attachments
);
```
