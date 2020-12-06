<?php
/**
 * File: DemoService.php.
 * User: yufewell
 * Date: 2020/12/6
 * Time: 18:10
 */

namespace service\demo;

use model\demo\User;

class DemoService
{
    public function getModel()
    {
        return new User();
    }
}