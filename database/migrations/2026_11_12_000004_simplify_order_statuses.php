<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('orders')->whereIn('status', ['pending', 'awaiting_payment'])->update(['status' => 'pending_payment']);
        DB::table('orders')->whereIn('status', ['verified', 'processing'])->update(['status' => 'pending_payment']);

        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'verified_at') && ! Schema::hasColumn('orders', 'completed_at')) {
                $table->renameColumn('verified_at', 'completed_at');
            }
            $table->string('status', 32)->default('pending_payment')->change();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'completed_at') && ! Schema::hasColumn('orders', 'verified_at')) {
                $table->renameColumn('completed_at', 'verified_at');
            }
            $table->string('status', 32)->default('pending')->change();
        });

        DB::table('orders')->where('status', 'pending_payment')->update(['status' => 'pending']);
    }
};
