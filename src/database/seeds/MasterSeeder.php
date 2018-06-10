<?php

namespace Solunes\Notification\Database\Seeds;

use Illuminate\Database\Seeder;
use DB;

class MasterSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // Módulo General de Empresa ERP
        $node_user_device = \Solunes\Master\App\Node::create(['name'=>'user-device', 'location'=>'notification', 'folder'=>'parameters']);

        // Usuarios
        $admin = \Solunes\Master\App\Role::where('name', 'admin')->first();
        $member = \Solunes\Master\App\Role::where('name', 'member')->first();
        if(!\Solunes\Master\App\Permission::where('name','notifications')->first()){
            $notifications_perm = \Solunes\Master\App\Permission::create(['name'=>'notifications', 'display_name'=>'Notificaciones']);
            $admin->permission_role()->attach([$notifications_perm->id]);
        }

    }
}