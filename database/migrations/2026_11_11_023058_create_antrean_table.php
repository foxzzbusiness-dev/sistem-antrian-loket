<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('antrean', function (Blueprint $t) {
            $t->id();
            $t->foreignId('layanan_id')->constrained('layanan')->cascadeOnDelete();
            $t->unsignedInteger('urutan');
            $t->string('nomor', 8);                         // A-025
            $t->string('loket')->nullable();
            $t->enum('status', ['menunggu', 'dilayani', 'selesai', 'batal'])->default('menunggu');
            $t->timestamp('diterbitkan_at');
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('antrean');
    }
};