<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('process_step')->nullable()->after('status');
        });

        DB::table('transactions')
            ->where('transaction_type', 'DROP_OFF')
            ->whereNull('process_step')
            ->update([
                'process_step' => DB::raw("
                    CASE
                        WHEN status = 'COMPLETED' THEN 'PICKED_UP'
                        WHEN status = 'READY' THEN 'READY'
                        ELSE 'RECEIVED'
                    END
                "),
            ]);
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('process_step');
        });
    }
};
