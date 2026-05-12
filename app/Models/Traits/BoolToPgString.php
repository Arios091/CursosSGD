<?php

namespace App\Models\Traits;

trait BoolToPgString
{
    protected function getPgBoolFields(): array
    {
        return property_exists($this, 'pgBoolFields') ? $this->pgBoolFields : [];
    }

    protected function boolToDbString($value)
    {
        return in_array($value, [true, 'true', 1, '1', 't'], true) ? 'true' : 'false';
    }

    protected function prepareBoolFields(array &$attrs)
    {
        foreach ($this->getPgBoolFields() as $field) {
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
