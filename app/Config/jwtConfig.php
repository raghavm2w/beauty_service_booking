<?php
return [
    'secret' => $_ENV['JWT_SECRET'],
    'algo'   => 'HS256',

    'access_expiry'  => 30 * 60,          // 30 minutes
    'refresh_expiry' => 7 * 24 * 60 * 60,  // 7 days

    'issuer' => 'beauty_booking_api'
];