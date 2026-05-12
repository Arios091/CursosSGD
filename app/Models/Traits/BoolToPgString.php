<?php

namespace App\Models\Traits;

trait BoolToPgString
{
    protected static $pgBoolFields = [];

    protected function boolToDbString($value)
    {
        return in_array($value, [true, 'true', 1, '1', 't'], true) ? 'true' : 'false';
    }

    protected function prepareBoolFields(array &$attrs)
    {
        foreach (static::$pgBoolFields as $field) {
            if (array_key_exists($field, $attrs)) {
                $attrs[$field] = $this->boolToDbString($attrs[$field]);
            }
        }
    }

    public function getAttributesForInsert()
    {
        $attributes = parent::getAttributesForInsert();
        $this->prepareBoolFields($attributes);
        return $attributes;
    }

    public function getDirty()
    {
        $dirty = parent::getDirty();
        $this->prepareBoolFields($dirty);
        return $dirty;
    }
}
