
<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        App\User::create([
            'name' => 'Research and Development',
            'email' => 'rnd@dctechmicro.com',
            'password' => bcrypt('mweakthegenius'),
            'remember_token' => str_random(10),
        ]);

        App\Role::create(['name' => 'create_account']);
        App\Role::create(['name' => 'update_account']);
        App\Role::create(['name' => 'create_client']);
        App\Role::create(['name' => 'update_client']);
        App\Role::create(['name' => 'create_warehouse']);
        App\Role::create(['name' => 'update_warehouse']);
        App\Role::create(['name' => 'create_item']);
        App\Role::create(['name' => 'update_item']);
        App\Role::create(['name' => 'delete_item']);
        App\Role::create(['name' => 'create_category']);
        App\Role::create(['name' => 'update_category']);
        App\Role::create(['name' => 'delete_category']);
        App\Role::create(['name' => 'create_sales_order']);
        App\Role::create(['name' => 'update_sales_order']);
        App\Role::create(['name' => 'approved_sales_order']);
        App\Role::create(['name' => 'create_delivery_receipt']);
        App\Role::create(['name' => 'create_purchase_order']);
        App\Role::create(['name' => 'update_purchase_order']);
        App\Role::create(['name' => 'approved_purchase_order']);
        App\Role::create(['name' => 'create_sales_return']);
        App\Role::create(['name' => 'create_supplier']);
        App\Role::create(['name' => 'update_supplier']);
        App\Role::create(['name' => 'create_company_assets']);
        App\Role::create(['name' => 'update_company_assets']);
        App\Role::create(['name' => 'delete_company_assets']);
        App\Role::create(['name' => 'create_item_receipt']);
        App\Role::create(['name' => 'view_aacountability']);
        App\Role::create(['name' => 'create_aacountability']);
        App\Role::create(['name' => 'update_aacountability']);
        App\Role::create(['name' => 'delete_aacountability']);


        DB::table('role_user')->insert([
            ['user_id' => 1, 'role_id' => 1],
            ['user_id' => 1, 'role_id' => 2],
            ['user_id' => 1, 'role_id' => 3],
            ['user_id' => 1, 'role_id' => 4],
            ['user_id' => 1, 'role_id' => 5],
            ['user_id' => 1, 'role_id' => 6],
            ['user_id' => 1, 'role_id' => 7],
            ['user_id' => 1, 'role_id' => 8],
            ['user_id' => 1, 'role_id' => 9],
            ['user_id' => 1, 'role_id' => 10],
            ['user_id' => 1, 'role_id' => 11],
            ['user_id' => 1, 'role_id' => 12],
            ['user_id' => 1, 'role_id' => 13],
            ['user_id' => 1, 'role_id' => 14],
            ['user_id' => 1, 'role_id' => 15],
            ['user_id' => 1, 'role_id' => 16],
            ['user_id' => 1, 'role_id' => 17],
            ['user_id' => 1, 'role_id' => 18],
            ['user_id' => 1, 'role_id' => 19],
            ['user_id' => 1, 'role_id' => 20],
            ['user_id' => 1, 'role_id' => 21],
            ['user_id' => 1, 'role_id' => 22],
            ['user_id' => 1, 'role_id' => 23],
            ['user_id' => 1, 'role_id' => 24],
            ['user_id' => 1, 'role_id' => 25],
            ['user_id' => 1, 'role_id' => 26]
        ]);

        App\Type::create([
            'name' => 'Consumable(sfp, ballpen. paper, etc)',
        ]);

        App\Type::create([
            'name' => 'Serialize(modem, printer, server, etc)',
        ]);

        App\Warehouse::create([
            'name' => 'Main Office',
            'address' => 'Dctech Building, Ponciano Reyes Street, Davao City'
        ]);

        App\Warehouse::create([
            'name' => 'Ma-a Warehouse'
        ]);

        App\Warehouse::create([
            'name' => 'Townhouse'
        ]);

        App\Locale::create([
            'name' => 'Local'
        ]);

        App\Locale::create([
            'name' => 'Manila'
        ]);

        App\Locale::create([
            'name' => 'China'
        ]);

        App\Locale::create([
            'name' => 'US'
        ]);
    }
}
