<?php

it('redirects guests to login before showing the intranet', function () {
    $response = $this->get('/');

    $response->assertRedirect(route('login'));
});
