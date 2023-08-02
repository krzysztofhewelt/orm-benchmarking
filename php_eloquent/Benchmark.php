<?php

require "bootstrap.php";

use App\User;
use Illuminate\Database\Capsule\Manager as DB;

class Benchmark
{
    public $startMem;
    public function __construct()
    {
        $this->run('test1', 50);
        $this->run('test2', 50);
    }

    public function run($method, $times) {
        $tempTimes = array();
        $tempMemory = array();

        for($i = 0; $i < $times; $i++) {
            $start = microtime(true);
            $this->$method();
            $tempTimes[] = microtime(true) - $start;
        }

        echo "\navg time: " . (array_sum($tempTimes) / count($tempTimes)) * 1000;
    }

    /**
     * Runs simple select query
     *
     * @param $times
     * @return void
     */
    public function test1() {
        return User::where('account_role', 'student')->with('teacher')->first();
    }

    /**
     * Runs complex select query
     */
    public function test2() {
        return User::where('id', '>', 6)->get();
        //return User::with('courses.tasks')->get();
    }

    /*
     * Runs complex select query with nested relations
     */


    /*
     * Inserts some users
     */


    /*
     * Assigns users to courses
     */


    /*
     * Updates some courses
     */

    /*
     * deletes some
     */
}

$benchmark = new Benchmark();