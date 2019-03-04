<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');

/*
|--------------------------------------------------------------------------
| Users
|--------------------------------------------------------------------------
|
| ...
|
*/
define('LOW_RISK_NAME', 'Riesgo Bajo');
define('MEDIUM_RISK_NAME', 'Riesgo Medio');
define('HIGH_RISK_NAME', 'Riesgo Alto');
define('LOW_RISK_TIME_GAP', 4);
define('MEDIUM_RISK_TIME_GAP', 2);
define('HIGH_RISK_TIME_GAP', 2);
define('MEDIUM_RISK_THRESHOLD', 1); //The minimum value to be in the Medium Risk range is MEDIUM_RISK_THRESHOLD
define('HIGH_RISK_THRESHOLD', 4); //The minimum value to be in the High Risk range is HIGH_RISK_THRESHOLD
define('LOW_RISK_POINT_NAME', 'Punto de Seguridad');
define('MEDIUM_RISK_POINT_NAME', 'Punto Neutro');
define('HIGH_RISK_POINT_NAME', 'Punto de Riesgo');
define('LOW_RISK_POINT', -1);
define('MEDIUM_RISK_POINT', 0);
define('HIGH_RISK_POINT', 1);

/*
|--------------------------------------------------------------------------
| Users
|--------------------------------------------------------------------------
|
| ...
|
*/

define('SUPER_ADMIN_PASS', 'demo1234');
define('SUPER_ADMIN_CODE', '666666');

define('LIKERT_FACTOR', 4);
/* End of file constants.php */
/* Location: ./application/config/constants.php */
