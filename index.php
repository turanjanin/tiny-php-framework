<?php
session_start();

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/config/bootstrap.php';

// Short and sweet :)

Event::emit(Event::APP_INIT);

Router::instance()->callAction();

Event::emit(Event::APP_SHUTDOWN);