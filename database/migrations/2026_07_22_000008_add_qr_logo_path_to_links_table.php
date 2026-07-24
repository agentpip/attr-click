<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('links', function (Blueprint $table): void {
            $table->string('qr_logo_path')->nullable()->after('qr_background_color');
        });
    }

    public function down(): void
    {
        Schema::table('links', function (Blueprint $table): void {
            $table->dropColumn('qr_logo_path');
        });
    }
};
