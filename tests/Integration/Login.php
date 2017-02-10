<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class Login extends TestCase
{
    private $header = [
        'Api-Token' => 'CC03E747A6AFBBCBF8BE7668ACFEBEE5'
    ];
    
    /** @test */
    public function login_routes_work()
    {
        $response = $this->post('/api/login/basic');
        $response->assertStatus(400);
        
        $response = $this->post('/api/login/ean');
        $response->assertStatus(400);
    }
    
    /** @test */
    public function login_fails()
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
    
    /** @test */
    public function login_works()
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
    
    public function testDokumentStavke()
    {
        $response = $this->get('/api/dokument/5/stavke', $this->header);
        
        $response->assertStatus(200);
        
        $this->assertArrayHasKey('stavke', json_decode($response->content(), true));
    }
}
