<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function kirim_otp_fonnte($no_hp, $kodeOtp)
{
    $token = 'v7wuTxga87X9HxZTqkLZ';

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

    // 🔥 DEBUG (PENTING)
    log_message('error', 'FONNTE RESPONSE: '.$response);

    return $response;
}
