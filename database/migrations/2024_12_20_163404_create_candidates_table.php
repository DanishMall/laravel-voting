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
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->string('student_id')->nullable();
            $table->string('name')->nullable();
            $table->string('faculty')->nullable();
            $table->string('position')->nullable();
            $table->text('vision')->nullable();
            $table->text('mission')->nullable();
            $table->integer('votes')->default(0);
            $table->decimal('vote_percentage', 5, 2)->default(0.00);
            $table->string('image_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('campaign_start_date')->nullable();
            $table->timestamp('campaign_end_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }
};
