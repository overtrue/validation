<?php

/*
 * This file is part of the overtrue/validation.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Overtrue\Validation;

interface TranslatorInterface
{
    /**
     * translator.
     *
     * @param $key message key.
     *
     * @return string
     */
    public function trans($key);
}
