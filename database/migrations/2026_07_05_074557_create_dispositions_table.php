<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dispositions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->constrained('complaints')->cascadeOnDelete();
            // BR-01/BR-02: application-level enforcement (see
            // App\Domain\Complaint\Rules\DispositionMustTargetOpdOrCamatRule)
            // guarantees this is always 'opd' or 'camat'.
            $table->string('disposed_to_type');
            $table->unsignedBigInteger('disposed_to_id');
            $table->foreignId('disposed_by')->constrained('users')->cascadeOnDelete();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['disposed_to_type', 'disposed_to_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispositions');
    }
};
