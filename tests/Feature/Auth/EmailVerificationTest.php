<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_verification_is_not_enabled_for_utilisateur_accounts(): void
    {
        $this->markTestSkipped('Email verification is not configured for Utilisateur accounts in this application.');
    }
}
