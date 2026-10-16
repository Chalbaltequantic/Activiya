<?php

return [
    // Shared FastAPI service for PO and GRN.
    'api_url' => env('GRN_API_URL', 'http://127.0.0.1:5002'),
    'api_key' => env('GRN_API_KEY', ''),
];
