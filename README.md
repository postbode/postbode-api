# Postbode.nu API
## Getting started
- Register an account on Postbode.nu (https://app.postbode.nu)
- Create an API key (https://app.postbode.nu/settings/api)

## Initialize composer package
``` bash
composer require postbode/postbode-api
```

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