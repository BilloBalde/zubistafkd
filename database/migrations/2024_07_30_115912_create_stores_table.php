<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string("store_name")->unique();
            $table->foreignId('place_id')->constrained('places')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->string('store_picture');
            $table->string('address');
            $table->string('phone');
            $table->text('description');
            $table->decimal('balance')->default(0);
            $table->timestamps();
        });
        DB::table('stores')->insert([
            [
                'store_name' => 'FBK',
                'place_id' => 1,
                'user_id'=>2,
                'store_picture'=>'ibra.png',
                'address'=>'',
                'phone'=>'',
                'description'=>'cette boutique est destinee a FBK store',
                 'balance'=>0
               
            ]
        ]);
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stores');
    }
};
