#!/bin/bash

#################################################
# Utility to request new letsencrypt certificate.
#
# $Date: 2018-05-08 15:33:40 -0400 (Tue, 08 May 2018) $
# $Revision: 41 $
# $Author: dblanch $
# $HeadURL: https://svn.backinafew.com/svn/letsencrypt/run-certbot.sh $
# $Id: run-certbot.sh 41 2018-05-08 19:33:40Z dblanch $
# ###############################################

# check to see if domain name argument entered...
if test $# -ne 0;
then    sudo certbot certonly --manual --preferred-challenges=dns \
            --manual-auth-hook "php /home/dblanch/projects/letsencrypt/update-certs.php -a" \
            --manual-cleanup-hook "php /home/dblanch/projects/letsencrypt/update-certs.php -r" \
            -m blanchdon1@comcast.net \
            --agree-tos \
            --manual-public-ip-logging-ok \
            -d $1
else
    echo "Please enter domain name." 
fi