<?php

namespace Overtrue\Validation;

/**
 * 验证类使用的默认消息翻译工具
 */
class Translator implements TranslatorInterface
{
    protected $messages = [];


    /**
     * 设置验证消息列表
     *
     * @param $messages
     */
    function __construct(array $messages)
    {
        $this->messages = ['validation' => $messages];
    }

    /**
     * 翻译
     *
     * @param string $key 点拼接的key
     *
     * @return string
     */
    public function trans($key)
    {
        return arrayGet($this->messages, $key);
    }

    /**
     * 使用点字符串获取
     *
     * @param  array   $array
     * @param  string  $key
     * @param  mixed   $default
     * 
     * @return mixed
     */
    protected function arrayGet($array, $key, $default = null)
    {
        if (is_null($key)) return $array;

        if (isset($array[$key])) return $array[$key];

        foreach (explode('.', $key) as $segment) {
            if ( ! is_array($array) || ! array_key_exists($segment, $array)) {
                return value($default);
            }

            $array = $array[$segment];
        }

        return $array;
    }
}