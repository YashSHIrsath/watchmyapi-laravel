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
        // Step 1: Add project_id (nullable initially)
        Schema::table('tbl_monitors', function (Blueprint $table) {
            $table->foreignId('project_id')->nullable()->after('company_id')->constrained('tbl_projects')->onDelete('cascade');
        });

        // Step 2: Create a Default Project for every existing company
        $companies = Illuminate\Support\Facades\DB::table('tbl_companies')->get();
        foreach ($companies as $company) {
            $projectId = Illuminate\Support\Facades\DB::table('tbl_projects')->insertGetId([
                'company_id' => $company->id,
                'name' => 'Default Project',
                'is_default' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Step 3: Migrate all existing monitors for this company to the new Default Project
            Illuminate\Support\Facades\DB::table('tbl_monitors')
                ->where('company_id', $company->id)
                ->update(['project_id' => $projectId]);
        }

        // Step 4: Make project_id non-nullable
        Schema::table('tbl_monitors', function (Blueprint $table) {
            $table->foreignId('project_id')->nullable(false)->change();
        });

        // Step 5: Drop company_id from tbl_monitors
        Schema::table('tbl_monitors', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_monitors', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('id')->constrained('tbl_companies')->onDelete('cascade');
        });

        // Restore company_id from projects
        $monitors = Illuminate\Support\Facades\DB::table('tbl_monitors')->get();
        foreach ($monitors as $monitor) {
            $project = Illuminate\Support\Facades\DB::table('tbl_projects')->where('id', $monitor->project_id)->first();
            if ($project) {
                Illuminate\Support\Facades\DB::table('tbl_monitors')
                    ->where('id', $monitor->id)
                    ->update(['company_id' => $project->company_id]);
            }
        }

        Schema::table('tbl_monitors', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable(false)->change();
            $table->dropForeign(['project_id']);
            $table->dropColumn('project_id');
        });

        Illuminate\Support\Facades\DB::table('tbl_projects')->where('is_default', true)->delete();
    }
};
