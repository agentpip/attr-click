<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('links', function (Blueprint $table): void {
            $table->timestamp('qr_regenerated_at')->nullable()->after('qr_logo_path');
        });
    }

    public function down(): void
    {
        Schema::table('links', function (Blueprint $table): void {
            $table->dropColumn('qr_regenerated_at');
        });
    }
};
