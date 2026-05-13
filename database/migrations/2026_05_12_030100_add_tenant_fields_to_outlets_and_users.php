<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('outlets', function (Blueprint $table) {
            $table->foreignUuid('tenant_id')->nullable()->after('id')->constrained('tenants')->nullOnDelete();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignUuid('tenant_id')->nullable()->after('id')->constrained('tenants')->nullOnDelete();
            $table->string('user_type')->default('staff')->after('role');
            $table->foreignUuid('outlet_id')->nullable()->change();
        });

        $tenantId = (string) Str::uuid();
        DB::table('tenants')->insert([
            'id' => $tenantId,
            'name' => 'Default Laundry Tenant',
            'slug' => 'default-laundry-tenant',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('outlets')->update([
            'tenant_id' => $tenantId,
        ]);

        DB::table('users')
            ->where('role', 'SUPER_ADMIN')
            ->update([
                'tenant_id' => null,
                'user_type' => 'super_admin',
                'outlet_id' => null,
            ]);

        DB::table('users')
            ->where('role', 'ADMIN')
            ->update([
                'tenant_id' => $tenantId,
                'user_type' => 'owner',
            ]);

        DB::table('users')
            ->where('role', 'KASIR')
            ->update([
                'tenant_id' => $tenantId,
                'user_type' => 'staff',
            ]);

        $ownerId = DB::table('users')
            ->where('role', 'ADMIN')
            ->value('id');

        if ($ownerId) {
            DB::table('tenants')
                ->where('id', $tenantId)
                ->update([
                    'owner_user_id' => $ownerId,
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tenant_id');
            $table->dropColumn('user_type');
        });

        Schema::table('outlets', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tenant_id');
        });
    }
};
