<?php

declare(strict_types=1);

use App\App\Core\Session;

/**
 */

function get_current_user()
{
   return Session::get('user');
}
