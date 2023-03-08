<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('affiliates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('merchant_id');
            // TODO: Replace me with a brief explanation of why floats aren't the correct data type, and replace with the correct data type.
           
            /*
            The float data type is not always the best choice for storing decimal values because
            it can lead to rounding errors due to the way floating point numbers are represented in binary.
            Therefore, it's recommended to use the decimal data type instead when working with decimal values
            that require high precision.
            In the given code, it's better to replace
            $table->float('commission_rate'); with $table->decimal('commission_rate', 8, 2);
            where 8 is the total number of digits and 2 is the number of digits after the decimal point. 
            This will ensure that the commission_rate column stores decimal values with a fixed precision of
            2 digits after the decimal point.*/

            // $table->float('commission_rate');
            $table->decimal('commission_rate', 8, 2);
            
            $table->string('discount_code');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('affiliates');
    }
};
