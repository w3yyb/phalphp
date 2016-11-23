<?php

/**
 * webapp
 * Auto Load Class files by namespace
 *
 * @eg 
 	'namespace' => '/path/to/dir'
 */

$autoload = [
	'Events\Api' => $dir . '/library/events/api/',
	'Micro\Messages' => $dir . '/library/micro/messages/',
	'Utilities\Debug' => $dir . '/library/utilities/debug/',
	'Security\Hmac' => $dir . '/library/security/hmac/',
	'Security\Access' => $dir . '/library/security/access/',
	'Application' => $dir . '/library/application/',
	'Interfaces' => $dir . '/library/interfaces/',
	'Controllers' => $dir . '/modules/Admin/controllers/',
	'Models' => $dir . '/models/',
	'Models\Services\Service' => $dir . '/models/services/Service/',
	'Models\Repositories' => $dir . '/models/repositories/',
	'Models\Repositories\Repository' => $dir . '/models/repositories/Repository/',
	'Common' => $dir . '/library/common/',
];

return $autoload;
