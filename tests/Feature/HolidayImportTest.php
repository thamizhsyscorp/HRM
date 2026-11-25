<?php

namespace Tests\Feature;

use App\Models\Holiday;
use App\Models\User;
use Database\Seeders\HrmDatabaseSeeder;
use Database\Seeders\SampleUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class HolidayImportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(HrmDatabaseSeeder::class);
        $this->seed(SampleUserSeeder::class);
    }

    /**
     * Admin can download the sample holiday CSV file.
     */
    public function test_admin_can_download_sample_holiday_csv(): void
    {
        $admin = User::where('email', 'admin@hrm.test')->first();

        $response = $this->actingAs($admin)->get(route('holidays.sample'));

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', $response->headers->get('content-type'));
        $this->assertStringContainsString('filename=sample_holidays.csv', $response->headers->get('content-disposition'));
    }

    /**
     * Regular employee cannot download sample holiday CSV or import holidays.
     */
    public function test_employee_cannot_import_or_download_sample(): void
    {
        $employee = User::where('email', 'employee@hrm.test')->first();

        $response = $this->actingAs($employee)->get(route('holidays.index'));
        $response->assertStatus(403);

        $importResponse = $this->actingAs($employee)->post(route('holidays.import'), []);
        $importResponse->assertStatus(403);
    }

    /**
     * Admin can successfully import holidays from CSV file.
     */
    public function test_admin_can_import_holidays_from_csv(): void
    {
        $admin = User::where('email', 'admin@hrm.test')->first();

        $csvContent = <<<CSV
name,holiday_date,type,is_optional,description
Pongal Festival,2026-01-14,FESTIVAL,0,Harvest festival celebration
Republic Day,2026-01-26,PUBLIC,0,National Republic Day
Custom Floating Leave,2026-04-10,OPTIONAL,1,Floating cultural holiday
CSV;

        $file = UploadedFile::fake()->createWithContent('holidays.csv', $csvContent);

        $response = $this->actingAs($admin)->post(route('holidays.import'), [
            'file' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $pongal = Holiday::where('name', 'Pongal Festival')->first();
        $this->assertNotNull($pongal);
        $this->assertEquals('2026-01-14', $pongal->holiday_date->format('Y-m-d'));
        $this->assertEquals('FESTIVAL', $pongal->type);
        $this->assertFalse($pongal->is_optional);

        $republic = Holiday::where('name', 'Republic Day')->first();
        $this->assertNotNull($republic);
        $this->assertEquals('2026-01-26', $republic->holiday_date->format('Y-m-d'));
        $this->assertEquals('PUBLIC', $republic->type);

        $floating = Holiday::where('name', 'Custom Floating Leave')->first();
        $this->assertNotNull($floating);
        $this->assertEquals('2026-04-10', $floating->holiday_date->format('Y-m-d'));
        $this->assertEquals('OPTIONAL', $floating->type);
        $this->assertTrue($floating->is_optional);
    }

    /**
     * Importing updates existing holidays if the date already exists.
     */
    public function test_import_updates_existing_holiday_on_same_date(): void
    {
        $admin = User::where('email', 'admin@hrm.test')->first();

        Holiday::create([
            'name' => 'Old Custom Name',
            'holiday_date' => '2026-09-20',
            'type' => 'PUBLIC',
            'is_optional' => false,
            'description' => 'Old description',
        ]);

        $csvContent = <<<CSV
name,holiday_date,type,is_optional,description
Updated Holiday Name,2026-09-20,PUBLIC,0,Updated description
CSV;

        $file = UploadedFile::fake()->createWithContent('update.csv', $csvContent);

        $response = $this->actingAs($admin)->post(route('holidays.import'), [
            'file' => $file,
        ]);

        $response->assertRedirect();
        $updated = Holiday::where('name', 'Updated Holiday Name')->first();
        $this->assertNotNull($updated);
        $this->assertEquals('2026-09-20', $updated->holiday_date->format('Y-m-d'));
        $this->assertEquals('Updated description', $updated->description);
    }
}
