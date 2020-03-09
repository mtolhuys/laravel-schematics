<?php

namespace Mtolhuys\LaravelSchematics\Actions\Resource;

use Mtolhuys\LaravelSchematics\Services\RuleParser;
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
            $rule = RuleParser::parseRule($field['type']);

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
}
