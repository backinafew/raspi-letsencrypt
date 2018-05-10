# raspi-letsencrypt
Utility to request SSL certificates from Letsencrypt using Certbot, Google's API client, and PHP on the Raspberry Pi.

This utility is useful when your Raspberry Pi uses SSL certificates from Letsencrypt and your DNS is hosted on Google's 
Cloud DNS service. No need to change firewall settings to allow traffic during HTTP verification step!  The DNS verification steps 
are handled by Googles's api client.  After obtaining your new certificates, the renewal process will use these 
same settings so a recurring cron job can keep the certificates current.

Process:

-- Install --
- Install Composer: sudo apt-get install composer
- Install Certbot: sudo apt-get install certbot
- Download / extract this project.
- Download Google Cloud credentials with permission to edit your domain DNS info.  It will be a *.json file.
- cd to project folder.
- Install required PHP dependencies: composer.phar install
- Edit config-helper.php with your information: nano config-helper.php
- Edit run-certbot.sh with your information: nano run-certbot.sh
- Run to generate config.json file and view: php config-helper.php 

-- Run --
- Run script substituting your domain, ex. www.mydomain.com .... for DOMAIN_NAME: sudo ./run-certbot.sh DOMAIN_NAME
- Note folders where Letsencrypt saved the files.  Edit your web server config to look in these folders for SSL certs etc...
  These files are links pointing to the newest certificates downloaded for the domains during subsequent renewals.  
  
 -- Maintenance --
 - Renew certificates manually (They expire at 90 days.): sudo certbot renew
 - Create cron job to run renew command every two weeks: crontab -e
 - Setup schedule. Example:    01 00 1,15 * * sudo certbot renew
 
 Hope this is useful "as is" or as a base for you to start with...
