# Postbode.nu API
## Getting started
- Register an account on Postbode.nu (https://app.postbode.nu)
- Create an API key (https://app.postbode.nu/settings/api)

## Initialize composer package
``` bash
composer require postbode/postbode-api
```

## Complete documentation
Our complete API is available at https://api.postbode.nu

## Usage
### List all available mailboxes
```php
$postbode = new \Postbode\PostbodeClient(API_KEY);
$mailboxes = $postbode->getMailboxes();
foreach($mailboxes AS $mailbox){
    // use $mailbox
}
```
### List all letters in mailbox
```php
$postbode = new \Postbode\PostbodeClient(API_KEY);
$letters = $postbode->getLetters(MAILBOX_ID);
foreach($letters AS $letter){
    // use $letter
}
```
### Send letter
```php
$postbode = new \Postbode\PostbodeClient(API_KEY);

$filename = 'example.pdf';
$envelope_id = 2;
$country = 'NL';
$registered = false; // Registered letter
$send_direct = false; // Create concept in mailbox

$letter = $postbode->sendLetter(MAILBOX_ID, $filename, $envelope_id, $country, $registered, $send_direct);
if(!is_array($letter)){
    echo 'Failed! Errorcode: '.$letter;
}else{
    echo 'Letter sent!';
    echo '<br /><pre>';
    print_r($letter);
}
```