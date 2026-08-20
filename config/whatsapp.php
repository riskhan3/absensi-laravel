<?php

return [
    /*
    |--------------------------------------------------------------------------
    | WhatsApp via Fonnte API
    |--------------------------------------------------------------------------
    | Token diperoleh dari https://fonnte.com/
    | WHATSAPP_ENABLED=true hanya aktifkan jika token sudah diisi dan terverifikasi
    */

    'token'   => env('WHATSAPP_TOKEN', ''),
    'enabled' => env('WHATSAPP_ENABLED', false),
    'api_url' => env('WHATSAPP_API_URL', 'https://api.fonnte.com/send'),
    'timeout' => (int) env('WHATSAPP_TIMEOUT', 10),
];
