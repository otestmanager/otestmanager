<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
	- User Guide
		EN : https://www.codeigniter.com/userguide2/libraries/email.html
		KO : http://codeigniter-kr.org/user_guide_2.1.0/libraries/email.html
			 http://www.ciboard.co.kr/user_guide/kr/libraries/email.html

	- Linux Server :
		ERROR: Failed to connect to server: Permission denied (13)
			First, try to out put the settings you currently have:
				-----------------------------------------------------
				-	$ getsebool httpd_can_sendmail					-
				-	httpd_can_sendmail --> off						-
				-	$ getsebool httpd_can_network_connect			-
				-	httpd_can_network_connect --> off				-
				-----------------------------------------------------

			If you get something similar, you should set these settings on.
				$ setsebool -P httpd_can_sendmail 1
				$ setsebool -P httpd_can_network_connect 1

			If you get error messages like:
				Cannot set persistent booleans without managed policy.
				Could not change policy booleans

			You may have the need to run these commands as root and you may need to sudo.
				$ sudo setsebool -P httpd_can_sendmail 1
				$ sudo setsebool -P httpd_can_network_connect 1

			Now, try sending emails using your script!

	- SMTP host use ssl
		Must include openssl.dll(PHP Dynamic Extensions)

		ex) Use Google smtp. add next line.
		 $config['smtp_host'] = 'ssl://smtp.gmail.com';
		 $config['smtp_port'] = 465;
*/

/*
$config['protocol'] = 'smtp';
$config['smtp_host'] = '{host}';
$config['smtp_port'] = '25';
$config['smtp_user'] = '{user}';
$config['smtp_pass'] = '{password}';
$config['wordwrap'] = TRUE;
*/
?>