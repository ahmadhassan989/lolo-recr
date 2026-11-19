<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\Job;
use App\Models\Project;
use App\Models\User;
use App\Notifications\ApplicationReceived;
use App\Notifications\InterviewScheduled;
use App\Notifications\JobOfferSent;
use App\Notifications\NewApplicationAlert;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class NotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_apply_notifies_candidate_and_project_team(): void
    {
        Storage::fake('public');
        Notification::fake();

        $lead = User::factory()->create();
        $project = Project::factory()->create(['status' => 'open', 'team_lead_id' => $lead->id]);
        $job = Job::factory()->create(['project_id' => $project->id, 'status' => 'open']);

        $this->post(route('careers.submit', $job), [
            'first_name' => 'Sara',
            'last_name' => 'Ahmad',
            'email' => 'sara@example.com',
            'resume' => UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf'),
        ])->assertRedirect(route('careers.thanks'));

        $candidate = Candidate::where('email', 'sara@example.com')->firstOrFail();
        Notification::assertSentTo($candidate, ApplicationReceived::class);
        Notification::assertSentTo($lead, NewApplicationAlert::class);
    }

    public function test_duplicate_application_is_ignored(): void
    {
        Storage::fake('public');
        Notification::fake();

        $job = Job::factory()->create(['status' => 'open']);
        $payload = [
            'first_name' => 'Sara',
            'last_name' => 'Ahmad',
            'email' => 'dup@example.com',
            'resume' => UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf'),
        ];

        $this->post(route('careers.submit', $job), $payload)->assertRedirect(route('careers.thanks'));
        $this->post(route('careers.submit', $job), $payload)->assertRedirect(route('careers.thanks'));

        $this->assertSame(1, \App\Models\Application::count());
        Notification::assertSentToTimes(Candidate::first(), ApplicationReceived::class, 1);
    }

    public function test_interview_store_notifies_candidate(): void
    {
        Notification::fake();
        $candidate = Candidate::factory()->create();

        $this->actingAs($this->superAdmin())->post(route('interviews.store', $candidate), [
            'interview_date' => now()->addDays(3)->format('Y-m-d H:i'),
            'interviewer' => 'HR Team',
            'type' => 'hr',
            'result' => 'pending',
        ])->assertRedirect();

        Notification::assertSentTo($candidate, InterviewScheduled::class);
    }

    public function test_offer_store_notifies_candidate(): void
    {
        Notification::fake();
        $candidate = Candidate::factory()->create();

        $this->actingAs($this->superAdmin())->post(route('job-offers.store'), [
            'candidate_id' => $candidate->id,
            'position_title' => 'Senior Engineer',
            'salary' => 1500,
            'currency' => 'usd',
        ])->assertRedirect();

        Notification::assertSentTo($candidate, JobOfferSent::class);
    }

    protected function superAdmin(): User
    {
        app(PermissionRegistrar::class)->setPermissionsTeamId(0);
        Role::findOrCreate('super_admin', 'web');

        return tap(User::factory()->create())->assignRole('super_admin');
    }
}
