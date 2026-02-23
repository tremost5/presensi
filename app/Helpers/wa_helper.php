<?php

function formatWA($no)
{
    $no = preg_replace('/[^0-9]/', '', $no);

    if (str_starts_with($no, '0')) {
        $no = '62' . substr($no, 1);
    }

    if (!str_starts_with($no, '62')) {
        return false;
    }

    return $no;
}

function kirimWA($no, $pesan)
{
    $url = 'https://api.fonnte.com/send';

    $token = env('FONNTE_TOKEN'); // WAJIB ADA

    if (!$token) {
        log_message('error', 'TOKEN WA TIDAK ADA');
        return false;
    }

    $data = [
        'target' => $no,
        'message' => $pesan,
        'countryCode' => '62'
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($data),
        CURLOPT_HTTPHEADER => ["Authorization: $token"],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10
    ]);

    $result = curl_exec($ch);
    $err    = curl_error($ch);
    curl_close($ch);

    if ($err) {
        log_message('error', 'WA ERROR: '.$err);
        return false;
    }

    log_message('info', 'WA SENT: '.$result);
    return true;
}
