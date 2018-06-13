<?php

namespace Tests\Feature;

use App\Category;
use App\Menu;
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

    static private $menu1Id;

    static private $menu2Id;

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


    private function findNumberOfCategories()
    {
        return Category::all()->count();
    }

    private function findNumberOfMenus($catId){
        return Category::with("menus")->find($catId)->menus->count();
    }


    /**
     * Test response when creating category with non fillable fields (id, deleted_at)
     */
    public function testCreationWithNonFillableFields()
    {
        $count = $this->findNumberOfCategories();

        $date = Carbon::now()->format("y-m-d h:i:s");

        $response = $this->prepared->postJson("/api/categories", [
            "name" => self::$name3,
            "id" => 5000,
            "deleted_at" => $date,
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonMissing(["id" => 5000]);
        $response->assertJson(["data" => ["deleted_at" => null]]);

        $countAfter = $this->findNumberOfCategories();
        self::assertEquals($count + 1, $countAfter);
    }


    /**
     * Test the response when creation one category
     * @depends testCreationWithNonFillableFields
     */
    public function testCreationFirst()
    {
        $count = $this->findNumberOfCategories();

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

        $countAfter = $this->findNumberOfCategories();
        self::assertEquals($count + 1, $countAfter);
    }


    /**
     * Test response when creating a second category with unique name
     * @depends testCreationFirst
     */
    public function testCreationSecond()
    {
        $count = $this->findNumberOfCategories();

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

        $countAfter = $this->findNumberOfCategories();
        self::assertEquals($count + 1, $countAfter);
    }


    /**
     * Test response when creating category with null name
     * @depends testCreationSecond
     */
    public function testCreationWithNameNull()
    {
        $count = $this->findNumberOfCategories();
        $response = $this->prepared->postJson("/api/categories", [
            "name" => null,
        ]);

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
        $response->assertJsonStructure(["error"]);
        $this->assertTrue(str_contains($response->getContent(), "SQLSTATE"));

        $countAfter = $this->findNumberOfCategories();
        self::assertEquals($count, $countAfter);
    }


    /**
     * Test response when creating category with empty name string
     * @depends testCreationSecond
     */
    public function testCreationWithEmptyStringName()
    {
        $count = $this->findNumberOfCategories();
        $response = $this->prepared->postJson("/api/categories", [
            "name" => "",
        ]);

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
        $response->assertJsonStructure(["error"]);
        $this->assertTrue(str_contains($response->getContent(), "SQLSTATE"));

        $countAfter = $this->findNumberOfCategories();
        self::assertEquals($count, $countAfter);
    }


    /**
     * Test response when creating category without name
     * @depends testCreationSecond
     */
    public function testCreationWithoutName()
    {
        $count = $this->findNumberOfCategories();

        $response = $this->prepared->postJson("/api/categories", []);

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
        $response->assertJsonStructure(["error"]);
        $this->assertTrue(str_contains($response->getContent(), "SQLSTATE"));

        $countAfter = $this->findNumberOfCategories();
        self::assertEquals($count, $countAfter);
    }


    /**
     * Test response when creating a category when name is already used
     * @depends testCreationSecond
     */
    public function testCreationWhenNameAlreadyUsed()
    {
        $count = $this->findNumberOfCategories();

        $name1 = self::$name1;
        $response = $this->prepared->postJson("/api/categories", [
            "name" => $name1,
        ]);

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR);
        $response->assertJsonStructure(["error"]);
        $this->assertTrue(str_contains($response->getContent(), "SQLSTATE"));

        $countAfter = $this->findNumberOfCategories();
        self::assertEquals($count, $countAfter);
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
     * Test response when attaching a menu
     * @depends testUpdateWhenNotExists
     */
    public function testAttachingMenu()
    {
        $count = $this->findNumberOfMenus(self::$firstId);

        self::$menu1Id = Menu::create(["name" => self::$name1, "price" => 0])->id;
        self::$menu2Id = Menu::create(["name" => self::$name2, "price" => 0])->id;

        $firstId = self::$firstId;
        $menu1Id = self::$menu1Id;

        $response = $this->postJson("/api/categories/{$firstId}/menus/{$menu1Id}");

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure(["data" => ["id", "deleted_at", "name"]]);

        $countAfter = $this->findNumberOfMenus(self::$firstId);
        $this->assertEquals($count + 1, $countAfter);
    }


    /**
     * Test response when attaching a second menu
     * Check the database
     * @depends testAttachingMenu
     */
    public function testAttachingSecondMenu()
    {
        $count = $this->findNumberOfMenus(self::$firstId);

        $firstId = self::$firstId;
        $menu1Id = self::$menu2Id;

        $response = $this->postJson("/api/categories/{$firstId}/menus/{$menu1Id}");

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure(["data" => ["id", "deleted_at", "name"]]);

        // Check in database

        $category = Category::with("menus")->find(self::$firstId);

        $this->assertEquals(2, $category->menus->count());
        $this->assertEquals(self::$menu1Id, $category->menus[0]->id);
        $this->assertEquals(self::$menu2Id, $category->menus[1]->id);

        $countAfter = $this->findNumberOfMenus(self::$firstId);
        $this->assertEquals($count + 1, $countAfter);
    }


    /**
     * Test response when detaching a menu
     * Check the database
     * @depends testAttachingSecondMenu
     */
    public function testDetachingFirstMenu()
    {
        $count = $this->findNumberOfMenus(self::$firstId);

        $firstId = self::$firstId;
        $menu1Id = self::$menu1Id;
        $response = $this->deleteJson("/api/categories/{$firstId}/menus/{$menu1Id}");

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure(["data" => ["id", "deleted_at", "name"]]);

        // Check in database

        $category = Category::with("menus")->find(self::$firstId);

        $this->assertEquals(1, $category->menus->count());
        $this->assertEquals(self::$menu2Id, $category->menus[0]->id);

        $countAfter = $this->findNumberOfMenus(self::$firstId);
        $this->assertEquals($count - 1, $countAfter);
    }


    /**
     * Test response when valid deletion
     * @depends testDetachingFirstMenu
     */
    public function testDelete()
    {
        $count = $this->findNumberOfCategories();

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

        $countAfter = $this->findNumberOfCategories();
        self::assertEquals($count - 1, $countAfter);
    }


    /**
     * Test response when valid deletion
     * @depends testDelete
     */
    public function testDeleteWhenNotFound()
    {
        $count = $this->findNumberOfCategories();

        $id = self::$firstId;
        $response = $this->prepared->deleteJson("/api/categories/{$id}");

        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $response->assertJson(["data" => null]);

        $countAfter = $this->findNumberOfCategories();
        self::assertEquals($count, $countAfter);
    }
}
