<?php

use Security\CRSF;
use Api\Output;
    
if (!LOCAL_USER_LOGIN) {
    Output::error('Server is set to not allow local logins', 400);
}

if (!MANUAL_REGISTRATION) {
    Output::error('Server does not permit manual registration', 400);
}

// Registration form here
