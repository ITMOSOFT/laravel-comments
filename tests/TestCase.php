<?php

namespace ItmoSoft\Comments\Tests;

use ItmoSoft\Comments\CommentsServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [CommentsServiceProvider::class];
    }
}
