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
        Schema::table('evolutions', function (Blueprint $table) {
            $table->string('weight')->nullable()->after('current_illness');
            $table->string('height')->nullable()->after('weight');
            $table->string('fetal_heart_rate')->nullable()->after('height');
            $table->string('uterine_height')->nullable()->after('fetal_heart_rate');
            $table->text('physical_exam')->nullable()->after('vital_signs');
        });
    }
 
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evolutions', function (Blueprint $table) {
            $table->dropColumn(['weight', 'height', 'fetal_heart_rate', 'uterine_height', 'physical_exam']);
        });
    }
};
