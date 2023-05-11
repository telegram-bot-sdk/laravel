<?php

use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\ResponseObject;

test('fake returns the given response', function () {
    Telegram::fake([
        ResponseObject::make([
            'id' => 123456789,
            'first_name' => 'Test',
            'username' => 'testbot',
        ]),
    ]);

    $response = Telegram::sendMessage([
        'chat_id' => 987654321,
        'text' => 'Hello World',
    ]);

    expect($response['id'])->toBe(123456789);
});
