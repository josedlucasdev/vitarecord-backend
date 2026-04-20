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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('name');
            $table->integer('age');
            $table->string('identity_card')->unique();
            $table->string('occupation')->nullable();
            $table->string('phone')->nullable();
            $table->timestamps();
        });

        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('from_doctor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('to_doctor_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->timestamps();
        });

        // 1. Historia Base (Fija)
        Schema::create('base_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            
            // Antecedentes Gineco-Obstétricos
            $table->integer('menarche')->nullable(); // Menarquia
            $table->string('menstrual_cycle')->nullable(); // Ciclo Menstrual
            $table->date('last_menstruation_date')->nullable(); // FUM (FUM)
            $table->integer('sexarche')->nullable(); // Sexarquia
            $table->integer('sexual_partners')->nullable(); // Nro Parejas
            $table->string('contraceptive_method')->nullable(); // MAC
            $table->string('pregnancies_gpcav')->nullable(); // Gestas: G, P, C, A, V (ej: G:2 P:1 C:1 A:0 V:2)
            $table->string('last_pap_smear')->nullable(); // Última Citología
            $table->string('last_mammography')->nullable(); // Última Mamografía
            
            // Antecedentes Personales y Familiares
            $table->text('personal_pathological')->nullable(); // Patológicos
            $table->text('surgical')->nullable(); // Quirúrgicos
            $table->text('allergies')->nullable(); // Alergias
            $table->text('habits')->nullable(); // Hábitos
            $table->text('family_history')->nullable(); // Familiares

            $table->timestamps();
        });

        // 2. Evoluciones (Consultas)
        Schema::create('evolutions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained('users')->onDelete('cascade');
            
            $table->date('date');
            $table->string('reason_for_consultation'); // Motivo de Consulta
            $table->text('current_illness')->nullable(); // Enfermedad Actual

            // Examen Físico
            $table->text('vital_signs')->nullable(); // Signos Vitales
            $table->text('breast_exam')->nullable(); // Exploración Mamaria
            $table->text('abdominal_exam')->nullable(); // Exploración Abdominal
            $table->text('gynecological_exam_external')->nullable(); // Genitales Externos
            $table->text('gynecological_exam_speculum')->nullable(); // Especuloscopia
            $table->text('gynecological_exam_bimanual')->nullable(); // Tacto Bimanual

            // Diagnóstico
            $table->text('diagnostic_impression')->nullable(); // Impresión Diagnóstica
            
            $table->timestamps();
        });

        // 3. Modulo Lego: Exámenes y Ecografías
        Schema::create('exams_ultrasounds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('evolution_id')->nullable()->constrained()->onDelete('set null');
            
            $table->date('date');
            $table->string('exam_type')->default('ultrasound');
            $table->text('findings_uterus')->nullable(); // Útero
            $table->text('findings_ovaries')->nullable(); // Ovarios
            $table->text('findings_cul_de_sac')->nullable(); // Fondo de Saco
            $table->text('general_findings')->nullable(); // Hallazgos generales para laboratorios etc.
            
            $table->string('attachment_path')->nullable(); // Ruta de PDF o imagen subida
            $table->timestamps();
        });

        // 4. Modulo Lego: Recetas y Plan de Tratamiento
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('evolution_id')->nullable()->constrained()->onDelete('set null');
            
            $table->date('date');
            $table->text('medications'); // Medicamentos o Plan y Tratamiento general
            $table->text('indications')->nullable(); // Indicaciones de uso
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
        Schema::dropIfExists('exams_ultrasounds');
        Schema::dropIfExists('evolutions');
        Schema::dropIfExists('base_histories');
        Schema::dropIfExists('referrals');
        Schema::dropIfExists('patients');
    }
};
