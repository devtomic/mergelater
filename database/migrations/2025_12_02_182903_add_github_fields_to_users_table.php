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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('github_id')->unique()->nullable()->after('id');
            $table->text('github_token')->nullable()->after('email');
            $table->string('avatar_url')->nullable()->after('github_token');
            $table->string('timezone')->nullable()->after('avatar_url');
            $table->timestamp('onboarding_completed_at')->nullable()->after('timezone');
            $table->boolean('email_notifications')->default(true)->after('onboarding_completed_at');
            $table->string('slack_webhook_url')->nullable()->after('email_notifications');
            $table->boolean('is_admin')->default(false)->after('slack_webhook_url');
            $table->boolean('is_disabled')->default(false)->after('is_admin');

            // Make password nullable for OAuth users
            $table->string('password')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'github_id',
                'github_token',
                'avatar_url',
                'timezone',
                'onboarding_completed_at',
                'email_notifications',
                'slack_webhook_url',
                'is_admin',
                'is_disabled',
            ]);
        });
    }
};
