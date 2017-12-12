<?php
/**
 * PHP version 7.1
 *
 * This source file is subject to the license that is bundled with this package in the file LICENSE.
 */

namespace PhUml\Code;

class Method
{
    private static $symbols = [
        'private' => '-',
        'protected' => '#',
        'public' => '+',
    ];

    /** @var string */
    public $name;

    /** @var string */
    public $modifier;

    /** @var Variable[] */
    public $params;

    public function __construct(string $name, string $modifier = 'public', array $params = [])
    {
        $this->name = $name;
        $this->modifier = $modifier;
        $this->params = $params;
    }

    public function isConstructor(): bool
    {
        return $this->name === '__construct';
    }

    public function __toString()
    {
        return sprintf(
            '%s%s%s',
            self::$symbols[$this->modifier],
            $this->name,
            empty($this->params) ? '()' : '( ' . implode($this->params, ', ') . ' )'
        );
    }
}