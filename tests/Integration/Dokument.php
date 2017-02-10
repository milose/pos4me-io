<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class Dokument extends TestCase
{
    private $header = [
        'Api-Token' => 'CC03E747A6AFBBCBF8BE7668ACFEBEE5'
    ];
    
    /** @test */
    public function spisak_is_returned()
    {
        $response = $this->get('/api/dokument/spisak', $this->header);
        
        $response->assertStatus(200);
        
        $this->assertArrayHasKey('vrste', json_decode($response->content(), true));
    }
    
    /** @test */
    public function list_by_vrsta_is_returned()
    {
        $response = $this->get('/api/dokument/vrsta/13', $this->header);
        
        $response->assertStatus(200);
        
        $this->assertArrayHasKey('dokumenti', json_decode($response->content(), true));
    }
    
    /** @test */
    public function dokument_is_returned()
    {
        $response = $this->get('/api/dokument/5', $this->header);
        
        $response->assertStatus(200);
        
        $this->assertArrayHasKey('dokument', json_decode($response->content(), true));
    }
    
    /** @test */
    public function vezani_dokument_is_returned()
    {
        $response = $this->get('/api/dokument/5/vezani', $this->header);
        
        $response->assertStatus(200);
        
        $this->assertArrayHasKey('dokument', json_decode($response->content(), true));
    }
    
    /** @test */
    public function dokument_can_be_found_by_ean()
    {
        $response = $this->get('/api/dokument/find/255', $this->header);
        
        $response->assertStatus(200);
        
        $this->assertArrayHasKey('dokument', json_decode($response->content(), true));
    }
}