Validation
==========

Validation 是从Laravel的验证模块提取简化而来，旨在让你更方便的在非laravel项目中便捷的完全数据验证。

更多验证规则请阅读：http://laravel.com/docs/4.2/validation#available-validation-rules

## Usage

```php
<?php

use Overtrue\Validation\Translator;
use Overtrue\Validation\Factory as ValidatorFactory;

//初始化工厂对象
$factory = new ValidatorFactory(new Translator);


//验证
$rules = [
    'username' => 'required|min:5',
    'password' => 'confirmed',
    ///...
];

$validator = $factory->make($input, $rules);

//判断验证是否通过
if ($validator->passes()) {
    //通过
} else {
    //未通过
    //输出错误消息
    print_r($validator->messages()); // 或者 $validator->errors();
}

```

## License

MIT
