<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->foreignUuid('tenant_id')->nullable()->after('id')->constrained('tenants')->nullOnDelete();
        });

        try {
            Schema::table('roles', function (Blueprint $table) {
                $table->dropUnique('roles_name_guard_name_unique');
            });
        } catch (\Throwable) {
            // ignore if index name differs
        }

        Schema::table('roles', function (Blueprint $table) {
            $table->index(['tenant_id', 'name']);
        });

        $defaultTenantId = DB::table('tenants')->where('slug', 'default-laundry-tenant')->value('id');

        if ($defaultTenantId) {
            DB::table('roles')
                ->whereIn('name', ['Owner', 'Kasir', 'Manager', 'Operator'])
                ->update(['tenant_id' => $defaultTenantId]);
        }
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropIndex(['tenant_id', 'name']);
            $table->dropConstrainedForeignId('tenant_id');
        });
    }
};
