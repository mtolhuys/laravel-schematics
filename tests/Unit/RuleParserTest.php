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
            ['id' => 'bigIncrements'],
            ['soft_deletes' => 'softDeletes'],
            ['soft_deletes_tz' => 'softDeletesTz'],
            ['remember_token' => 'rememberToken'],
            ['big_id' => 'bigIncrements'],
            ['big_int' => 'bigInteger|required'],
            ['bool' => 'boolean|required'],
            ['date_time' => 'dateTime'],
            ['date_time_tz' => 'dateTimeTz'],
            ['date' => 'date'],
            ['decimal' => 'decimal'],
            ['enum' => 'enum'],
            ['geometry' => 'geometry'],
            ['increments' => 'increments'],
            ['integer' => 'integer|max:50'],
            ['integer' => 'unsigned|integer|required|change'],
            ['ip' => 'ipAddress'],
            ['json' => 'json'],
            ['jsonb' => 'jsonb'],
            ['long_text' => 'longText'],
            ['mac_address' => 'macAddress'],
            ['medium_integer' => 'mediumInteger'],
            ['medium_increments' => 'mediumIncrements'],
            ['point' => 'point'],
            ['polygon' => 'polygon'],
            ['small_increments' => 'smallIncrements'],
            ['small_integer' => 'smallInteger'],
            ['string' => 'string'],
            ['surname' => 'renameColumn|from:string'],
            ['text' => 'text'],
            ['timestamp' => 'timestamp'],
            ['timestamp_tz' => 'timestampTz'],
            ['tiny_increments' => 'tinyIncrements'],
            ['tiny_integer' => 'tinyInteger'],
            ['time' => 'time'],
            ['unsigned_integer' => 'unsignedInteger'],
            ['uuid' => 'uuid'],
            ['year' => 'year'],
        ]));

        $expected = str_replace([' ', '\r', '\n'], '',
            '$table->bigIncrements(\'id\');
            $table->softDeletes();
            $table->softDeletesTz();
            $table->rememberToken();
            $table->bigIncrements(\'big_id\');
            $table->bigInteger(\'big_int\');
            $table->boolean(\'bool\');
            $table->dateTime(\'date_time\')->nullable();
            $table->dateTimeTz(\'date_time_tz\')->nullable();
            $table->date(\'date\')->nullable();
            $table->decimal(\'decimal\')->nullable();
            $table->enum(\'enum\')->nullable();
            $table->geometry(\'geometry\')->nullable();
            $table->increments(\'increments\');
            $table->integer(\'integer\',50)->nullable();
            $table->integer(\'integer\')->unsigned()->change();
            $table->ipAddress(\'ip\')->nullable();
            $table->json(\'json\')->nullable();
            $table->jsonb(\'jsonb\')->nullable();
            $table->longText(\'long_text\')->nullable();
            $table->macAddress(\'mac_address\')->nullable();
            $table->mediumInteger(\'medium_integer\')->nullable();
            $table->mediumIncrements(\'medium_increments\');
            $table->point(\'point\')->nullable();
            $table->polygon(\'polygon\')->nullable();
            $table->smallIncrements(\'small_increments\');
            $table->smallInteger(\'small_integer\')->nullable();
            $table->string(\'string\')->nullable();
            $table->renameColumn(\'string\',\'surname\')->nullable();
            $table->text(\'text\')->nullable();
            $table->timestamp(\'timestamp\')->nullable();
            $table->timestampTz(\'timestamp_tz\')->nullable();
            $table->tinyIncrements(\'tiny_increments\');
            $table->tinyInteger(\'tiny_integer\')->nullable();
            $table->time(\'time\')->nullable();
            $table->unsignedInteger(\'unsigned_integer\')->nullable();
            $table->uuid(\'uuid\')->nullable();
            $table->year(\'year\')->nullable();
        ');

        $this->assertEquals($expected, $result);
    }
}
