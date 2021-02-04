<?php

namespace api\account;

use service\demo\DemoService;
use lib\Base;

class Login extends Base
{
    // username,auth,captcha,mobile
    // type:1用户名密码登录,2手机号验证码登录
    protected $rules = [
        'u' => 'if:t=1,required,length_range:1~30',
        'a' => 'if:t=1,required,length_range:8~50',
        'c' => 'required,length:6',
        'm' => 'if:t=2,required,length:11',
        't' => 'required,length:1',
    ];

    public function controller()
    {
        $this->success($this->request);
    }
}