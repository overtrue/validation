<?php

use Overtrue\Validation\Factory;

class ValidatorTest extends PHPUnit_Framework_TestCase
{
    protected $validator;

    public function setUp()
    {
        $this->validator = new Factory();
    }

    /**
     * @dataProvider cases
     *
     * @return
     */
    public function testCases($input, $rule, $status, $message = null)
    {
        $validator = $this->validator->make($input, $rule);

        $this->assertEquals($status, $validator->passes());

        if (!$status) {
            $this->assertSame($message, $validator->messages()->first());
        }
    }

    public function cases()
    {
        return [
            // sometimes
            [
                ['foo' => 'foo'],
                ['foo' => 'sometimes'],
                true,
            ],
            [
                ['foo' => false],
                ['foo' => 'sometimes'],
                true,
            ],

            // required
            [
                ['foo' => 'foo', 'bar' => 1, 'baz' => 0, 'overtrue' => array('0')],
                ['foo' => 'required', 'bar' => 'required', 'baz' => 'required', 'overtrue' => 'required'],
                true,
            ],
            [
                ['foo' => ' ', 'bar' => array()],
                ['foo' => 'required'],
                false,
                'The foo field is required.'
            ],
            [
                ['bar' => array()],
                ['bar' => 'required'],
                false,
                'The bar field is required.'
            ],

            // filled
            [
                [],
                ['foo' => 'filled'],
                true,
            ],
            [
                ['bar' => ''],
                ['bar' => 'required|filled'],
                false,
                'The bar field is required.'
            ],

            // required_with
            [
                ['bar' => ''],
                ['bar' => 'required_with:foo'],
                true,
            ],
            [
                ['foo' => 'overtrue'],
                ['bar' => 'required_with:foo'],
                false,
                'The bar field is required when foo is present.',
            ],

            // required_with_all
            [
                ['baz' => ''],
                ['bar' => 'required_with_all:foo,baz'],
                true,
            ],
            [
                ['foo' => 'overtrue', 'baz' => 123],
                ['bar' => 'required_with_all:foo,baz'],
                false,
                'The bar field is required when foo / baz is present.',
            ],

            // required_without
            [
                ['foo' => 'overtrue'],
                ['bar' => 'required_without:foo'],
                true,
            ],
            [
                ['foo' => '', 'baz' => 123],
                ['bar' => 'required_without:foo'],
                false,
                'The bar field is required when foo is not present.',
            ],

            // required_without_all
            [
                ['bar' => 123],
                ['bar' => 'required_without_all:foo,baz'],
                true,
            ],
            [
                ['foo' => '', 'baz' => ''],
                ['bar' => 'required_without_all:foo,baz'],
                false,
                'The bar field is required when none of foo / baz are present.',
            ],

            // required_if
            [
                ['foo' => 1],
                ['bar' => 'required_if:foo,2'],
                true
            ],
            [
                ['foo' => 1],
                ['bar' => 'required_if:foo,1'],
                false,
                'The bar field is required when foo is 1.',
            ],

            // comfirmed
            [
                ['password' => 'foo', 'password_confirmation' => 'foo'],
                ['password' => 'confirmed'],
                true,
            ],

            [
                ['password' => 'foo'],
                ['password' => 'confirmed'],
                false,
                'The password confirmation does not match.',
            ],
            [
                ['password' => 'foo', 'password_confirmation' => 'foo'],
                ['password' => 'confirmed'],
                true,
            ],

            // same
            [
                ['foo' => 'hello', 'bar' => 'hello'],
                ['foo' => 'same:bar'],
                true,
            ],
            [
                ['foo' => 'overtrue', 'bar' => 'hello'],
                ['foo' => 'same:bar'],
                false,
                'The foo and bar must match.',
            ],
            [
                ['foo' => 'overtrue', 'bar' => ''],
                ['foo' => 'same:bar'],
                false,
                'The foo and bar must match.',
            ],

            // different
            [
                ['foo' => 'overtrue', 'bar' => 'hello'],
                ['foo' => 'different:bar'],
                true,
            ],
            [
                ['foo' => 'a', 'bar' => 'a'],
                ['foo' => 'different:bar'],
                false,
                'The foo and bar must be different.',
            ],

            // accepted
            [
                ['foo' => 'yes', 'bar' => 'on', 'one' => 1, 'two' => true, 'three' => 'true'],
                [
                    'foo' => 'accepted',
                    'bar' => 'accepted',
                    'one' => 'accepted',
                    'two' => 'accepted',
                    'three' => 'accepted',
                ],
                true,
            ],
            [
                ['foo' => 'onn'],
                ['foo' => 'accepted'],
                false,
                'The foo must be accepted.',
            ],
            [
                ['foo' => 'off'],
                ['foo' => 'accepted'],
                false,
                'The foo must be accepted.',
            ],

            // boolean
            [
                [
                    'one' => false,
                    'two' => 1,
                    'three' => '1',
                    'four' => true,
                    'five' => 0,
                    'six' => '0',
                ],
                [
                    'one' => 'boolean',
                    'two' => 'boolean',
                    'three' => 'boolean',
                    'four' => 'boolean',
                    'five' => 'boolean',
                    'six' => 'boolean',
                ],
                true,
            ],
            [
                ['foo' => 'onn'],
                ['foo' => 'boolean'],
                false,
                'The foo field must be true or false',
            ],
            [
                ['foo' => 'off'],
                ['foo' => 'boolean'],
                false,
                'The foo field must be true or false',
            ],

            // array
            [
                ['foo' => []],
                ['foo' => 'array'],
                true
            ],
            [
                ['foo' => [1,2,3]],
                ['foo' => 'array'],
                true
            ],
            [
                ['foo' => 'string'],
                ['foo' => 'array'],
                false,
                'The foo must be an array.',
            ],
            // numeric
            [
                ['foo' => 1],
                ['foo' => 'numeric'],
                true
            ],
            [
                ['foo' => '2356'],
                ['foo' => 'numeric'],
                true
            ],
            [
                ['foo' => '1234string'],
                ['foo' => 'numeric'],
                false,
                'The foo must be a number.',
            ],
            [
                ['foo' => 'string1234'],
                ['foo' => 'numeric'],
                false,
                'The foo must be a number.',
            ],

            // integer
            [
                ['foo' => 0, 'bar' => 1, 'baz' => -3],
                ['foo' => 'integer'],
                true
            ],
            [
                ['foo' => 1234.5],
                ['foo' => 'integer'],
                false,
                'The foo must be an integer.',
            ],
            [
                ['foo' => '000'],
                ['foo' => 'integer'],
                false,
                'The foo must be an integer.',
            ],

            // digits
            [
                ['foo' => 0, 'bar' => 1, 'baz' => '3', 'baba' => '0'],
                ['foo' => 'digits:1'],
                true
            ],
            [
                ['foo' => 999999999999],
                ['foo' => 'digits:12'],
                true,
            ],
            [
                ['foo' => '1234string'],
                ['foo' => 'digits:4'],
                false,
                'The foo must be 4 digits.',
            ],

            // digits_between
            [
                ['foo' => 0, 'bar' => 134, 'baz' => '32345', 'baba' => '0'],
                [
                    'foo' => 'digits_between:1,5',
                    'bar' => 'digits_between:1,5',
                    'baz' => 'digits_between:1,5',
                    'baba' => 'digits_between:1,5',
                ],
                true
            ],
            [
                ['foo' => 123],
                ['foo' => 'digits_between:1,3'],
                true,
            ],
            [
                ['foo' => '1234'],
                ['foo' => 'digits_between:2,3'],
                false,
                'The foo must be between 2 and 3 digits.',
            ],

            // size
            [
                ['foo' => 12, 'bar' => [3,45], 'one' => 'ab'],
                ['foo' => 'size:2', 'bar' => 'size:2', 'one' => 'size:2'],
                true
            ],
            [
                ['foo' => 123],
                ['foo' => 'size:1'],
                false,
                'The foo must be 1 characters.'
            ],

            // between
            [
                ['foo' => 12, 'bar' => [3,45], 'one' => 'ab'],
                ['foo' => 'between:2,12', 'bar' => 'between:2,4', 'one' => 'between:1,2'],
                true
            ],
            [
                ['foo' => 123],
                ['foo' => 'numeric|between:1,2'],
                false,
                'The foo must be between 1 and 2.'
            ],
            [
                ['foo' => [1,2,3,4]],
                ['foo' => 'array|between:1,2'],
                false,
                'The foo must have between 1 and 2 items.'
            ],
            [
                ['foo' => 'item'],
                ['foo' => 'between:2,3'],
                false,
                'The foo must be between 2 and 3 characters.'
            ],

            // min
            [
                ['foo' => 1, 'bar' => [1,2], 'baz' => 'abc'],
                ['foo' => 'numeric|min:0', 'bar' => 'array|min:2', 'baz' => 'min:3'],
                true,
            ],
            [
                ['foo' => 1,],
                ['foo' => 'numeric|min:2'],
                false,
                'The foo must be at least 2.'
            ],
            [
                ['foo' => [1],],
                ['foo' => 'array|min:2'],
                false,
                'The foo must have at least 2 items.'
            ],

            // max
            [
                ['foo' => 1, 'bar' => [1,2], 'baz' => 'abc'],
                ['foo' => 'numeric|max:1', 'bar' => 'array|max:2', 'baz' => 'max:4'],
                true,
            ],
            [
                ['foo' => 1,],
                ['foo' => 'numeric|max:0'],
                false,
                'The foo may not be greater than 0.'
            ],
            [
                ['foo' => [1,5],],
                ['foo' => 'array|max:1'],
                false,
                'The foo may not have more than 1 items.'
            ],

            // in
            [
                ['foo' => 1, 'bar' => 'abc'],
                ['foo' => 'in:1,2,3', 'bar' => 'in:abc,def'],
                true,
            ],
            [
                ['foo' => 1 ],
                ['foo' => 'in:2,3'],
                false,
                'The selected foo is invalid.',
            ],
            [
                ['foo' => 'abc' ],
                ['foo' => 'in:cde,def'],
                false,
                'The selected foo is invalid.',
            ],

            // not_in
            [
                ['foo' => 1, 'bar' => 'abc'],
                ['foo' => 'not_in:2,3', 'bar' => 'not_in:bcd,def'],
                true,
            ],
            [
                ['foo' => 1 ],
                ['foo' => 'not_in:1,2'],
                false,
                'The selected foo is invalid.',
            ],
            [
                ['foo' => 'abc' ],
                ['foo' => 'not_in:abc,def'],
                false,
                'The selected foo is invalid.',
            ],

            // ip
            [
                ['ip_address' => '127.0.0.1'],
                ['ip_address' => 'ip'],
                true,
            ],
            [
                ['ip_address' => '127.0.0.1.1'],
                ['ip_address' => 'ip'],
                false,
                'The ip address must be a valid IP address.',
            ],

            // email
            [
                ['email_address' => 'foo@bar.com', 'foo' => 'f.bar@bar.com'],
                ['email_address' => 'email', 'foo' => 'email'],
                true,
            ],
            [
                ['email_address' => '127.0.0.1.1'],
                ['email_address' => 'email'],
                false,
                'The email address must be a valid email address.',
            ],

            // url
            [
                ['foo' => 'http://abc.m', 'bar' => 'https://f.bar.com', 'ftp' => 'ftp://a.c.com'],
                ['foo' => 'url', 'bar' => 'url', 'ftp' => 'url'],
                true,
            ],
            [
                ['foo' => 'abc.com'],
                ['foo' => 'url'],
                false,
                'The foo format is invalid.',
            ],

            // active_url
            [
                ['foo' => 'http://google.com', 'bar' => 'https://ip.com'],
                ['foo' => 'active_url', 'bar' => 'active_url'],
                true,
            ],
            [
                ['foo' => 'thisisanonexistswebsite.com'],
                ['foo' => 'active_url'],
                false,
                'The foo is not a valid URL.',
            ],

            // alpha
            [
                ['foo' => 'abcd', 'bar' => '汉字', 'baz' => 'Пароль'],
                ['foo' => 'alpha', 'bar' => 'alpha', 'baz' => 'alpha'],
                true,
            ],

            [
                ['foo' => '123abcd'],
                ['foo' => 'alpha'],
                false,
                'The foo may only contain letters.',
            ],

            // alpha_num
            [
                ['foo' => 'a13bcd', 'bar' => '汉2字', 'baz' => '123Пароль3'],
                ['foo' => 'alpha_num', 'bar' => 'alpha_num', 'baz' => 'alpha_num'],
                true,
            ],

            [
                ['foo' => '123abcd.w'],
                ['foo' => 'alpha_num'],
                false,
                'The foo may only contain letters and numbers.',
            ],

            // alpha_dash
            [
                ['foo' => 'a13b_cd', 'bar' => '汉2字_', 'baz' => '123П__ароль3'],
                ['foo' => 'alpha_dash', 'bar' => 'alpha_dash', 'baz' => 'alpha_dash'],
                true,
            ],

            [
                ['foo' => '123ab_cd.w'],
                ['foo' => 'alpha_dash'],
                false,
                'The foo may only contain letters, numbers, and dashes.',
            ],

            // regex
            [
                ['foo' => 'a13b_cd', 'bar' => '汉2字_', 'baz' => '123П__ароль3'],
                ['foo' => 'regex:/^[a-z_13]+$/', 'bar' => 'regex:/^[_2\p{Han}]+$/u', 'baz' => 'regex:/^[\p{L}_0-9]+$/u'],
                true,
            ],

            [
                ['foo' => '123ab_cd.w'],
                ['foo' => 'regex:/^[a-z]+$/'],
                false,
                'The foo format is invalid.',
            ],

            // date
            [
                ['foo' => '2016-03-06', 'bar' => '1970/09/08 12:23:00'],
                ['foo' => 'date', 'bar' => 'date'],
                true,
            ],
            [
                ['foo' => '+1 days'],
                ['foo' => 'date'],
                false,
                'The foo is not a valid date.',
            ],
            [
                ['foo' => '2016年 4月27日 星期三 22时08分39秒 CST'],
                ['foo' => 'date'],
                false,
                'The foo is not a valid date.',
            ],
            [
                ['foo' => '2016 12'],
                ['foo' => 'date'],
                false,
                'The foo is not a valid date.',
            ],

            // date_format
            [
                ['foo' => '2016-03-06', 'bar' => '1970/09/08 12:23:00'],
                ['foo' => 'date_format:Y-m-d', 'bar' => 'date_format:Y/m/d H:i:s'],
                true,
            ],
            [
                ['foo' => '1970/09/08 12:23:00'],
                ['foo' => 'date_format:Y-m-d'],
                false,
                'The foo does not match the format Y-m-d.',
            ],

            // before & after
            [
                ['foo' => '2016-03-04', 'bar' => '2016-05-06 12:06', 'baz' => '2016-05-06 12:06:02'],
                ['foo' => 'before:2016-03-04 01:00:00', 'bar' => 'before:2016-05-06 12:06:01', 'baz' => 'before:2016-05-06 12:06:03'],
                true,
            ],
            [
                ['start' => '2013-01-02', 'end' => '2014-02-02'],
                ['start' => 'before:end', 'end' => 'after:start'],
                true,
            ],
            [
                ['foo' => '2016-03-04'],
                ['foo' => 'before:2016-03-04 00:00:00'],
                false,
                'The foo must be a date before 2016-03-04 00:00:00.',
            ],

            // after
            [
                ['foo' => '2016-03-04', 'bar' => '2016-05-06 12:06', 'baz' => '2016-05-06 12:06:02'],
                ['foo' => 'after:2016-03-03 23:59:59', 'bar' => 'after:2016-05-06 12:05:59', 'baz' => 'after:2016-05-06 12:06:01'],
                true,
            ],
            [
                ['foo' => '2016-03-04'],
                ['foo' => 'after:2016-03-04 00:00:00'],
                false,
                'The foo must be a date after 2016-03-04 00:00:00.',
            ],

            // timezone
            [
                ['zone' => 'PRC', 'zone' => 'asia/chongqing', 'UTC'],
                ['zone' => 'timezone'],
                true,
            ],
            [
                ['zone' => 'not_a_time_zone'],
                ['zone' => 'timezone'],
                false,
                'The zone must be a valid zone.',
            ],
        ];
    }
}