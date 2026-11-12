<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->string('kode', 10)->nullable()->unique()->after('username'); // PTO-001
            $t->time('shift_mulai')->default('08:00:00');
            $t->time('shift_selesai')->default('16:00:00');
            $t->boolean('aktif')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->dropUnique(['kode']);
            $t->dropColumn(['kode', 'shift_mulai', 'shift_selesai', 'aktif']);
        });
    }
};