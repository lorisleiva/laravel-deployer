<?php

namespace Lorisleiva\LaravelDeployer\Concerns;

use Illuminate\Support\Str;

trait RendersCode
{
    protected function render($value, $indent = 1, $allow_env = true)
    {
        switch (gettype($value)) {
            case 'array':
                return $this->renderArray($value, $indent, $allow_env);
            case 'string':
                return (Str::startsWith($value, 'env(') && $allow_env)
                    ? $value
                    : "'" . addslashes($value) . "'";
            case 'boolean':
                return $value ? 'true' : 'false';
            default:
                return is_null($value) ? 'null' : $value;
        }
    }

    protected function renderArray($value, $indent, $allow_env)
    {
        $indentParent = str_repeat('    ', $indent);
        $indentChildren = str_repeat('    ', $indent + 1);

        if (empty($value)) {
            return "[\n$indentChildren//\n$indentParent]";
        }

        $arrayContent = collect($value)
            ->map(function ($v, $k) use ($indent, $allow_env, $indentChildren) {
                $v = $this->render($v, $indent + 1, $allow_env);
                return is_string($k) 
                    ? "$indentChildren'$k' => $v"
                    : "$indentChildren$v";
            })
            ->implode(",\n");

        return "[\n$arrayContent,\n$indentParent]";
    }
}
