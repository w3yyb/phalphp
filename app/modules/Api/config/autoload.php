<?php

/**
 * api 
 * Auto Load Class files by namespace
 *
 * @eg 
 	'namespace' => '/path/to/dir'
 */

$autoload = [
	'Events\Api' => $dir . '/library/events/api/',
	'Micro\Messages' => $dir . '/library/micro/messages/',
	'Utilities\Debugapi' => $dir . '/library/utilities/debugapi/',
	'Security\Hmac' => $dir . '/library/security/hmac/',
	'Application' => $dir . '/library/application/',
	'Interfaces' => $dir . '/library/interfaces/',
	'Controllers' => $dir . '/modules/Api/controllers/',
	'Models' => $dir . '/models/',
	'Models\Services' => $dir . '/models/services/',
	'Models\Services\Service' => $dir . '/models/services/Service/',
	'Models\Repositories' => $dir . '/models/repositories/',
	'Models\Repositories\Repository' => $dir . '/models/repositories/Repository/',
	'Common' => $dir . '/library/common/',
];

return $autoload;
