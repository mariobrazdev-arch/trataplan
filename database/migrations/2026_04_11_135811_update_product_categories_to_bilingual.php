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
        Schema::table('product_categories', function (Blueprint $table) {
            $table->renameColumn('name', 'name_pt');
            $table->renameColumn('description', 'description_pt');
        });

        Schema::table('product_categories', function (Blueprint $table) {
            $table->string('name_en', 150)->nullable()->after('name_pt');
            $table->text('description_en')->nullable()->after('description_pt');
        });
    }

    public function down(): void
    {
        Schema::table('product_categories', function (Blueprint $table) {
            $table->dropColumn(['name_en', 'description_en']);
        });

        Schema::table('product_categories', function (Blueprint $table) {
            $table->renameColumn('name_pt', 'name');
            $table->renameColumn('description_pt', 'description');
        });
    }
};
