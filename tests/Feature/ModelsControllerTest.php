<?php

use Mtolhuys\LaravelSchematics\Http\Controllers\ModelsController;
use Mtolhuys\LaravelSchematics\Http\Requests\CreateModelRequest;
use Mtolhuys\LaravelSchematics\Tests\TestCase;
use Illuminate\Support\Facades\File;

class ModelsControllerTest extends TestCase
{
    private $fields = [
        [
            'name' => 'id',
            'type' => 'increments',
        ],
        [
            'name' => 'name',
            'type' => null,
        ],
        [
            'name' => 'email',
            'type' => 'string|required|unique|max:255',
        ],
        [
            'name' => 'blog_id',
            'type' => 'unsigned|integer|required',
        ],
        [
            'name' => 'last_login',
            'type' => 'dateTime',
        ],
        [
            'name' => 'last_ip',
            'type' => 'ipAddress',
        ],
        [
            'name' => 'active',
            'type' => 'boolean',
        ],
        [
            'name' => 'rating',
            'type' => 'decimal',
        ],
    ];

    public function tearDown(): void
    {
        parent::tearDown();

        $controller = app_path('Http/Controllers/WombatController.php');
        $formRequest = app_path('Http/Requests/CreateWombatRequest.php');
        $model = app_path('Wombat.php');

        File::delete($controller);
        File::delete($formRequest);
        File::delete($model);
    }

    /** @test
     * @throws ReflectionException
     */
    public function it_successfully_creates_models_with_all_optional_actions()
    {
        (new ModelsController())->create(new CreateModelRequest([
            'name' => 'Wombat',
            'fields' => $this->fields,
            'actions' => [
                'hasResource' => 'true',
                'hasFormRequest' => 'true',
            ],
        ]));

        $controller = app_path('Http/Controllers/WombatController.php');

        $this->assertTrue(File::exists($controller));

        $content = File::get($controller);

        $this->assertStringContainsString("{$this->modelNamespace}Wombat", $content);
        $this->assertStringContainsString('public function show(Wombat $wombat)', $content);

        $formRequest = app_path('Http/Requests/CreateWombatRequest.php');

        $this->assertTrue(File::exists($formRequest));

        $content = File::get($formRequest);

        $this->assertStringContainsString($this->formRequestNamespace, $content);
        $this->assertStringContainsString('CreateWombatRequest', $content);
        $this->assertStringContainsString('public function rules()', $content);
        $this->assertStringContainsString("'name' => 'string|max:255',", $content);
        $this->assertStringContainsString("'email' => 'string|required|unique|max:255',", $content);
        $this->assertStringContainsString("'blog_id' => 'numeric|required',", $content);
        $this->assertStringContainsString("'last_login' => 'date',", $content);
        $this->assertStringContainsString("'last_ip' => 'ip',", $content);
        $this->assertStringContainsString("'active' => 'boolean',", $content);
        $this->assertStringContainsString("'rating' => 'numeric',", $content);

        $model = app_path('Wombat.php');

        $content = File::get($model);

        $this->assertStringContainsString(rtrim($this->modelNamespace, '\\'), $content);
        $this->assertStringContainsString('Wombat', $content);
        $this->assertStringContainsString('protected $fillable', $content);
        $this->assertStringContainsString("'id',", $content);
        $this->assertStringContainsString("'name',", $content);
        $this->assertStringContainsString("'email',", $content);
        $this->assertStringContainsString("'blog_id',", $content);
        $this->assertStringContainsString("'last_login',", $content);
        $this->assertStringContainsString("'last_ip',", $content);
        $this->assertStringContainsString("'active',", $content);
        $this->assertStringContainsString("'rating',", $content);
    }
}
