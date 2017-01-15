<?php

require_once __DIR__ . '/../../idiorm.php';

ORM::configure('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME);
ORM::configure('username', DB_USER);
ORM::configure('password', DB_PASSWORD);
