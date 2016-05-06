<?php

namespace App\Tests\Issue;

use App\Models\Issue;
use App\Tests\TestCase;

class IssueTest extends TestCase
{
    public function test_issue_index_regular_user()
    {
        $user = $this->createUser();

        $this->actingAs($user);

        $this->visit(route('issues.index'))->see('Tickets');
    }

    public function test_issue_index_regular_user_sees_only_their_own_tickets()
    {
        $issue = factory(Issue::class)->create();

        $user = $this->createUser();

        $this->actingAs($user);

        $this->visit(route('issues.index'))
            ->see('There are no records to display.');

        $this->seeInDatabase('issues', ['user_id' => $issue->user_id]);
    }

    public function test_admins_can_see_all_tickets()
    {
        $user = $this->createAdmin();

        $this->actingAs($user);

        $issue = factory(Issue::class)->make();

        $this->visit(route('issues.index'))
            ->see($issue->id);
    }

    public function test_issue_create()
    {
        $user = $this->createUser();

        $this->actingAs($user);

        $this->post(route('issues.store'), [
            'title'       => 'Issue Title',
            'occurred_at' => '03/03/2016 12:00 AM',
            'description' => 'Issue Description',
        ]);

        $this->seeInDatabase('issues', [
            'id'    => 1,
            'title' => 'Issue Title',
        ]);
    }

    public function test_issue_create_validation_errors()
    {
        $user = $this->createUser();

        $this->actingAs($user);

        $this->post(route('issues.store'));

        $this->assertSessionHasErrors();
    }

    public function test_regular_users_cannot_see_labels_and_users_field()
    {
        $user = $this->createUser();

        $this->actingAs($user);

        $this->visit(route('issues.create'))
            ->dontSee('Labels')
            ->dontSee('Users');

        $issue = factory(Issue::class)->create(['user_id' => $user->id]);

        $this->visit(route('issues.show', [$issue->id]))
            ->dontSee('Labels')
            ->dontSee('Users');
    }

    public function test_admins_can_see_labels_and_users_field()
    {
        $user = $this->createAdmin();

        $this->actingAs($user);

        $this->visit(route('issues.create'))
            ->see('Labels')
            ->see('Users');

        $issue = factory(Issue::class)->create(['user_id' => $user->id]);

        $this->visit(route('issues.show', [$issue->id]))
            ->see('Labels')
            ->see('Users');
    }

    public function test_index_closed_only_shows_only_closed_issues()
    {
        $user = $this->createUser();

        $this->actingAs($user);

        $closed = factory(Issue::class)->create([
            'title'     => str_random(),
            'user_id'   => $user->id,
            'closed'    => true,
        ]);

        $open = factory(Issue::class)->create([
            'title'     => str_random(),
            'user_id'   => $user->id,
            'closed'    => false,
        ]);

        $this->visit(route('issues.closed'))
            ->see($closed->title)
            ->dontSee($open->title);
    }
}
