<?php


namespace App\Model;

use JsonSerializable;

trait JsonSerializeTrait
{
    public function jsonSerialize()
    {
        $data = [];
        if (property_exists(static::class, 'serializableAttributes'))
            foreach (static::$serializableAttributes as $attribute => $options) {
                $getter = 'get' . ucfirst($attribute);
                if (!is_callable($this, $getter))
                    continue;
                $value = $this->$getter();
                if (is_array($value) || $value instanceof JsonSerializable)
                    $data[$attribute] = $data;
            }
        return $data;
    }
}
