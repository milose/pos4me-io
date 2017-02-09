<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EmptyRoutes extends TestCase
{
    public $token;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testRoutes()
    {
        $response = $this->get('/');
        $response->assertStatus(404);
        
        $response = $this->get('/api');
        $response->assertStatus(404);
    }
    
    public function testRoutesLogin()
    {
        $response = $this->post('/api/login/basic');
        $response->assertStatus(401);
        
        $response = $this->post('/api/login/ean');
        $response->assertStatus(401);
    }
    
    public function testLogin()
    {
        $response = $this->post('/api/login/basic', [
            'user' => 'test',
            'password' => 'test123'
        ]);
        $response->assertStatus(200);
        
    }
}
