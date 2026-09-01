<?php

$uploadCbcPath = dirname(__DIR__, 2) . '/uploads/cbc';

if (!is_dir($uploadCbcPath)) {
    mkdir($uploadCbcPath, 0777, true);
}

return [
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
	 'jwtSecret' => 'BRKS-H2H-SECRET-2026',
	 
	 
	 'bankSftp' => [
    'host' => '202.152.22.234',
    'port' => 22,
    'username' => 'reliance',
    'password' => 'reliance@brks2026',
    'directory' => '/Outgoing',
	  'upload_cbc' => $uploadCbcPath,
	],
	
	 'bankApi' => [
        'tokenUrl' => 'http://202.152.22.234:5005/token',
        'documentCallback' => 'http://202.152.22.234:5008/callback/document',
        'debiturCallback' => 'http://202.152.22.234:5008/callback/debitur',
        'clientId' => 'SIAP',
        'clientSecret' => '62bb0a61-1eaf-489e-b3f2-6a60ff8c8ffa',
        'username' => 'reliance',
        'password' => 'Brk$reliance',
		'grant_type' => 'password',
    ],
];
