<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'address')) {
                $table->dropColumn('address');
            }

            $table->string('street_address')->nullable()->after('phone_number');
            $table->string('province')->nullable()->after('street_address');
            $table->string('city')->nullable()->after('province');
            $table->string('zip_code')->nullable()->after('city');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['street_address', 'province', 'city', 'zip_code']);
            $table->text('address')->nullable()->after('phone_number');
        });
    }
};
