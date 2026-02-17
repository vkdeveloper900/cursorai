<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| This file is the single entrypoint for API routes. It simply includes
| separate route files for user and admin modules to keep things clean.
|
*/

require __DIR__.'/api/user.php';
require __DIR__.'/api/admin.php';
