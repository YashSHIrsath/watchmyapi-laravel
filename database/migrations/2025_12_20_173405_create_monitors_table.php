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
        Schema::create('tbl_monitors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('tbl_companies')->onDelete('cascade');
            $table->string('name');
            $table->string('url');
            $table->string('status')->default('active');
            $table->timestamps();

            $table->index('company_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_monitors');
    }
};
