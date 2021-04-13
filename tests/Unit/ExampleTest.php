<?php

namespace Tests\Unit;

use App\Models\Gateway;
use Illuminate\Auth\Access\Gate;
use PHPUnit\Framework\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExampleTest extends TestCase
{

    public function testSimple(): void
    {
        $this->assertTrue(true);
    }

}
