<?php

test('example', function () {
    $this->get('/')
        ->assertStatus(200);
});
