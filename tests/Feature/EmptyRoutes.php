<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EmptyRoutes extends TestCase
{
    private $token = 'CC03E747A6AFBBCBF8BE7668ACFEBEE5';
    
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
    
    public function testRoutesWork()
    {
        $response = $this->post('/api/login/basic');
        $response->assertStatus(400);
        
        $response = $this->post('/api/login/ean');
        $response->assertStatus(400);
    }
    
    public function testLoginFails()
    {
        $response = $this->post('/api/login/basic', [
            'user' => 'test',
            'pass' => 'test'
        ]);
        $response->assertStatus(401);      
        
        $response = $this->post('/api/login/ean', [
            'ean' => 'test'
        ]);
        $response->assertStatus(401);
    }
    
    public function testLoginWorks()
    {
        $response = $this->post('/api/login/basic', [
            'user' => 'test',
            'pass' => 'test123',
        ]);
        $response->assertStatus(200);
        
        $response = $this->post('/api/login/ean', [
            'ean' => '4770070392058',
        ]);
        $response->assertStatus(200);    
    }
    
    public function testOperaterQuery()
    {
        $response = $this->get('/api/operater', [
            'Api-Token' => $this->token
        ]);
        $response->assertStatus(200);
    }
}
