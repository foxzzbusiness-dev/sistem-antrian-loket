<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('layanan', function (Blueprint $t) {
            $t->id();
            $t->string('kode', 1);                     // A, B, C
            $t->string('nama');
            $t->string('deskripsi');
            $t->string('ikon')->default('file-text');  // nama ikon lucide
            $t->string('loket');                       // contoh: Loket 01–02
            $t->unsignedTinyInteger('rata_rata_menit')->default(5);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('layanan');
    }
};