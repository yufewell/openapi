<?php
/**
 * File: Demo.php.
 * User: yufewell
 * Date: 2019/3/13
 * Time: 19:56
 */

use service\demo\DemoService;
use lib\Base;
use lib\Request;

class Demo extends Base
{
    public function controller(Request $request)
    {
        return $this->success([
            'demo' => (new DemoService())->getModel()
        ]);
    }
}