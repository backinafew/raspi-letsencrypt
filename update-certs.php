#!/usr/bin/php

<?php
/**
 * Uses Google Cloud DNS api to update certificates using
 * the Certbot program to pull certificates from letsencrypt
 * using DNS authentication.  Google's apiclient code processes
 * the verification "TXT" DNS record.
 *
 * $Date: 2018-05-10 16:10:04 -0400 (Thu, 10 May 2018) $
 * $Revision: 42 $
 * $Author: dblanch $
 * $HeadURL: https://svn.backinafew.com/svn/letsencrypt/update-certs.php $
 * $Id: update-certs.php 42 2018-05-10 20:10:04Z dblanch $
 * **/ 

require_once('vendor/autoload.php');

// load config.json info...
$config = json_decode(file_get_contents('./config.json'));

// delay after adding record before returning to certbot
$delay = 0;

//if debugging, export certbot env variables...
if ($config->runlevel == 'DEBUG') {
    putenv('CERTBOT_DOMAIN=tmp.backinafew.com');
    putenv('CERTBOT_VALIDATION=CERTBOT_VALIDATION');
    putenv('CERTBOT_AUTH_OUTPUT=CERTBOT_AUTH_OUTPUT');
} // end if ($config->runlevel == 'DEBUG')

// handle command line call options...            "-a" add record "-d" delete record "-h" help -d domain
$arguments = getopt("ar:h");
// set dns action flag...
$dnsAction = '';
// check if options given...
if (count($arguments > 0)) {
    // if -h echo and exit...
    if (array_key_exists('h', $arguments)) {
        $help = "\n" . "syntax: update-certs.php [-h help] | [-a add record] | [-r remove record] \n";
        die($help);        
    } // end if (array_key_exists('h', $arguments))
    elseif (array_key_exists('a', $arguments)) {
        $dnsAction = 'add';
    } // end if (array_key_exists('a', $arguments))
    elseif (array_key_exists('r', $arguments)) {
        $dnsAction = 'delete';
    } // end if (array_key_exists('d', $arguments))    
} // end if (count($arguments > 0))
else {
    $help = "\n" . "syntax: update-certs.php [-h help] | [-a add record] | [-r remove record] \n";
    echo $help;
    exit(1);
}
// read in certbot info...
$certbotDomain = getenv('CERTBOT_DOMAIN'); // domain for certificate /       _acme-challenge. as prefix to domain.
$certbotValidation = getenv('CERTBOT_VALIDATION'); // value for TXT record
$certbotAuthOutput = getenv('CERTBOT_AUTH_OUTPUT'); // certbot message.

// letsencrypt dns TXT record key / value...
// add _acme-challenge. to domain...
$certbotDomain = '_acme-challenge.' . $certbotDomain;    
// append root "." to validation key.  received errors without appending "."  ...
$certbotDomain = $certbotDomain . '.';

// create / configure client...
$client = new Google_Client();
$client->setAuthConfig($config->credentials_path);
$client->addScope('https://www.googleapis.com/auth/ndev.clouddns.readwrite');

$dns = new Google_Service_Dns($client);

$change = new Google_Service_Dns_Change();

//create dns entry info...
$dnsRecordSet = new Google_Service_Dns_ResourceRecordSet();
$dnsRecordSet->setKind("dns#resourceRecordSet");
$dnsRecordSet->setName($certbotDomain); //remember to use root "."
$dnsRecordSet->setType('TXT');
$dnsRecordSet->setTtl(300);

// add rrdatas as array...
$rrdatas = array($certbotValidation);
$dnsRecordSet->setRrdatas($rrdatas);
$dnsRecordSets = array($dnsRecordSet);

// add or delete record...
if ($dnsAction == 'add'){
    $change->setAdditions($dnsRecordSets);
    $delay = 60;
} // end if ($dnsAction == 'add')
elseif ($dnsAction == 'delete') {
     $change->setDeletions($dnsRecordSets);
} // end elseif

//process request...
$success = $dns->changes->create($config->project, $config->managedZone, $change);

//propagation delay...
sleep($delay);
?>