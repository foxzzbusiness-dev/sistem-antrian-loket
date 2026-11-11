<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->string('username')->nullable()->unique()->after('name');
            $t->string('role')->default('petugas')->after('password'); // petugas | admin
            $t->string('loket')->nullable()->after('role');            // contoh: Loket 01
        });

        Schema::table('antrean', function (Blueprint $t) {
            $t->timestamp('dimulai_at')->nullable();
            $t->timestamp('selesai_at')->nullable();
            $t->timestamp('dipanggil_at')->nullable(); // dipakai display loket nanti
        });

        Schema::create('loket', function (Blueprint $t) {
            $t->id();
            $t->string('nama');                                              // Loket 01
            $t->enum('status', ['aktif', 'istirahat', 'tutup'])->default('aktif');
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loket');

        Schema::table('antrean', function (Blueprint $t) {
            $t->dropColumn(['dimulai_at', 'selesai_at', 'dipanggil_at']);
        });

        Schema::table('users', function (Blueprint $t) {
            $t->dropUnique(['username']);
            $t->dropColumn(['username', 'role', 'loket']);
        });
    }
};