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

use Closure;

class Factory
{
    /**
     * The Translator implementation.
     *
     * @var \Overtrue\Validation\TranslatorInterface
     */
    protected $translator;

    /**
     * The Presence Verifier implementation.
     *
     * @var \Overtrue\Validation\PresenceVerifierInterface
     */
    protected $verifier;

    /**
     * All of the custom validator extensions.
     *
     * @var array
     */
    protected $extensions = [];

    /**
     * All of the custom implicit validator extensions.
     *
     * @var array
     */
    protected $implicitExtensions = [];

    /**
     * All of the custom validator message replacers.
     *
     * @var array
     */
    protected $replacers = [];

    /**
     * All of the fallback messages for custom rules.
     *
     * @var array
     */
    protected $fallbackMessages = [];

    /**
     * The Validator resolver instance.
     *
     * @var Closure
     */
    protected $resolver;

    /**
     * Create a new Validator factory instance.
     *
     * @param \Overtrue\Validation\TranslatorInterface $translator
     *
     * @return \Overtrue\Validation\Factory
     */
    public function __construct(TranslatorInterface $translator = null)
    {
        $this->translator = $translator ?: new Translator();
    }

    /**
     * Create a new Validator instance.
     *
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     *
     * @return \Overtrue\Validation\Validator
     */
    public function make(array $data, array $rules, array $messages = [], array $customAttributes = [])
    {
        // The presence verifier is responsible for checking the unique and exists data
        // for the validator. It is behind an interface so that multiple versions of
        // it may be written besides database. We'll inject it into the validator.
        $validator = $this->resolve($data, $rules, $messages, $customAttributes);

        if (!is_null($this->verifier)) {
            $validator->setPresenceVerifier($this->verifier);
        }

        $this->addExtensions($validator);

        return $validator;
    }

    /**
     * Add the extensions to a validator instance.
     *
     * @param \Overtrue\Validation\Validator $validator
     */
    protected function addExtensions(Validator $validator)
    {
        $validator->addExtensions($this->extensions);

        // Next, we will add the implicit extensions, which are similar to the required
        // and accepted rule in that they are run even if the attributes is not in a
        // array of data that is given to a validator instances via instantiation.
        $implicit = $this->implicitExtensions;

        $validator->addImplicitExtensions($implicit);

        $validator->addReplacers($this->replacers);

        $validator->setFallbackMessages($this->fallbackMessages);
    }

    /**
     * Resolve a new Validator instance.
     *
     * @param array $data
     * @param array $rules
     * @param array $messages
     * @param array $customAttributes
     *
     * @return \Overtrue\Validation\Validator
     */
    protected function resolve(array $data, array $rules, array $messages, array $customAttributes)
    {
        if (is_null($this->resolver)) {
            return new Validator($this->translator, $data, $rules, $messages, $customAttributes);
        } else {
            return call_user_func($this->resolver, $this->translator, $data, $rules, $messages, $customAttributes);
        }
    }

    /**
     * Register a custom validator extension.
     *
     * @param string          $rule
     * @param \Closure|string $extension
     * @param string          $message
     */
    public function extend($rule, $extension, $message = null)
    {
        $this->extensions[$rule] = $extension;

        if ($message) {
            $this->fallbackMessages[snake_case($rule)] = $message;
        }
    }

    /**
     * Register a custom implicit validator extension.
     *
     * @param string          $rule
     * @param \Closure|string $extension
     * @param string          $message
     */
    public function extendImplicit($rule, $extension, $message = null)
    {
        $this->implicitExtensions[$rule] = $extension;

        if ($message) {
            $this->fallbackMessages[snake_case($rule)] = $message;
        }
    }

    /**
     * Register a custom implicit validator message replacer.
     *
     * @param string          $rule
     * @param \Closure|string $replacer
     */
    public function replacer($rule, $replacer)
    {
        $this->replacers[$rule] = $replacer;
    }

    /**
     * Set the Validator instance resolver.
     *
     * @param \Closure $resolver
     */
    public function resolver(Closure $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * Get the Translator implementation.
     *
     * @return \Overtrue\Validation\TranslatorInterface
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * Get the Presence Verifier implementation.
     *
     * @return \Overtrue\Validation\PresenceVerifierInterface
     */
    public function getPresenceVerifier()
    {
        return $this->verifier;
    }

    /**
     * Set the Presence Verifier implementation.
     *
     * @param \Overtrue\Validation\PresenceVerifierInterface $presenceVerifier
     */
    public function setPresenceVerifier(PresenceVerifierInterface $presenceVerifier)
    {
        $this->verifier = $presenceVerifier;
    }
}
