<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class);
            $table->string('country_name');
            $table->string('country_code');
            $table->string('state_name');
            $table->string('state_code')->nullable();
            $table->string('city');
            $table->string('locality');
            $table->text('address'); // with number
            $table->string('zip');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
