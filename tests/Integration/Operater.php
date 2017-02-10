<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class Operater extends TestCase
{
    private $header = [
        'Api-Token' => 'CC03E747A6AFBBCBF8BE7668ACFEBEE5'
    ];
    
    /** @test */
    public function operater_is_returned()
    {
        $response = $this->get('/api/operater', $this->header);
        $response->assertStatus(200);
    }
}