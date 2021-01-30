<?php

namespace App\Js;

use App\Js\Vue\ParsesComputedValues;
use App\Js\Vue\ParsesWatchers;

/**
 * Class VueComponent
 *
 * @package App\Js
 */
class VueComponent
{

    use ParsesComputedValues,
        ParsesWatchers;

    public $imports = [
        [
            'import' => '{ Component, Prop, Watch, Vue }',
            'from'   => 'vue-property-decorator',
        ],
    ];
    public $name;
    public $components = [];
    public $data = [];
    public $methods = [];
    public $computed = [];
    public $watch = [];
    public $lifecycleMethods = [];
    public $extraLifecycleMethods = [];

    public function addImport(string $import, string $from)
    {
        $this->imports[] = compact('import', 'from');
    }

    public function addData(string $name, $val, bool $forceValue = false)
    {
        $value = $val;

        if ( ! $forceValue)
        {
            $value = var_export($val, true);

            if (is_null($val))
            {
                $value = 'null';
            }
        }

        $this->data[$name] = $value;
    }

    public function addMethod(string $method)
    {
        $this->methods[] = $method;
    }

    public function addLifecycleMethod(string $method)
    {
        $this->lifecycleMethods[] = $method;
    }

    public function addExtraLifecycleMethod(string $method)
    {
        $this->extraLifecycleMethods[] = $method;
    }

    public function addComputed(string $computedString)
    {
        $this->computed[] = $this->parseComputed($computedString);
    }

    public function addWatcher(string $watch)
    {
        $this->watch[] = $this->parseWatcher($watch);
    }

    public function has(array $array): bool
    {
        foreach ($array as $has)
        {
            if (empty($this->{$has}))
            {
                return false;
            }
        }

        return true;
    }
}