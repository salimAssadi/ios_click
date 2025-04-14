<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ConsultingCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // First insert a country
        $countryId = DB::connection('crm')->table('countries')->insertGetId([
            'name_ar' => 'المملكة العربية السعودية',
            'name_en' => 'Saudi Arabia',
            'status' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        // Insert consulting company
        $companyId = DB::connection('crm')->table('consulting_companies')->insertGetId([
            'country_id' => $countryId,
            'name_ar' => 'شركة الاستشارات للجودة',
            'name_en' => 'Quality Consulting Company',
            'email' => 'info@qualityconsulting.com',
            'mobile' => '+966500000000',
            'contact_person' => 'Ahmed Mohammed',
            'is_default' => true,
            'status' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        // Create allocated space
        $allocatedSpaceId = DB::connection('crm')->table('allocated_spaces')->insertGetId([
            'name' => 'Basic Package Space',
            'size' => 5000, // 5GB in MB
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);

        // Insert subscription for the company
        DB::connection('crm')->table('customer_subscriptions')->insert([
            'customer_id' => $companyId,
            'subscription_start' => Carbon::now(),
            'subscription_end' => Carbon::now()->addYear(),
            'allocated_space_id' => $allocatedSpaceId,
            'total_users' => 10,
            'registered_users' => 0,
            'subscription_status' => 'ساري',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}
