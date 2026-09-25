<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * One-time repair: the mysql connection previously had 'engine' => null, so
 * every table was created under this server's MyISAM default instead of
 * InnoDB. MyISAM silently discards every FOREIGN KEY clause a migration
 * declares (constrained()/cascadeOnDelete()/nullOnDelete()/restrictOnDelete()),
 * leaving only a plain, unenforced index behind. This migration converts
 * every table to InnoDB, clears any orphaned references that accumulated
 * while nothing enforced them, then adds the real constraints the schema
 * always claimed to have.
 */
return new class extends Migration
{
    /**
     * child table => list of [column, references table, on delete action, nullable]
     */
    private array $relations = [
        'section_items' => [
            ['section_id', 'sections', 'cascade', false],
            ['image', 'media_items', 'set null', true],
        ],
        'banners' => [
            ['image', 'media_items', 'set null', true],
        ],
        'galleries' => [
            ['cover_image', 'media_items', 'set null', true],
        ],
        'gallery_items' => [
            ['gallery_id', 'galleries', 'cascade', false],
            ['image', 'media_items', 'set null', true],
        ],
        'projects' => [
            ['category_id', 'project_categories', 'set null', true],
            ['gallery_id', 'galleries', 'set null', true],
            ['cover_image', 'media_items', 'set null', true],
        ],
        'seo_meta' => [
            ['meta_image', 'media_items', 'set null', true],
        ],
        'gallery_section' => [
            ['section_id', 'sections', 'cascade', false],
            ['gallery_id', 'galleries', 'cascade', false],
        ],
        'pages' => [
            ['banner_image', 'media_items', 'set null', true],
        ],
        'team_members' => [
            ['category_id', 'team_categories', 'set null', true],
            ['photo', 'media_items', 'set null', true],
        ],
        'section_team_category' => [
            ['section_id', 'sections', 'cascade', false],
            ['team_category_id', 'team_categories', 'cascade', false],
        ],
        'stories' => [
            ['project_id', 'projects', 'set null', true],
            ['cover_image', 'media_items', 'set null', true],
            ['category_id', 'story_categories', 'set null', true],
        ],
        'visitor_book_entries' => [
            ['project_id', 'projects', 'cascade', false],
        ],
        'donations' => [
            ['project_id', 'projects', 'set null', true],
        ],
        'featured_visitors' => [
            ['photo', 'media_items', 'set null', true],
        ],
        'section_team_member' => [
            ['section_id', 'sections', 'cascade', false],
            ['team_member_id', 'team_members', 'cascade', false],
        ],
        'certificates' => [
            ['project_id', 'projects', 'set null', true],
        ],
        'enrollments' => [
            ['student_id', 'students', 'restrict', false],
            ['course_id', 'courses', 'restrict', false],
        ],
        'section_news_post' => [
            ['section_id', 'sections', 'cascade', false],
            ['news_post_id', 'news_posts', 'cascade', false],
        ],
        'section_news_category' => [
            ['section_id', 'sections', 'cascade', false],
            ['news_category_id', 'news_categories', 'cascade', false],
        ],
        'section_story_category' => [
            ['section_id', 'sections', 'cascade', false],
            ['story_category_id', 'story_categories', 'cascade', false],
        ],
        'section_story_item' => [
            ['section_id', 'sections', 'cascade', false],
            ['story_id', 'stories', 'cascade', false],
        ],
        'section_project_item' => [
            ['section_id', 'sections', 'cascade', false],
            ['project_id', 'projects', 'cascade', false],
        ],
        'sections' => [
            ['page_id', 'pages', 'cascade', false],
            ['image', 'media_items', 'set null', true],
        ],
        'menu_items' => [
            ['menu_id', 'menus', 'cascade', false],
            ['parent_id', 'menu_items', 'cascade', true],
        ],
        'section_project_category' => [
            ['section_id', 'sections', 'cascade', false],
            ['project_category_id', 'project_categories', 'cascade', false],
        ],
        'news_posts' => [
            ['category_id', 'news_categories', 'set null', true],
            ['cover_image', 'media_items', 'set null', true],
        ],
        'media_items' => [
            ['uploaded_by', 'users', 'set null', true],
        ],
        'model_has_permissions' => [
            ['permission_id', 'permissions', 'cascade', false],
        ],
        'model_has_roles' => [
            ['role_id', 'roles', 'cascade', false],
        ],
        'role_has_permissions' => [
            ['permission_id', 'permissions', 'cascade', false],
            ['role_id', 'roles', 'cascade', false],
        ],
    ];

    public function up(): void
    {
        // This is a one-time repair for the mysql connection's historical
        // 'engine' => null default (MyISAM). Other drivers (sqlite in tests,
        // pgsql) have no storage-engine concept and always honored
        // ->constrained() correctly, so there's nothing to repair there.
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        $this->convertAllTablesToInnodb();

        foreach ($this->relations as $table => $columns) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            foreach ($columns as [$column, $references, $onDelete, $nullable]) {
                if (! Schema::hasColumn($table, $column) || ! Schema::hasTable($references)) {
                    continue;
                }

                $this->cleanOrphans($table, $column, $references, $nullable);
                $this->addForeignKey($table, $column, $references, $onDelete);
            }
        }
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return;
        }

        foreach ($this->relations as $table => $columns) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            foreach ($columns as [$column, $references]) {
                $constraint = "{$table}_{$column}_fk";

                try {
                    DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$constraint}`");
                } catch (Throwable) {
                    // Constraint didn't exist under this name; nothing to drop.
                }
            }
        }

        // Data cleanup and the storage engine change are not reversed: there is
        // no reliable way to restore nulled/deleted orphan rows, and reverting
        // to MyISAM would only reintroduce the original bug.
    }

    private function convertAllTablesToInnodb(): void
    {
        $tables = DB::select(
            "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND ENGINE = 'MyISAM'"
        );

        foreach ($tables as $row) {
            DB::statement("ALTER TABLE `{$row->TABLE_NAME}` ENGINE=InnoDB");
        }
    }

    private function cleanOrphans(string $table, string $column, string $references, bool $nullable): void
    {
        // The subquery is wrapped in a derived table so self-referential
        // relations (menu_items.parent_id -> menu_items.id) don't hit
        // MySQL's "can't specify target table for update in FROM clause".
        $validIds = "(SELECT id FROM (SELECT id FROM `{$references}`) AS valid_ids)";

        if ($nullable) {
            DB::statement(
                "UPDATE `{$table}` SET `{$column}` = NULL WHERE `{$column}` IS NOT NULL AND `{$column}` NOT IN {$validIds}"
            );

            return;
        }

        DB::statement("DELETE FROM `{$table}` WHERE `{$column}` NOT IN {$validIds}");
    }

    private function addForeignKey(string $table, string $column, string $references, string $onDelete): void
    {
        $constraint = "{$table}_{$column}_fk";

        // Checked by column, not just this migration's own constraint name:
        // a fresh install (created after config/database.php's engine default
        // was fixed) already gets a real FK from ->constrained() under
        // Laravel's own naming, so this stays a no-op there too.
        $exists = DB::selectOne(
            "SELECT 1 FROM information_schema.KEY_COLUMN_USAGE k
             JOIN information_schema.TABLE_CONSTRAINTS c
               ON c.CONSTRAINT_SCHEMA = k.CONSTRAINT_SCHEMA AND c.CONSTRAINT_NAME = k.CONSTRAINT_NAME
             WHERE k.TABLE_SCHEMA = DATABASE() AND k.TABLE_NAME = ? AND k.COLUMN_NAME = ? AND c.CONSTRAINT_TYPE = 'FOREIGN KEY'",
            [$table, $column]
        );

        if ($exists) {
            return;
        }

        $action = strtoupper($onDelete);

        DB::statement(
            "ALTER TABLE `{$table}` ADD CONSTRAINT `{$constraint}` FOREIGN KEY (`{$column}`) REFERENCES `{$references}` (`id`) ON DELETE {$action}"
        );
    }
};
