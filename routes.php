<?php

require_once __DIR__ . '/router.php';

// ##################################################
// ##################################################
// ##################################################

// Static GET
// In the URL -> http://localhost
// The output -> Index
get('/', '/pages/index.php');
get('/signup', '/pages/auth/signup.php');
get('/login', '/pages/auth/login.php', );

post('/signup', '/app/controller/Signup.php');
post('/login', '/app/controller/Login.php');



// For GET or POST
// The 404.php which is inside the views folder will be called
// The 404.php has access to $_GET and $_POST
any('/404', 'pages/404.php');
