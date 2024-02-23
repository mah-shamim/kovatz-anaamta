<?php

namespace ElementskitVendor;

// Don't redefine the functions if included multiple times.
if (!\function_exists('ElementskitVendor\\GuzzleHttp\\Promise\\promise_for')) {
    require __DIR__ . '/functions.php';
}
