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
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->string("no")->unique();
            $table->date("date");
            $table->string("nama_dokter");
            $table->string("nama_pasien");
            $table->integer("id_obat");
            $table->integer("jumlah_obat");
            $table->integer("flag");
            $table->boolean("transaksi")->default(false); // Nilai default diatur sebagai false
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};