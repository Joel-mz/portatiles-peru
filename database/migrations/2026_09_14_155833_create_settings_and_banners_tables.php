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
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('NEXORA');
            $table->string('tagline')->default('Tecnología sin límites');
            $table->string('legal_name')->default('NEXORA TECHNOLOGY S.A.C.');
            $table->string('ruc')->default('20681254567');
            $table->string('email')->default('ventas@nexora.com');
            $table->string('phone')->default('+51 987 654 321');
            $table->string('whatsapp_number')->default('+51987654321');
            $table->text('whatsapp_default_message')->nullable();
            $table->string('address')->default('Av. Los Héroes 123, Moyobamba, San Martín, Perú');
            $table->string('schedule_weekdays')->default('Lun - Sáb: 8:00 a.m. - 7:00 p.m.');
            $table->string('schedule_weekends')->default('Dom: 9:00 a.m. - 2:00 p.m.');
            $table->string('yape_number')->default('987 654 321');
            $table->string('yape_holder')->default('NEXORA TECHNOLOGY S.A.C.');
            $table->string('yape_qr')->nullable();
            $table->string('plin_number')->default('987 654 321');
            $table->string('plin_holder')->default('NEXORA TECHNOLOGY S.A.C.');
            $table->string('plin_qr')->nullable();
            $table->string('bank_name')->default('Banco de Crédito del Perú (BCP)');
            $table->string('bank_account')->default('191-12345678-0-72');
            $table->string('bank_cci')->default('002-191-0012345678072-55');
            $table->string('bank_holder')->default('NEXORA TECHNOLOGY S.A.C.');
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->string('facebook_url')->nullable()->default('https://facebook.com/nexoratech');
            $table->string('instagram_url')->nullable()->default('https://instagram.com/nexora');
            $table->string('tiktok_url')->nullable()->default('https://tiktok.com/@nexora');
            $table->string('youtube_url')->nullable()->default('https://youtube.com/@nexora');
            $table->boolean('enable_whatsapp_button')->default(true);
            $table->boolean('enable_dark_mode')->default(true);
            $table->timestamps();
        });

        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('badge')->nullable();
            $table->string('image');
            $table->string('link_url')->nullable();
            $table->string('button_text')->nullable()->default('Ver productos');
            $table->string('type')->default('hero'); // hero, promo_card, combo_card
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'role')) {
                $table->string('role')->default('admin')->after('email'); // superadmin, admin, editor, seller
            }
            if (! Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('role');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'phone')) {
                $table->dropColumn('phone');
            }
            if (Schema::hasColumn('users', 'role')) {
                $table->dropColumn('role');
            }
        });
        Schema::dropIfExists('banners');
        Schema::dropIfExists('company_settings');
    }
};
