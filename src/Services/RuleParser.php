<?php

namespace Mtolhuys\LaravelSchematics\Services;

class RuleParser
{
    /**
     * Possible migration methods
     * WARNING: be aware of word length vs. word matching
     * f.e. 'date' <-> 'dateTime'
     *
     * @var array
     */
    public static $methods = [
        'bigIncrements',
        'bigInteger',
        'dropColumn',
        'ipAddress',
        'macAddress',
        'mediumInteger',
        'mediumIncrements',
        'renameColumn',
        'smallIncrements',
        'timestampTz',
        'timestamp',
        'smallInteger',
        'tinyIncrements',
        'tinyInteger',
        'unsignedInteger',
        'increments',
        'dateTimeTz',
        'dateTime',
        'longText',
        'integer',
        'boolean',
        'decimal',
        'date',
        'enum',
        'geometry',
        'jsonb',
        'json',
        'point',
        'polygon',
        'string',
        'text',
        'time',
        'unsigned',
        'uuid',
        'year',
    ];

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
            $max = self::getMax($rule);
            $method = self::getMethod($rule);
            $oldName = self::getRenameFrom($rule);
            $additional = self::getAdditionalUpMethods($rule);

            $columns .=
                "\$table->{$method}({$oldName}'$column'{$max}){$additional}" .
                PHP_EOL . str_repeat(' ', 12);
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
        $methods .= ! self::isRequired($rule) ? '->nullable()' : '';
        $methods .= self::isUnique($rule) ? '->unique()' : '';
        $methods .= self::hasChanged($rule) ? '->change()' : '';

        return "$methods;";
    }

    /**
     * Check if $rule contains any of the possible methods
     *
     * @param $rule
     * @return mixed|string
     */
    public static function getMethod($rule)
    {
        foreach (self::$methods as $method) {
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
        foreach(explode('|', $rule) as $token) {
            $hasRenameRule = self::contains($token, 'from:');

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
        return self::contains($rule, 'required')
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
        return self::contains($rule, 'increments');
    }

    /**
     * Checks if columns needs to be set to unique
     *
     * @param $rule
     * @return boolean
     */
    public static function isUnique($rule): bool
    {
        return self::contains($rule, 'unique');
    }

    /**
     * Checks if columns is unsigned
     *
     * @param $rule
     * @return boolean
     */
    public static function isUnsigned($rule): bool
    {
        return self::contains($rule, 'unsigned')
            && ! self::contains($rule, 'unsignedInteger');
    }


    /**
     * Checks if columns has changed
     *
     * @param $rule
     * @return boolean
     */
    public static function hasChanged($rule): bool
    {
        return self::contains($rule, 'change');
    }

    /**
     * Checks if columns needs to be constructed as a foreign key
     *
     * @param $rule
     * @return boolean
     */
    public static function isForeign($rule): bool
    {
        return self::contains($rule, 'foreign');
    }

    /**
     * For aesthetic reasons
     *
     * @param $rule
     * @param $needle
     * @return bool
     */
    private static function contains($rule, $needle): bool
    {
        return stripos($rule, $needle) !== false;
    }
}

