<?php

require_once __DIR__ . '/src/autoload.php';
require_once __DIR__ . '/src/config/dependencies.php';
require_once __DIR__ . '/src/config/schema.php';
require_once __DIR__ . '/src/config/assets.php';


define('THEMETIK_ASSETS_URL', app()->assets()->url(''));

app()->wp()->setup();
app()->seo()->hooks();
