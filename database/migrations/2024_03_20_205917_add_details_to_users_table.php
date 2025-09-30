<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('cpf')->after('email');
            $table->string('telephone')->after('cpf');
            $table->softDeletes();
            $table->foreignId('created_by')->nullable()->constrained('users')->after('softDeletes');
            $table->foreignId('updated_by')->nullable()->constrained('users')->after('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropColumn('cpf');
            $table->dropColumn('telephone');
            $table->dropColumn('client_id');
            $table->dropColumn('created_by');
            $table->dropColumn('updated_by');
            $table->dropSoftDeletes();
        });
    }
};
