<?php

namespace Tests\Feature;

use App\Models\Milestone;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProjectMonitoringTest extends TestCase
{
    use DatabaseTransactions;

    protected User $admin;

    protected User $member;

    protected Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::where('role', 'admin')->first() ?? User::factory()->create(['role' => 'admin']);
        $this->member = User::where('role', 'member')->first() ?? User::factory()->create(['role' => 'member']);
        $this->project = Project::first() ?? Project::create([
            'code' => 'PRJ-TEST-001',
            'name' => 'Proyek Uji Coba Monitoring',
            'client' => 'Klien Pengujian',
            'manager_id' => $this->admin->id,
            'start_date' => now(),
            'deadline' => now()->addMonth(),
            'budget' => 100000000,
            'priority' => 'high',
            'status' => 'in_progress',
            'progress' => 50,
        ]);
    }

    public function test_dashboard_renders_successfully(): void
    {
        $response = $this->actingAs($this->admin)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Selamat datang kembali');
        $response->assertSee('Total Proyek');
        $response->assertSee('Proyek Aktif');
    }

    public function test_project_index_renders_with_filters(): void
    {
        $response = $this->actingAs($this->admin)->get('/projects?status=in_progress');

        $response->assertStatus(200);
        $response->assertSee('Manajemen Proyek');
    }

    public function test_admin_can_create_project(): void
    {
        $uniqueCode = 'PRJ-TEST-'.rand(1000, 9999);

        $response = $this->actingAs($this->admin)->post('/projects', [
            'code' => $uniqueCode,
            'name' => 'Sistem Otomasi Pelayanan Publik Terpadu',
            'client' => 'Bappeda Litbang',
            'manager_id' => $this->admin->id,
            'start_date' => now()->toDateString(),
            'deadline' => now()->addDays(30)->toDateString(),
            'budget' => 250000000,
            'priority' => 'high',
            'status' => 'in_progress',
            'progress' => 10,
            'description' => 'Deskripsi proyek uji coba.',
        ]);

        $this->assertDatabaseHas('projects', ['code' => $uniqueCode]);
        $project = Project::where('code', $uniqueCode)->first();
        $response->assertRedirect(route('projects.show', $project));
    }

    public function test_project_show_page_renders_with_tabs(): void
    {
        $response = $this->actingAs($this->admin)->get("/projects/{$this->project->id}?tab=overview");
        $response->assertStatus(200);
        $response->assertSee($this->project->name);

        $responseTasks = $this->actingAs($this->admin)->get("/projects/{$this->project->id}?tab=tasks");
        $responseTasks->assertStatus(200);

        $responseMilestones = $this->actingAs($this->admin)->get("/projects/{$this->project->id}?tab=milestones");
        $responseMilestones->assertStatus(200);
    }

    public function test_task_creation_and_status_update(): void
    {
        $response = $this->actingAs($this->admin)->post("/projects/{$this->project->id}/tasks", [
            'title' => 'Task Uji Coba Implementasi API',
            'description' => 'Deskripsi task pengujian',
            'priority' => 'critical',
            'status' => 'todo',
            'start_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'assigned_to' => $this->member->id,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('tasks', [
            'project_id' => $this->project->id,
            'title' => 'Task Uji Coba Implementasi API',
        ]);

        $task = Task::where('title', 'Task Uji Coba Implementasi API')->first();

        // Update Status
        $statusResponse = $this->actingAs($this->member)->patch("/tasks/{$task->id}/status", [
            'status' => 'done',
        ]);

        $statusResponse->assertSessionHas('success');
        $this->assertEquals('done', $task->fresh()->status);
        $this->assertNotNull($task->fresh()->completed_at);
    }

    public function test_milestone_creation_and_toggle(): void
    {
        $response = $this->actingAs($this->admin)->post("/projects/{$this->project->id}/milestones", [
            'name' => 'Milestone Uji Coba Peluncuran Alpha',
            'description' => 'Target pengujian alpha internal',
            'target_date' => now()->addDays(14)->toDateString(),
            'status' => 'pending',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('milestones', [
            'name' => 'Milestone Uji Coba Peluncuran Alpha',
        ]);

        $milestone = Milestone::where('name', 'Milestone Uji Coba Peluncuran Alpha')->first();

        // Toggle
        $toggleResponse = $this->actingAs($this->admin)->patch("/milestones/{$milestone->id}/toggle");
        $toggleResponse->assertSessionHas('success');
        $this->assertEquals('completed', $milestone->fresh()->status);
    }

    public function test_reports_page_and_csv_export(): void
    {
        $response = $this->actingAs($this->admin)->get('/reports');
        $response->assertStatus(200);
        $response->assertSee('Laporan');

        $csvResponse = $this->actingAs($this->admin)->get('/reports/export');
        $csvResponse->assertStatus(200);
        $this->assertStringContainsString('text/csv', (string) $csvResponse->headers->get('Content-Type'));
    }

    public function test_member_cannot_delete_project(): void
    {
        $response = $this->actingAs($this->member)->delete("/projects/{$this->project->id}");
        $response->assertStatus(403);
    }
}
