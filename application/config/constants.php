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

//Constants for library and style remote loading
define('RESOURCE_DATATABLE_LANGUAGE', '//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json');
define('RESOURCE_DATATABLE_BOOTSTRAP_CSS', 'https://cdn.datatables.net/1.10.15/css/dataTables.bootstrap.min.css');
define('RESOURCE_DATATABLE_JQUERY_LIBRARY', 'https://cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js');
define('RESOURCE_DATATABLE_BOOTSTRAP_LIBRARY', 'https://cdn.datatables.net/1.10.15/js/dataTables.bootstrap.min.js');
define('RESOURCE_DATATABLE_BUTTONS_DATATABLE_LIBRARY', 'https://cdn.datatables.net/buttons/1.3.1/js/dataTables.buttons.min.js');
define('RESOURCE_DATATABLE_BUTTONS_BOOTSTRAP_LIBRARY', 'https://cdn.datatables.net/buttons/1.3.1/js/buttons.bootstrap.min.js');
define('RESOURCE_DATATABLE_BUTTONS_HTML5_LIBRARY', '//cdn.datatables.net/buttons/1.3.1/js/buttons.html5.min.js');
define('RESOURCE_DATATABLE_BUTTONS_PRINT_LIBRARY', '//cdn.datatables.net/buttons/1.3.1/js/buttons.print.min.js');
define('RESOURCE_DATATABLE_BUTTONS_COLVIS_LIBRARY', '//cdn.datatables.net/buttons/1.3.1/js/buttons.colVis.min.js');
define('RESOURCE_DATATABLE_BUTTONS_PRINT_LIBRARY', '//cdn.datatables.net/buttons/1.3.1/js/buttons.print.min.js');
define('RESOURCE_PDFMAKE_LIBRARY', '//cdn.rawgit.com/bpampuch/pdfmake/0.1.27/build/pdfmake.min.js');
define('RESOURCE_PDFMAKE_FONTS_LIBRARY', '//cdn.rawgit.com/bpampuch/pdfmake/0.1.27/build/vfs_fonts.js');
define('RESOURCE_JSZIP_LIBRARY', '//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js');
define('RESOURCE_GRAPHICS_GENERATION', 'https://code.highcharts.com/highcharts.js');
define('RESOURCE_GRAPHICS_EXPORT', 'https://code.highcharts.com/modules/exporting.js');
define('RESOURCE_FONTS_CSS', '//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css');
define('RESOURCE_BOOTSTRAP_LIBRARY', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js');
define('RESOURCE_BOOTSTRAP_LIBRARY_INTEGRITY', 'sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa');
define('RESOURCE_BOOTSTRAP_CSS', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css');
define('RESOURCE_BOOTSTRAP_BUTTONS_CSS', 'https://cdn.datatables.net/buttons/1.3.1/css/buttons.bootstrap.min.css');
define('RESOURCE_JQUERY_LIBRARY', 'https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js');
define('RESOURCE_JQUERY_MOBILE_LIBRARY', 'http://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.js');
define('RESOURCE_JQUERY_MOBILE_CSS', 'http://code.jquery.com/mobile/1.4.5/jquery.mobile-1.4.5.min.css');
define('RESOURCE_SELECT2_LIBRARY', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js');
define('RESOURCE_SELECT2_CSS', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css');
define('RESOURCE_YOUTUBE_RESIZER_LIBRARY', 'https://cdn.rawgit.com/skipser/youtube-autoresize/master/youtube-autoresizer.js');
/* End of file constants.php */
/* Location: ./application/config/constants.php */
