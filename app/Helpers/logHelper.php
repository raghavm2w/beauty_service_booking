<?php

use App\Models\Log;

function log_event(
    string $type,
    string $action,
    string $message = '',
    ?int $user_id = null
) {

    try {
        $log = new Log();
        $log->create($user_id, $type, $action, $message);
    } catch (\Exception $e) {
        error_log("Log failed: " . $e->getMessage());
    }
}
