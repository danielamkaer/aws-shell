<?php

require 'vendor/autoload.php';

$shell = new Shell([
    'mounts' => [
        Ec2Mount::class,
    ],
    'commands' => [
        LsCommand::class,
        CdCommand::class,
    ],
]);

$shell->loop();
