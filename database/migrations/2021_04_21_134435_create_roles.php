<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Role;

class CreateRoles extends Migration
{
    /**
     * Роли пользователей:
     * - админ - admin
     * - производство - production
     * - сервисник - service technician
     * - монтажник - fitter
     * - техподдержка - support
     * - менеджер от нас - manager
     * - предприятие - client
     * - разработчик - developer
     * - наш работник - employee
     */
    private static $names = [
        'admin',
        'client',
        'production',
        'service technician',
        'fitter',
        'support',
        'manager',
        'developer',
        'employee',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (self::$names as $name) {
            Role::firstOrCreate(['name' => $name]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     * @throws Exception
     */
    public function down()
    {
        foreach (self::$names as $name) {
            Role::findByName($name)->delete();
        }
    }
}
