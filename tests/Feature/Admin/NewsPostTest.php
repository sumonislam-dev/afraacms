<?php

namespace Tests\Feature\Admin;

use App\Models\NewsCategory;
use App\Models\NewsPost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Concerns\CreatesAdminUsers;
use Tests\TestCase;

class NewsPostTest extends TestCase
{
    use CreatesAdminUsers, RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $post = NewsPost::factory()->create();

        $this->get(route('admin.news.index'))->assertRedirect(route('login'));
        $this->get(route('admin.news.edit', $post))->assertRedirect(route('login'));
    }

    public function test_a_user_without_permissions_cannot_view_news(): void
    {
        $user = $this->userWithoutPermissions();

        $this->actingAs($user)->get(route('admin.news.index'))->assertForbidden();
        $this->actingAs($user)->get(route('admin.news.create'))->assertForbidden();
    }

    public function test_editor_can_view_the_news_list(): void
    {
        $editor = $this->editor();
        NewsPost::factory()->create(['title' => 'New Scholarship Round']);

        $this->actingAs($editor)
            ->get(route('admin.news.index'))
            ->assertOk()
            ->assertSee('New Scholarship Round');
    }

    public function test_editor_can_create_a_post_with_a_category(): void
    {
        $editor = $this->editor();
        $category = NewsCategory::factory()->create();

        $response = $this->actingAs($editor)->post(route('admin.news.store'), [
            'category_id' => $category->id,
            'title' => 'New Office Opening',
            'slug' => 'new-office-opening',
            'status' => 'draft',
        ]);

        $response->assertRedirect(route('admin.news.index'));
        $this->assertDatabaseHas('news_posts', [
            'slug' => 'new-office-opening',
            'category_id' => $category->id,
        ]);
    }

    /**
     * The Content field is authored via a rich-text editor and rendered
     * unescaped on the public frontend - anything outside its own toolbar's
     * output (see config/purifier.php's "cms" profile) must be stripped
     * before it ever reaches the database, not just relied on at render time.
     */
    public function test_a_posts_content_is_sanitized_of_disallowed_html_on_create(): void
    {
        $editor = $this->editor();

        $this->actingAs($editor)->post(route('admin.news.store'), [
            'title' => 'Malicious Post',
            'slug' => 'malicious-post',
            'status' => 'draft',
            'content' => '<p>Safe text</p><script>alert(1)</script><a href="javascript:alert(1)">bad link</a>',
        ]);

        $post = NewsPost::whereSlug('malicious-post')->firstOrFail();

        $this->assertStringNotContainsString('<script', $post->content);
        $this->assertStringNotContainsString('javascript:', $post->content);
        $this->assertStringContainsString('<p>Safe text</p>', $post->content);
    }

    public function test_a_user_without_permissions_cannot_create_a_post(): void
    {
        $user = $this->userWithoutPermissions();

        $this->actingAs($user)->post(route('admin.news.store'), [
            'title' => 'Sneaky Post',
            'slug' => 'sneaky-post',
            'status' => 'draft',
        ])->assertForbidden();

        $this->assertDatabaseMissing('news_posts', ['slug' => 'sneaky-post']);
    }

    public function test_creating_a_post_requires_title_and_slug(): void
    {
        $editor = $this->editor();

        $this->actingAs($editor)
            ->post(route('admin.news.store'), ['slug' => 'no-title', 'status' => 'draft'])
            ->assertSessionHasErrors('title');
    }

    public function test_a_post_slug_must_be_unique(): void
    {
        $editor = $this->editor();
        NewsPost::factory()->create(['slug' => 'existing-post']);

        $this->actingAs($editor)
            ->post(route('admin.news.store'), [
                'title' => 'Duplicate',
                'slug' => 'existing-post',
                'status' => 'draft',
            ])
            ->assertSessionHasErrors('slug');
    }

    public function test_editor_can_update_a_post(): void
    {
        $editor = $this->editor();
        $post = NewsPost::factory()->create(['title' => 'Old Title', 'slug' => 'old-title']);

        $response = $this->actingAs($editor)->put(route('admin.news.update', $post), [
            'title' => 'New Title',
            'slug' => 'old-title',
            'status' => 'published',
            'is_featured' => true,
        ]);

        $response->assertRedirect(route('admin.news.index'));
        $post->refresh();
        $this->assertSame('New Title', $post->title);
        $this->assertSame('published', $post->status);
        $this->assertTrue($post->is_featured);
    }

    public function test_editor_can_delete_a_post(): void
    {
        $editor = $this->editor();
        $post = NewsPost::factory()->create();

        $this->actingAs($editor)
            ->delete(route('admin.news.destroy', $post))
            ->assertRedirect(route('admin.news.index'));

        $this->assertNull(NewsPost::find($post->id));
        $this->assertSoftDeleted($post);
    }

    public function test_editor_can_restore_a_trashed_post(): void
    {
        $editor = $this->editor();
        $post = NewsPost::factory()->create();
        $post->delete();

        $this->actingAs($editor)
            ->post(route('admin.news.restore', $post))
            ->assertRedirect(route('admin.news.trash'));

        $this->assertNotSoftDeleted($post);
    }

    public function test_editor_can_upload_an_attachment_with_a_post(): void
    {
        $editor = $this->editor();
        $file = UploadedFile::fake()->create('notice.pdf', 100);

        $this->actingAs($editor)->post(route('admin.news.store'), [
            'title' => 'Notice With Attachment',
            'slug' => 'notice-with-attachment',
            'status' => 'published',
            'attachment' => $file,
        ])->assertRedirect(route('admin.news.index'));

        $post = NewsPost::where('slug', 'notice-with-attachment')->firstOrFail();
        $this->assertNotNull($post->attachment_url);
        $this->assertSame('notice.pdf', $post->attachment_file_name);
    }

    public function test_an_attachment_must_be_an_allowed_file_type(): void
    {
        $editor = $this->editor();
        $file = UploadedFile::fake()->create('malware.exe', 100);

        $this->actingAs($editor)
            ->post(route('admin.news.store'), [
                'title' => 'Bad Attachment',
                'slug' => 'bad-attachment',
                'status' => 'draft',
                'attachment' => $file,
            ])
            ->assertSessionHasErrors('attachment');
    }

    public function test_editor_can_remove_an_existing_attachment(): void
    {
        $editor = $this->editor();
        $post = NewsPost::factory()->create(['title' => 'Has Attachment', 'slug' => 'has-attachment']);
        $post->addMedia(UploadedFile::fake()->create('old.pdf', 50))->toMediaCollection('attachment');
        $this->assertNotNull($post->fresh()->attachment_url);

        $this->actingAs($editor)->put(route('admin.news.update', $post), [
            'title' => 'Has Attachment',
            'slug' => 'has-attachment',
            'status' => 'draft',
            'remove_attachment' => true,
        ])->assertRedirect(route('admin.news.index'));

        $this->assertNull($post->fresh()->attachment_url);
    }

    public function test_uploading_a_new_attachment_replaces_the_old_one(): void
    {
        $editor = $this->editor();
        $post = NewsPost::factory()->create(['title' => 'Swap Attachment', 'slug' => 'swap-attachment']);
        $post->addMedia(UploadedFile::fake()->create('old.pdf', 50))->toMediaCollection('attachment');

        $this->actingAs($editor)->put(route('admin.news.update', $post), [
            'title' => 'Swap Attachment',
            'slug' => 'swap-attachment',
            'status' => 'draft',
            'attachment' => UploadedFile::fake()->create('new.pdf', 50),
        ])->assertRedirect(route('admin.news.index'));

        $post->refresh();
        $this->assertSame('new.pdf', $post->attachment_file_name);
        $this->assertSame(1, $post->getMedia('attachment')->count());
    }
}
