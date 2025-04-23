<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyProfilesTable extends Migration
{
    public function up()
    {
        if(!Schema::hasTable('company_profiles')){
            Schema::create('company_profiles', function (Blueprint $table) {
                $table->id();
                $table->string('company_name');
                $table->string('logo')->nullable();
                $table->string('email');
            $table->string('phone');
            $table->string('address');
            $table->string('city');
            $table->string('country');
            $table->string('postal_code')->nullable();
            $table->text('description')->nullable();
            $table->string('website')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('registration_number')->nullable();
            $table->unsignedBigInteger('tenant_id')->nullable();
            $table->timestamps();
        });
        }
    }

    public function down()
    {
        if(Schema::hasTable('company_profiles')){
            Schema::dropIfExists('company_profiles');
        }
    }
}
