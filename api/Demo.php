<?php
/**
 * File: Demo.php.
 * User: yufewell
 * Date: 2019/3/13
 * Time: 19:56
 */

class Demo extends Base
{
    public function index($request = [])
    {
        return $this->success([
            'demo' => 'it is demo...'
        ]);
    }
}