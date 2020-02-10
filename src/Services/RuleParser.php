<?php

namespace Mtolhuys\LaravelSchematics\Services;

class RuleParser
{
    public static $columnTypes = [
        'date',
        'dateTime',
        'decimal',
        'integer',
        'string',
        'text',
        'time',
        'timestamp',
        'unsigned',
    ];

    /**
     * Parse the rules to column creation methods
     *
     * @param $rules
     * @return string
     */
    public static function rulesToMigrationColumns(array $rules): string
    {
        $columns = '$table->increments(\'id\');' . PHP_EOL;

        foreach ($rules as $column => $rule) {
            $type = self::getType($rule);
            $max = self::getMax($rule);
            $unsigned = self::isUnsigned($rule) ? '->unsigned()' : '';
            $nullable = self::isRequired($rule) ? '' : '->nullable()';
            $unique = self::isUnique($rule) ? '->unique()' : '';

            $columns .=
                str_repeat(' ', 12) .
                "\$table->{$type}('$column'{$max}){$unsigned}{$nullable}{$unique};" .
                PHP_EOL;
        }

        return $columns . str_repeat(' ', 12) . '$table->timestamps();';
    }

    /**
     * Check if $rule contains any of the supported type
     * WARNING: be aware of word length vs. word matching f.e. 'date' <-> 'dateTime'
     * In that case the longest word should appear last in the array
     *
     * @param $rule
     * @return mixed|string
     */
    public static function getType($rule)
    {
        foreach (self::$columnTypes as $type) {
            if (stripos($rule, strtolower($type)) !== false) {
                return $type;
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
     * Checks if column can be set to nullable
     *
     * @param $rule
     * @return boolean
     */
    public static function isRequired($rule): bool
    {
        return self::contains($rule, 'required')
            || self::isUnique($rule);
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
        return self::contains($rule, 'unsigned');
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

    private static function contains($rule, $needle): bool
    {
        return stripos($rule, $needle) !== false;
    }
}

