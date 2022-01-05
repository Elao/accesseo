<?php

declare(strict_types=1);

namespace Elao\Bundle\Accesseo\Checker;

use Brick\Schema\Base;
use Brick\Schema\Interfaces\Thing;
use Brick\Schema\SchemaTypeList;

class Microdata
{
    public string $type;
    public array $values;

    public function __construct(string $type, array $values)
    {
        $this->type = $type;
        $this->values = $values;
    }

    public static function create($thing)
    {
        $type = \Closure::bind(function () {return $this->type[0]; }, $thing, Base::class)();
        $values = [];

        foreach (\Closure::bind(function () {return $this->values; }, $thing, Base::class)() as $key => $value) {
            if ($value instanceof Thing) {
                $values[$key] = static::create($value);
            } elseif ($value instanceof Base) {
                $values[$key] = static::create($value);
            } elseif ($value instanceof SchemaTypeList) {
                $list = \Closure::bind(function () {return $this->values; }, $value, SchemaTypeList::class)();
                $items = [];

                foreach ($list as $item) {
                    $items[] = \is_object($item) ? static::create($item) : $item;
                }

                $values[$key] = \count($items) > 1 ? $items : $items[0];
            } else {
                $values[$key] = \get_class($value);
            }
        }

        return new Microdata($type, $values);
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getValues(): array
    {
        return $this->values;
    }
}
