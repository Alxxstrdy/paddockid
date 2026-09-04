<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function kirim_otp_fonnte($no_hp, $kodeOtp)
{
    $token = getenv('FONNTE_API_TOKEN');
    if (empty($token)) {
        log_coded_error('PFT-5001', 'FONNTE_API_TOKEN tidak dikonfigurasi');
        return false;
    }

    $pesan = "Kode OTP kamu adalah *{$kodeOtp}*\n\nJangan bagikan kode ini ke siapa pun.";

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "https://api.fonnte.com/send",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => [
            "Authorization: $token"
        ],
        CURLOPT_POSTFIELDS => [
            "target" => $no_hp,
            "message" => $pesan,
            "countryCode" => "62"
        ],
    ]);

    $response = curl_exec($curl);
    curl_close($curl);

    return $response;
}
