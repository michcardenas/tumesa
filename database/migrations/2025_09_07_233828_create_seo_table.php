<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seo', function (Blueprint $table) {
            $table->id();
            $table->string('id_pagina')->index(); // 'experiencias', 'ser-chef', 'como-funciona'
            
            // Meta Tags básicos
            $table->string('meta_title', 60)->nullable();
            $table->string('meta_description', 160)->nullable();
            $table->string('meta_keywords')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('robots', 50)->default('index,follow');
            
            // Open Graph
            $table->string('og_title', 60)->nullable();
            $table->string('og_description', 160)->nullable();
            $table->string('og_image')->nullable();
            $table->string('og_type', 20)->default('website');
            
            // Twitter Cards
            $table->string('twitter_title', 60)->nullable();
            $table->string('twitter_description', 160)->nullable();
            $table->string('twitter_image')->nullable();
            
            // SEO adicional
            $table->string('focus_keyword')->nullable();
            $table->text('schema_markup')->nullable(); // JSON-LD
            
            $table->timestamps();
            
            // Único por página
            $table->unique('id_pagina');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seo');
    }
};