<?php

namespace Tests\Feature;

use App\Category;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    static private $name1;

    static private $name2;

    static private $name3;

    static private $updatedName;

    static private $uniqueString;

    static private $firstId;

    /**
     * @var CategoryTest
     */
    private $prepared;


    public static function setUpBeforeClass()/* The :void return type declaration that should be here would cause a BC issue */
    {
        self::$uniqueString = Carbon::now()->format("ymdhis");
        self::$name1 = "c1-" . self::$uniqueString;
        self::$name2 = "c2-" . self::$uniqueString;
        self::$name3 = "c3-" . self::$uniqueString;
        self::$updatedName = "u-" . self::$uniqueString;

        parent::setUpBeforeClass();
    }


    protected function setUp()
    {
        $this->prepared = $this->withHeader("Content-Type", "application/json");
        parent::setUp();
    }


    /**
     * Test response when creating category with non fillable fields (id, deleted_at)
     */
    public function testCreationWithNonFillableFields()
    {
        $date =  Carbon::now()->format("y-m-d h:i:s");
        $response = $this->prepared->postJson("/api/categories", [
            "name" => self::$name3,
            "id" => 5000,
            "deleted_at" => $date,
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonMissing(["id" => 5000]);
        $response->assertJson(["data" => ["deleted_at" => null]]);
    }


    /**
     * Test the response when creation one category
     * @depends testCreationWithNonFillableFields
     */
    public function testCreationFirst()
    {
        $tableName = env("DB_PREFIX") . (new Category)->getTable();
        self::$firstId = DB::select("SHOW TABLE STATUS LIKE '$tableName'")[0]->Auto_increment;

        $response = $this->prepared->postJson("/api/categories", [
            "name" => self::$name1,
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure(["data" => ["id", "name"]]);
        $response->assertJson([
            "data" => [
                "name" => self::$name1,
                "deleted_at" => null,
            ],
        ]);
    }


    /**
     * Test response when creating a second category with unique name
     * @depends testCreationFirst
     */
    public function testCreationSecond()
    {
        $response = $this->prepared->postJson("/api/categories", [
            "name" => self::$name2,
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJson([
            "data" => [
                "id" => self::$firstId + 1,
                "deleted_at" => null,
                "name" => self::$name2,
            ],
        ]);
    }

    /**
     * Test response when creating category with null name
     * @depends testCreationSecond
     */
    public function testCreationWithNameNull()
    {
        $response = $this->prepared->postJson("/api/categories", [
            "name" => null,
        ]);

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
        $response->assertJsonStructure(["error"]);
        $this->assertTrue(str_contains($response->getContent(), "SQLSTATE"));
    }


    /**
     * Test response when creating category with empty name string
     * @depends testCreationSecond
     */
    public function testCreationWithEmptyStringName()
    {
        $response = $this->prepared->postJson("/api/categories", [
            "name" => "",
        ]);

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
        $response->assertJsonStructure(["error"]);
        $this->assertTrue(str_contains($response->getContent(), "SQLSTATE"));
    }


    /**
     * Test response when creating category without name
     * @depends testCreationSecond
     */
    public function testCreationWithoutName()
    {
        $response = $this->prepared->postJson("/api/categories", []);

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
        $response->assertJsonStructure(["error"]);
        $this->assertTrue(str_contains($response->getContent(), "SQLSTATE"));
    }


    /**
     * Test response when creating a category when name is already used
     * @depends testCreationSecond
     */
    public function testCreationWhenNameAlreadyUsed()
    {
        $name1 = self::$name1;

        $response = $this->prepared->postJson("/api/categories", [
            "name" => $name1,
        ]);

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
        $response->assertJsonStructure(["error"]);
        $this->assertTrue(str_contains($response->getContent(), "SQLSTATE"));
    }


    /**
     * Test response if the first category is existing
     * @depends testCreationSecond
     */
    public function testFindWhenExists()
    {
        $id = self::$firstId;
        $response = $this->prepared->getJson("/api/categories/{$id}");

        $response->assertJson([
            "data" => [
                "id" => self::$firstId,
                "deleted_at" => null,
                "name" => self::$name1,
            ],
        ]);
    }


    /**
     * Test response when the wanted category is not existing
     * @depends testCreationSecond
     */
    public function testFindWhenNotExists()
    {
        $id = self::$firstId + 2; // The third here should have failed due to Unique constraint, but id is still incremented
        $response = $this->prepared->getJson("/api/categories/{$id}");

        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $response->assertJson(["data" => null]);
    }


    /**
     * Test response when updating an existing with an unused name
     * @depends testCreationSecond
     */
    public function testUpdate()
    {
        $id = self::$firstId;
        $response = $this->prepared->putJson("/api/categories/{$id}", [
            "name" => self::$updatedName,
        ]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            "data" => [
                "id" => self::$firstId,
                "deleted_at" => null,
                "name" => self::$updatedName,
            ],
        ]);
    }


    /**
     * Test response when updating an existing with an unused name
     * @depends testCreationSecond
     */
    public function testUpdateWithAlreadyUsedName()
    {
        $id = self::$firstId;
        $name2 = self::$name2;
        $response = $this->prepared->putJson("/api/categories/{$id}", [
            "name" => self::$name2,
        ]);

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
        $response->assertJsonStructure(["error"]);
        $this->assertTrue(str_contains($response->getContent(), [
                "SQLSTATE",
                "Integrity constraint violation",
                "Duplicate entry '{$name2}'",
            ]
        ));
    }


    /**
     * Test response when updating a non-existing category
     * @depends testCreationSecond
     */
    public function testUpdateWhenNotExists()
    {
        $id = self::$firstId + 2; // The third here should have failed due to Unique constraint, but id is still incremented
        $response = $this->prepared->putJson("/api/categories/{$id}", [
            "name" => "azertyu",
        ]);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $response->assertJson(["data" => null]);
    }


    /**
     * Test response when valid deletion
     * @depends testUpdate
     */
    public function testDelete()
    {
        $id = self::$firstId;
        $response = $this->prepared->deleteJson("/api/categories/{$id}");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            "data" => [
                "id" => self::$firstId,
                "name" => self::$updatedName,
            ],
        ]);
        $response->assertJsonMissing(["deleted_at" => null]);
    }


    /**
     * Test response when valid deletion
     * @depends testDelete
     */
    public function testDeleteWhenNotFound()
    {
        $id = self::$firstId;
        $response = $this->prepared->deleteJson("/api/categories/{$id}");

        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $response->assertJson(["data" => null]);
    }
}
