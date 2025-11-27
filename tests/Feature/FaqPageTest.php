<?php

namespace Tests\Feature;

use Tests\TestCase;

class FaqPageTest extends TestCase
{
    /** @test */
    public function test_has_a_faq_page()
    {
        $response = $this->get('/faq');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'GENERAL QUESTIONS',
            'ACCOUNT & PROFILE',
            'SERVICES',
        ]);
    }
}
