<?php

use Security\CRSF;
use Response\DieCode;
    
if (!LOCAL_USER_LOGIN) {
    DieCode::kill('Server is set to not allow local logins', 400);
}

if (!MANUAL_REGISTRATION) {
    DieCode::kill('Server does not permit manual registration', 400);
}

// Registration form here
