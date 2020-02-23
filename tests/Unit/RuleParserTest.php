<?php

use Mtolhuys\LaravelSchematics\Services\RuleParser;
use Mtolhuys\LaravelSchematics\Tests\TestCase;

class RuleParserTest extends TestCase
{
    /**
     * @test
     */
    public function it_successfully_parses_fields_to_migration_methods(): void
    {
        $result = str_replace([' ', '\r', '\n'], '', RuleParser::fieldsToMigrationMethods([
            ['id' => 'increments'],
            ['name' => 'string|max:10'],
            ['surname' => 'renameColumn|from:name'],
            ['email' => 'string|unique'],
            ['daily_alarm' => 'time'],
            ['daily_alarm' => 'dropColumn|required'],
            ['last_login' => 'dateTime'],
            ['profile_id' => 'unsigned|integer'],
            ['profile_id' => 'unsigned|integer|required|change'],
            ['active' => 'boolean|required'],
            ['balance' => 'decimal'],
            ['ip' => 'ipAddress'],
            ['bio' => 'text'],
        ]));

        $expected = str_replace([' ', '\r', '\n'], '',
            '$table->increments(\'id\');
            $table->string(\'name\',10)->nullable();
            $table->renameColumn(\'name\',\'surname\')->nullable();
            $table->string(\'email\')->unique();
            $table->time(\'daily_alarm\')->nullable();
            $table->dropColumn(\'daily_alarm\');
            $table->dateTime(\'last_login\')->nullable();
            $table->integer(\'profile_id\')->unsigned()->nullable();
            $table->integer(\'profile_id\')->unsigned()->change();
            $table->boolean(\'active\');
            $table->decimal(\'balance\')->nullable();
            $table->ipAddress(\'ip\')->nullable();
            $table->text(\'bio\')->nullable();
        ');

        $this->assertEquals($expected, $result);
    }
}
