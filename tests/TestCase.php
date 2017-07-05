<?php

namespace Tests;

use App\Exceptions\Handler;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;


    protected function setUp()
    {
        parent::setUp();
        $this->disableExceptionHandling();
    }

    public function signIn($user = null)
    {
    	$user = $user ?: factory('App\User')->create();

    	$this->be($user);

    	return $this;
    }

    protected function disableExceptionHandling()
    {
        $this->oldExceptionHandler = $this->app->make(ExceptionHandler::class);

        $this->app->instance(ExceptionHandler::class, new TestHandler);
    }

    protected function withExceptionHandling()
    {
        $this->app->instance(ExceptionHandler::class, $this->oldExceptionHandler);

        return $this;
    }


}

class TestHandler extends Handler
{
    public function __construct()
    {
    }
    public function report(\Exception $e)
    {
    }
    public function render($request, \Exception $e)
    {
        throw $e;
    }
}

