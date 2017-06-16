# TeeStalker
```diff
- This project is no longer being maintained.
```
Receive browser notifications for every move your Teeworlds stalkees make (joining/leaving/changing Teeworlds Servers).

<img src="http://i.imgur.com/uEOj8mu.png">

## Installation
This is a web app, so simply snatch these files and put them in your server directory. PHP is required. Also, [TwRequest.php](includes/TwRequest.php) needs sockets, so your host must also support that.
> Note: hosting this locally may cause some network lags

## Code Overview
This is written primarily with JavaScript, with a bit of PHP. Essentially, the user settings stored in the browser local storage are sent to the server; then the stalkee statuses (and some other information) are returned and displayed. Stalkee statuses and actions are displayed and included in browser notifications.

## Acknolwedgements
Thanks to [Teele for TwRequest.php](https://code.teele.eu/twrequest)


#### More detailed information can be found in the [about page](includes/about.inc.php)
