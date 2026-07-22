<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qr_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name', 80);
            $table->string('foreground_color', 7);
            $table->string('background_color', 7);
            $table->timestamps();
            $table->unique(['user_id', 'name']);
        });

        Schema::table('links', function (Blueprint $table) {
            $table->foreignId('qr_template_id')->nullable()->after('user_id')->constrained('qr_templates')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('links', function (Blueprint $table) {
            $table->dropConstrainedForeignId('qr_template_id');
        });

        Schema::dropIfExists('qr_templates');
    }
};
