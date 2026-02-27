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
        if (!Schema::hasTable('payments') || Schema::hasColumn('payments', 'snap_token')) {
            return;
        }

        Schema::table('payments', function (Blueprint $table) {
            $table->text('snap_token')->nullable()->after('payment_gateway_ref');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('payments') || !Schema::hasColumn('payments', 'snap_token')) {
            return;
        }

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('snap_token');
        });
    }
};
