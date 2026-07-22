<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('links', function (Blueprint $table) {
            $table->string('qr_foreground_color', 7)->default('#111827')->after('stored_query');
            $table->string('qr_background_color', 7)->default('#ffffff')->after('qr_foreground_color');
        });
    }

    public function down(): void
    {
        Schema::table('links', function (Blueprint $table) {
            $table->dropColumn(['qr_foreground_color', 'qr_background_color']);
        });
    }
};
