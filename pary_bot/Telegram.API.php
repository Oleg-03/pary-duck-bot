<?php
$API = 'https://api.telegram.org/bot';

function setToken($botToken)
{
    global $API;

    $API = $API . $botToken;
}

function sendMessage($chat_id, $text)
{
    global $API;

    $params =
    [
        'chat_id' => $chat_id,
        'text' => $text
    ];

    $ch = curl_init($API . '/sendMessage');
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    $result = curl_exec($ch);
    curl_close($ch);

    return json_decode($result, true); 
}

function deleteMessage($chat_id, $message_id)
{
    global $API;

    $params =
    [
        'chat_id' => $chat_id,
        'message_id' => $message_id
    ];

    $ch = curl_init($API . '/deleteMessage');
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    $result = curl_exec($ch);
    curl_close($ch);

    return json_decode($result, true); 
}

function sendKeyboard($chat_id, $text, $keyboard)
{
    global $API;

    // Нагадування:
    //
    // $keyboard =
    // [
    //     'inline_keyboard' =>
    //     [
    //         [
    //             ['text' => 'Text', 'callback_data' => 'Value']
    //         ]
    //     ]
    // ];

    $keyboard = json_encode($keyboard);

    $params =
    [
        'chat_id' => $chat_id,
        'text' => $text,
        'reply_markup' => $keyboard
    ];

    $ch = curl_init($API . '/sendMessage');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    $result = curl_exec($ch);
    curl_close($ch);

    return json_decode($result, true);
}

function getUpdates()
{
    global $API;

    $json = json_decode(file_get_contents($API . '/getUpdates'), true);
    
    if (end($json['result']) != false)
    {
        $json = end($json['result']);
        $offset = $json['update_id'];
        ++$offset;
        file_get_contents($API . '/getUpdates?offset=' . $offset);   
    }
    else
    {
        $json = null;
    }
        
    return $json;
}