<?php

namespace Mtolhuys\LaravelSchematics\Actions\Resource;

use Mtolhuys\LaravelSchematics\Services\StubWriter;

class CreateFormRequestAction
{
    /**
     * @param $request
     * @return string
     */
    public function execute($request)
    {
        $model = $request['name'];
        $namespace = config('schematics.form-request-namespace');
        $stub = __DIR__ . '/../../../resources/stubs/form-request.stub';
        $file = app_path(str_replace(['App\\', '\\'], ['', '/'], $namespace) . "/Create{$model}Request.php");

        (new StubWriter($file, $stub))->write([
            '$namespace$' => $namespace,
            '$class$' => "Create{$model}Request",
            '$rules$' => trim($this->fieldsToRules($request['fields'])),
        ]);
    }

    /**
     * @param array $fields
     * @return string
     */
    private function fieldsToRules(array $fields): string
    {
        $rules = '';

        foreach ($fields as $index => $field) {
            $rule = $this->parseRule($field['type']);

            if ($rule === null) {
                continue;
            }

            if ($index === 0) {
                $rules .= "'{$field['name']}' => '{$rule}',";
            } else {
                $rules .= PHP_EOL . str_repeat(' ', 12) . "'{$field['name']}' => '{$rule}',";
            }
        }

        return $rules;
    }

    /**
     * @param $type
     * @return string|null
     */
    private function parseRule($type = null): ?string
    {
        if (! $type) {
            return 'string|max:255';
        }

        $valid = array_diff(explode('|', $type), [
            'dropColumn',
            'increments',
            'renameColumn',
            'unsigned',
        ]);

        if (empty($valid)) {
            return null;
        }

        return $this->toRule(implode('|', $valid));
    }

    /**
     * @param string $rawRules
     * @return string
     */
    private function toRule(string $rawRules): string
    {
        $translations = [
            'dateTime' => 'date',
            'decimal' => 'numeric',
            'integer' => 'numeric',
            'ipAddress' => 'ip',
            'string' => 'string',
            'text' => 'string',
            'timestamp' => 'date',
            'time' => 'date',
        ];

        return str_replace(
            array_keys($translations),
            array_values($translations),
            $rawRules
        );
    }
}
