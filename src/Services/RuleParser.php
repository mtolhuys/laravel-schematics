<?php

namespace Mtolhuys\LaravelSchematics\Services;

use Mtolhuys\LaravelSchematics\Models\Migration;

class RuleParser
{
    /**
     * Parse the rules to column creation methods
     *
     * @param $fields
     * @return string
     */
    public static function fieldsToMigrationMethods(array $fields): string
    {
        $columns = '';

        foreach ($fields as $field) {
            $column = key($field);
            $rule = $field[$column];
            $break = PHP_EOL . str_repeat(' ', 12);

            if (self::isMethodOnly($rule)) {
                $columns .= "\$table->{$rule}();{$break}";

                continue;
            }

            $max = self::getMax($rule);
            $method = self::getMethod($rule);
            $oldName = self::getRenameFrom($rule);
            $additional = self::getAdditionalUpMethods($rule);

            $columns .= "\$table->{$method}({$oldName}'$column'{$max}){$additional}{$break}";
        }

        return $columns;
    }

    /**
     * @param $rule
     * @return string
     */
    public static function getAdditionalUpMethods($rule): string
    {
        $methods = '';
        $methods .= self::isUnsigned($rule) ? '->unsigned()' : '';
        $methods .= !self::isRequired($rule) ? '->nullable()' : '';
        $methods .= self::isUnique($rule) ? '->unique()' : '';
        $methods .= self::hasChanged($rule) ? '->change()' : '';

        return "$methods;";
    }

    /**
     * Check if $rule should be handled as single method
     *
     * @param $rule
     * @return mixed|string
     */
    public static function isMethodOnly($rule)
    {

        return self::ruleContains($rule, [
            'softDeletes',
            'rememberToken',
            'softDeletesTz',
        ]);
    }

    /**
     * Check if $rule contains any of the possible methods
     *
     * @param $rule
     * @return mixed|string
     */
    public static function getMethod($rule)
    {
        foreach (Migration::$methods as $method) {
            if (stripos($rule, strtolower($method)) !== false) {
                return $method;
            }
        }

        return 'string';
    }

    /**
     * Parses the max:* rule
     *
     * @param $rule
     * @return string
     */
    public static function getMax($rule): string
    {
        $max = (int)substr($rule, strpos($rule, 'max:') + 4);

        if ($max > 0) {
            return ", $max";
        }

        return '';
    }

    /**
     * Gets old name for renaming column
     *
     * @param $rule
     * @return string
     */
    public static function getRenameFrom($rule): string
    {
        foreach (explode('|', $rule) as $token) {
            $hasRenameRule = stripos($token, 'from:') !== false;

            if ($hasRenameRule) {
                $from = substr($token, strpos($token, 'from:') + 5);

                return "'$from', ";
            }
        }

        return '';
    }

    /**
     * Checks if column can be set to nullable
     *
     * @param $rule
     * @return boolean
     */
    public static function isRequired($rule): bool
    {
        return self::ruleContains($rule, ['required'])
            || self::isIncrements($rule)
            || self::isUnique($rule);
    }

    /**
     * Checks if columns needs to be set to unique
     *
     * @param $rule
     * @return boolean
     */
    public static function isIncrements($rule): bool
    {
        return self::ruleContains($rule, [
            'increments',
            'bigIncrements',
            'mediumIncrements',
            'smallIncrements',
            'tinyIncrements',
        ]);
    }

    /**
     * Checks if columns needs to be set to unique
     *
     * @param $rule
     * @return boolean
     */
    public static function isUnique($rule): bool
    {
        return self::ruleContains($rule, ['unique']);
    }

    /**
     * Checks if columns is unsigned
     *
     * @param $rule
     * @return boolean
     */
    public static function isUnsigned($rule): bool
    {
        return self::ruleContains($rule, ['unsigned'])
            && ! self::ruleContains($rule, ['unsignedInteger']);
    }


    /**
     * Checks if columns has changed
     *
     * @param $rule
     * @return boolean
     */
    public static function hasChanged($rule): bool
    {
        return self::ruleContains($rule, ['change']);
    }

    /**
     * Checks if columns needs to be constructed as a foreign key
     *
     * @param $rule
     * @return boolean
     */
    public static function isForeign($rule): bool
    {
        return self::ruleContains($rule, ['foreign']);
    }

    /**
     * For aesthetic reasons
     *
     * @param $rule
     * @param $needles
     * @return bool
     */
    private static function ruleContains($rule, array $needles): bool
    {
        return !empty(array_filter(
            array_map(static function ($rule) use ($needles) {
                return in_array($rule, $needles, true);
            }, explode('|', $rule))
        ));
    }
}

