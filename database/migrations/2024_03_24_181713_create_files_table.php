<?php

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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->morphs('fileable');
            $table->string('type');
            $table->text('thumbnail_url');
            $table->text('preview_url');
            $table->text('filename');
            $table->string('mime_type');
            $table->integer('size');
            $table->integer('width');
            $table->integer('height');
            $table->smallInteger('dpi')->nullable();
            $table->unsignedBigInteger('printful_file_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
