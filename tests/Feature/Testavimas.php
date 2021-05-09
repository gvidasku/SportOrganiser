<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class Testavimas extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {

         $this->visit('{{URL('/')}}')
             ->click('Sporto Užsiėmimų Organizavimas')
             ->seePageIs('{{URL('/')}}');
    }
}
