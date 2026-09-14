<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employers', function (Blueprint $table): void {
            $table->foreignId('company_id')
                ->nullable()
                ->after('user_id')
                ->constrained()
                ->nullOnDelete();
        });

            DB::statement('UPDATE employers SET company_id = (SELECT id FROM companies WHERE companies.user_id = employers.user_id) WHERE company_id IS NULL');
    }

    public function down(): void
    {
        Schema::table('employers', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('company_id');
        });
    }
};
