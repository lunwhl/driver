<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExampleTest extends TestCase
{
	use DatabaseMigrations;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $this->assertTrue(true);
    }

    /** @test */
    public function users_are_able_to_update_profile(){
      $user = $this->signIn();
      $user1 = factory('App\User')->create();
      $user2 = factory('App\User')->create();
             
      $response = $this->post( '/profile', $user2->toArray());      
      $this->assertDatabaseHas("users", ["lname"=>$user2->lname, "fname"=>$user2->fname, "license_plate"=>$user2->license_plate, "id" => auth()->id()]);  
    }

    /** @test */
    public function guests_are_unable_to_update_profile(){
    	$user = $this->withExceptionHandling();
      $user1 = factory('App\User')->create();
      $user2 = factory('App\User')->create();
             
      $response = $this->post( '/profile', $user2->toArray());      
      $response->assertSee('login');  
    }

    /** @test */
    // public function test_chef_can_modify_selected_food(){
    //   //$this = mean in this class
    //   //$this->signIn() = in this class find sign in function
    //   //response = send thing to controller and controller send back

    //   $user = $this->signIn();
    //   $food1 = factory('App\Food')->create();
    //   $food2 = factory('App\Food')->create();
    //   $food3 = factory('App\Food')->make(["name"=>$food2->name]);
    //   $response = $this->put( '/food/edit/'.$food1->id , $food3->toArray());  
       
    //    //, (comma)= and so below is id same and name=wever and ingredient = wha2
    //    $this->assertDatabaseHas("foods", ["id"=>$food1->id, "name"=>$food3->name, 'ingredients' => $food3->ingredients]);

    // }
}
