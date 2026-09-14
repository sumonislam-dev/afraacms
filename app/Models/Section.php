<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['page_id', 'type', 'anchor', 'heading', 'subheading', 'body', 'image', 'button_text', 'button_url', 'layout', 'source', 'show_search', 'item_limit', 'is_active', 'sort_order'])]
class Section extends Model
{
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'show_search' => 'boolean',
            'item_limit' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Page::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SectionItem::class)->orderBy('sort_order');
    }

    /**
     * Albums explicitly picked for this section (only meaningful for the
     * "gallery_albums" type) - empty means "show all active albums".
     */
    public function galleries(): BelongsToMany
    {
        return $this->belongsToMany(Gallery::class)->orderBy('galleries.sort_order');
    }

    /**
     * Team members explicitly hand-picked for this section (only
     * meaningful for the "team_members" type, in "Specific Members" mode).
     */
    public function teamMembers(): BelongsToMany
    {
        return $this->belongsToMany(TeamMember::class, 'section_team_member')->orderBy('team_members.sort_order');
    }

    /**
     * Team categories picked for this section (only meaningful for the
     * "team_members" type, in "By Category" mode) - e.g. show only
     * "Volunteers" here and only "Board" on another page.
     */
    public function teamCategories(): BelongsToMany
    {
        return $this->belongsToMany(TeamCategory::class, 'section_team_category');
    }

    /**
     * Notice-format news posts explicitly hand-picked for this section (only
     * meaningful for the "notices" type, in "Specific Notices" mode).
     */
    public function newsPosts(): BelongsToMany
    {
        return $this->belongsToMany(NewsPost::class, 'section_news_post')->orderByDesc('news_posts.published_at');
    }

    /**
     * News categories picked for this section (only meaningful for the
     * "notices" type, in "By Category" mode).
     */
    public function newsCategories(): BelongsToMany
    {
        return $this->belongsToMany(NewsCategory::class, 'section_news_category');
    }

    /**
     * Projects explicitly hand-picked for this section (only meaningful for
     * the "content_list" type, source "projects", "Specific Items" mode).
     */
    public function projectItems(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'section_project_item');
    }

    /**
     * Project categories picked for this section (only meaningful for the
     * "content_list" type, source "projects", "By Category" mode).
     */
    public function projectCategories(): BelongsToMany
    {
        return $this->belongsToMany(ProjectCategory::class, 'section_project_category');
    }

    /**
     * Success Stories explicitly hand-picked for this section (only
     * meaningful for the "content_list" type, source "stories", "Specific
     * Items" mode - Story has no category concept yet, so that's the only
     * non-"all" mode available for this source).
     */
    public function storyItems(): BelongsToMany
    {
        return $this->belongsToMany(Story::class, 'section_story_item');
    }

    /**
     * Story categories picked for this section (only meaningful for the
     * "content_list" type, source "stories", "By Category" mode).
     */
    public function storyCategories(): BelongsToMany
    {
        return $this->belongsToMany(StoryCategory::class, 'section_story_category');
    }

    public function getImageUrlAttribute(): ?string
    {
        return media_url($this->image);
    }

    /**
     * The section type's field configuration from config/sections.php.
     *
     * @return array{label: string, fields: array, has_items: bool, item_fields?: array}
     */
    public function typeConfig(): array
    {
        return config("sections.types.{$this->type}", []);
    }
}
