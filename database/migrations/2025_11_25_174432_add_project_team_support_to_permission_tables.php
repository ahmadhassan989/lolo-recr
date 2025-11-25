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
        Schema::table('roles', function (Blueprint $table) {
            if (! Schema::hasColumn('roles', 'project_id')) {
                $table->unsignedBigInteger('project_id')->nullable()->after('guard_name');
                $table->index('project_id', 'roles_project_id_index');
            }
        });

        Schema::table('model_has_roles', function (Blueprint $table) {
            if (! Schema::hasColumn('model_has_roles', 'project_id')) {
                $table->dropForeign(['role_id']);
                $table->unsignedBigInteger('project_id')->nullable()->after('model_type');
                $table->index('project_id', 'model_has_roles_project_id_index');
                $table->dropPrimary();
                $table->primary(['role_id', 'model_id', 'model_type', 'project_id'], 'model_has_roles_role_model_type_project_primary');
                $table->foreign('role_id')
                    ->references('id')
                    ->on('roles')
                    ->onDelete('cascade');
            }
        });

        Schema::table('model_has_permissions', function (Blueprint $table) {
            if (! Schema::hasColumn('model_has_permissions', 'project_id')) {
                $table->dropForeign(['permission_id']);
                $table->unsignedBigInteger('project_id')->nullable()->after('model_type');
                $table->index('project_id', 'model_has_permissions_project_id_index');
                $table->dropPrimary();
                $table->primary(['permission_id', 'model_id', 'model_type', 'project_id'], 'model_has_permissions_permission_model_type_project_primary');
                $table->foreign('permission_id')
                    ->references('id')
                    ->on('permissions')
                    ->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            if (Schema::hasColumn('roles', 'project_id')) {
                $table->dropIndex('roles_project_id_index');
                $table->dropColumn('project_id');
            }
        });

        Schema::table('model_has_roles', function (Blueprint $table) {
            if (Schema::hasColumn('model_has_roles', 'project_id')) {
                $table->dropForeign(['role_id']);
                $table->dropPrimary('model_has_roles_role_model_type_project_primary');
                $table->dropIndex('model_has_roles_project_id_index');
                $table->dropColumn('project_id');
                $table->primary(['role_id', 'model_id', 'model_type'], 'model_has_roles_role_model_type_primary');
                $table->foreign('role_id')
                    ->references('id')
                    ->on('roles')
                    ->onDelete('cascade');
            }
        });

        Schema::table('model_has_permissions', function (Blueprint $table) {
            if (Schema::hasColumn('model_has_permissions', 'project_id')) {
                $table->dropForeign(['permission_id']);
                $table->dropPrimary('model_has_permissions_permission_model_type_project_primary');
                $table->dropIndex('model_has_permissions_project_id_index');
                $table->dropColumn('project_id');
                $table->primary(['permission_id', 'model_id', 'model_type'], 'model_has_permissions_permission_model_type_primary');
                $table->foreign('permission_id')
                    ->references('id')
                    ->on('permissions')
                    ->onDelete('cascade');
            }
        });
    }
};
