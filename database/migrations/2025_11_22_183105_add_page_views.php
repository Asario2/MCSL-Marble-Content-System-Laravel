<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

        //
public function up(): void
{
    Schema::connection('gnerals')->create('page_views', function (Blueprint $table) {
        $table->id();
        $table->string('url');
        $table->string('referrer')->nullable();
        $table->string('ip', 45);
        $table->timestamp('visited_at');
    });
}

public function down(): void
{
    Schema::connection('gnerals')->dropIfExists('page_views');

}

};
