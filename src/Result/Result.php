<?php

namespace Paynl\BancontactSDK\Result;

use function GuzzleHttp\json_encode;

class Result
{
    /**
     * @var array The data for this model
     */
    protected $_data;

    public function __construct()
    {
        $this->_data = [];
    }

    /**
     * Create an instance from an array
     *
     * @param array $data
     * @return static
     */
    public static function fromArray(array $data)
    {
        $instance = new static();
        foreach ($data as $name => $value) {
            $instance->__set($name, $value);
        }
        return $instance;
    }

    /**
     * Converts the model to a json string
     *
     * @return false|string
     */
    public function __toString()
    {
        return $this->asJson();
    }

    public function asJson()
    {
        return json_encode($this->asArray());
    }

    /**
     * @return array
     * Convert the model to an array
     */
    public function asArray(): array
    {
        return array_map(function ($value) {
            if (is_array($value)) {
                return array_map(function ($sub) {
                    if ($sub instanceof Result) return $sub->asArray();
                    return $sub;
                }, $value);
            } elseif ($value instanceof Result) {
                return $value->asArray();
            }
            return $value;
        }, $this->_data);
    }

    public function __get($name)
    {
        return $this->_data[$name] ?? null;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $value = is_array($value) ? (object)$value : $value;
        $this->_data[$name] = $value;
    }
}