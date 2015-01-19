<?php

CswConst::c('URL', 'http://' . $_SERVER['HTTP_HOST']);
CswConst::c('HOSTNAME', $_SERVER['HTTP_HOST']);
CswConst::c('CURRENTURL', URL.$_SERVER['REQUEST_URI']);

CswConst::c('NBCHARSPERPAGE', 3000);
CswConst::c('MINPAGES', 30);

CswConst::c('NOTALLFIELD', 'Tous les champs du formulaire doivent-être complété.');
CswConst::c('WRONGDATA', 'Les données saisies ne sont pas correctes.');

CswConst::c('INTERVAL_REQUEST', 5000);
CswConst::c('LAST_REQUEST', 'last_request');
CswConst::c('SEL', 'xxxxxxxxxxxx');

CswConst::c('MAILFROM', 'xxxxxxxxxxxx');
CswConst::c('MAILFROMNAME', 'xxxxxxxxxxxx');
CswConst::c('MAILREPLY', 'xxxxxxxxxxxx');
CswConst::c('MAILHOST', 'xxxxxxxxxxxx');
CswConst::c('MAILPORT', 920);
CswConst::c('MAILUSER', 'xxxxxxxxxxxx');
CswConst::c('MAILPASSWORD', 'xxxxxxxxxxxx');
CswConst::c('MAILSMTP', 'smtp');
CswConst::c('MAILPHP', 'mail');
CswConst::c('MAILCHARSET', 'UTF-8');
CswConst::c('MAILCONTENTPATTERN', '[content]');
CswConst::c('SUFIX', 'BP');
CswConst::c('SUFFIX', 'BP');
CswConst::c('PREFIX', 'BP');

CswConst::c('MAXFILE', 5);
CswConst::c('PASSWORDLENGTH', 8);
CswConst::c('PASSWORDCHARS', '123456789abcdefghijklmnopqrtuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_&');


CswConst::c('PAYPALAPIURL', 'https://api-3t.sandbox.paypal.com/nvp?');
CswConst::c('PAYPALLOCATIONURL', 'https://www.sandbox.paypal.com/cgi-bin/webscr?');
//CswConst::c('PAYPALUSER', 'xxxxxxxxxxxx');
CswConst::c('PAYPALUSER', 'xxxxxxxxxxxx');
//CswConst::c('PAYPALPWD', 'xxxxxxxxxxxx');
CswConst::c('PAYPALPWD', 'xxxxxxxxxx');
CswConst::c('PAYPALSIGNATURE', 'xxxxxxxxxx');
CswConst::c('PAYPALRETURNURLHIGHLIGHT', 'xxxxxxxxxxxx');
CswConst::c('PAYPALRETURNURL', 'xxxxxxxxxxxx');
CswConst::c('PAYPALCANCELURLHIGHLIGHT', 'xxxxxxxxxxxx');
CswConst::c('PAYPALCANCELURL', 'xxxxxxxxxxxx');
CswConst::c('PAYPALPAYMENTACTION', 'sale');
CswConst::c('PAYPALHDRIMG', 'xxxxxxxxxxxx');

CswConst::c('LOCATIONDELAY', 2000);
CswConst::c('PIXURL', cswPref::pref('pathCdn').'images/pix.gif');

CswConst::c('NORESULT', '<div class="no-data">Il n\'y a pas de résultats.</div>');
CswConst::c('TRUNCATEENDSTRING', '...');
CswConst::c('DISPLAYPRODUCTTYPEG', 'gallery');
CswConst::c('DISPLAYPRODUCTTYPEL', 'list');

CswConst::c('NBELEMENTSPERPAGE', 21);

CswConst::c('DOCNUMEROTATIONLENGTH', '000000')

CswConst::c('STATICSPATH', 'http://cdn.local.dev/');


CswConst::c('MEDIAPATH', '../cdn');

?>