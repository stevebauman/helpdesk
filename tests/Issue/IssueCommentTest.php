<?php

namespace App\Tests\Issue;

use App\Models\Comment;
use App\Models\Issue;
use App\Tests\TestCase;

class IssueCommentTest extends TestCase
{
    public function test_issue_comment_create()
    {
        $user = $this->createUser();

        $this->actingAs($user);

        $issue = factory(Issue::class)->create([
            'user_id' => $user->id,
        ]);

        $response = $this->call('POST', route('issues.comments.store', [$issue->id]), [
            'content' => 'Testing Comment',
        ]);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertSessionHas('flash_message.level', 'success');
    }

    public function test_issue_comment_create_as_admin()
    {
        $user = $this->createAdmin();

        $this->actingAs($user);

        $issue = factory(Issue::class)->create();

        $this->post(route('issues.comments.store', [$issue->id]), [
            'content' => 'Testing Comment',
        ]);

        $this->seeInDatabase('comments', [
            'id'      => 1,
            'content' => 'Testing Comment',
        ]);
    }

    public function test_issue_comment_edit()
    {
        $user = $this->createUser();

        $this->actingAs($user);

        $comment = factory(Comment::class)->create([
            'user_id' => $user->id,
        ]);

        $issue = factory(Issue::class)->create([
            'user_id' => $user->id,
        ]);

        $issue->comments()->save($comment);

        $this->patch(route('issues.comments.update', [$issue->id, $comment->id]), [
            'content' => 'Edited content',
        ]);

        $this->seeInDatabase('comments', [
            'id'      => 1,
            'content' => 'Edited content',
        ]);
    }

    public function test_admins_can_edit_all_comments()
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin);

        $user = $this->createUser();

        $comment = factory(Comment::class)->create([
            'user_id' => $user->id,
        ]);

        $issue = factory(Issue::class)->create([
            'user_id' => $user->id,
        ]);

        $issue->comments()->save($comment);

        $this->patch(route('issues.comments.update', [$issue->id, $comment->id]), [
            'content' => 'Edited content',
        ]);

        $this->seeInDatabase('comments', [
            'id'      => 1,
            'content' => 'Edited content',
        ]);
    }

    public function test_comments_support_markdown()
    {
        $user = $this->createUser();

        $this->actingAs($user);

        $comment = factory(Comment::class)->create([
            'user_id' => $user->id,
            'content' => '**Supports Markdown**',
        ]);

        $issue = factory(Issue::class)->create([
            'user_id' => $user->id,
        ]);

        $issue->comments()->save($comment);

        $this->visit(route('issues.show', [$issue->id]))->see('<strong>Supports Markdown</strong>');
    }
}
