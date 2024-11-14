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
        Schema::create('drugs', function (Blueprint $table) {
            $table->id();
            $table->string("kode_obat")->unique();
            $table->string("nama_obat");
            $table->text("image");
            $table->date("expired_date");
            $table->bigInteger("jumlah");
            $table->bigInteger("harga");
            $table->Integer("flag");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drugs');
    }
};