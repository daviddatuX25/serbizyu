<?php

namespace Tests\Feature;

use Tests\TestCase;

class AboutPageTest extends TestCase
{
    /** @test */
    public function test_has_an_about_page()
    {
        $response = $this->get('/about');

        $response->assertStatus(200);
        $response->assertViewIs('about');
        $response->assertViewHas('about');
        $response->assertSee('Our Story');
    }
}
