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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("no")->unique();
            $table->dateTime("date")->useCurrent()->useUtc();
            $table->bigInteger("total_bayar");
            $table->integer("id_user");
            $table->integer("id_drug");
            $table->integer("id_recipe");
            $table->integer("flag");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};