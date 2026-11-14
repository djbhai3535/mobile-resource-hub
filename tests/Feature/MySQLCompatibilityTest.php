<?php

namespace Tests\Feature;

use Illuminate\Database\MySqlConnection;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\Grammars\MySqlGrammar;
use Tests\TestCase;

class MySQLCompatibilityTest extends TestCase
{
    protected MySqlGrammar $grammar;
    protected MySqlConnection $connection;

    protected function setUp(): void
    {
        parent::setUp();

        $pdo = $this->createMock(\PDO::class);
        $this->connection = new MySqlConnection($pdo, 'laravel', '', [
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'engine' => 'InnoDB',
        ]);
        $this->grammar = new MySqlGrammar($this->connection);
        $this->connection->setSchemaGrammar($this->grammar);
    }

    public function test_roles_and_permissions_table_mysql_ddl(): void
    {
        $blueprint = new Blueprint($this->connection, 'roles');
        $blueprint->create();
        $blueprint->id();
        $blueprint->string('name', 50)->unique();
        $blueprint->string('display_name', 100);
        $blueprint->string('description', 255)->nullable();
        $blueprint->timestamps();

        $statements = $blueprint->toSql($this->connection, $this->grammar);
        $sql = implode('; ', $statements);

        $this->assertStringContainsString('create table `roles`', $sql);
        $this->assertStringContainsString('`id` bigint unsigned not null auto_increment primary key', $sql);
        $this->assertStringContainsString('`name` varchar(50) not null', $sql);
        $this->assertStringContainsString('unique `roles_name_unique`(`name`)', $sql);
    }

    public function test_posts_table_mysql_ddl_and_foreign_keys(): void
    {
        $blueprint = new Blueprint($this->connection, 'posts');
        $blueprint->create();
        $blueprint->id();
        $blueprint->foreignId('author_id')->constrained('users')->cascadeOnDelete();
        $blueprint->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
        $blueprint->string('title', 255);
        $blueprint->string('slug', 191)->unique();
        $blueprint->text('excerpt')->nullable();
        $blueprint->longText('content');
        $blueprint->string('status', 30)->default('draft');
        $blueprint->timestamp('published_at')->nullable();
        $blueprint->softDeletes();
        $blueprint->timestamps();
        $blueprint->index(['status', 'published_at']);

        $statements = $blueprint->toSql($this->connection, $this->grammar);
        $sql = implode('; ', $statements);

        $this->assertStringContainsString('create table `posts`', $sql);
        $this->assertStringContainsString('`author_id` bigint unsigned not null', $sql);
        $this->assertStringContainsString('`slug` varchar(191) not null', $sql);
        $this->assertStringContainsString('unique `posts_slug_unique`(`slug`)', $sql);
        $this->assertStringContainsString('`deleted_at` timestamp null', $sql);
        $this->assertStringContainsString('index `posts_status_published_at_index`(`status`, `published_at`)', $sql);
        $this->assertStringContainsString('foreign key (`author_id`) references `users` (`id`) on delete cascade', $sql);
        $this->assertStringContainsString('foreign key (`category_id`) references `categories` (`id`) on delete set null', $sql);
    }

    public function test_categories_and_tags_mysql_ddl(): void
    {
        $blueprint = new Blueprint($this->connection, 'categories');
        $blueprint->create();
        $blueprint->id();
        $blueprint->string('name', 150);
        $blueprint->string('slug', 191)->unique();
        $blueprint->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
        $blueprint->text('description')->nullable();
        $blueprint->string('status', 30)->default('active');
        $blueprint->timestamps();

        $statements = $blueprint->toSql($this->connection, $this->grammar);
        $sql = implode('; ', $statements);

        $this->assertStringContainsString('create table `categories`', $sql);
        $this->assertStringContainsString('`slug` varchar(191) not null', $sql);
        $this->assertStringContainsString('foreign key (`parent_id`) references `categories` (`id`) on delete set null', $sql);
    }

    public function test_media_and_contact_messages_mysql_ddl(): void
    {
        $blueprint = new Blueprint($this->connection, 'media');
        $blueprint->create();
        $blueprint->id();
        $blueprint->string('name', 255);
        $blueprint->string('file_name', 255);
        $blueprint->string('file_path', 255);
        $blueprint->string('mime_type', 100);
        $blueprint->unsignedBigInteger('file_size');
        $blueprint->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
        $blueprint->timestamps();

        $statements = $blueprint->toSql($this->connection, $this->grammar);
        $sql = implode('; ', $statements);

        $this->assertStringContainsString('create table `media`', $sql);
        $this->assertStringContainsString('`file_size` bigint unsigned not null', $sql);
        $this->assertStringContainsString('foreign key (`uploaded_by`) references `users` (`id`) on delete set null', $sql);
    }

    public function test_activity_logs_and_menus_mysql_ddl(): void
    {
        $blueprint = new Blueprint($this->connection, 'activity_logs');
        $blueprint->create();
        $blueprint->id();
        $blueprint->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
        $blueprint->string('action', 50);
        $blueprint->string('subject_type', 100)->nullable();
        $blueprint->unsignedBigInteger('subject_id')->nullable();
        $blueprint->json('properties')->nullable();
        $blueprint->string('ip_address', 45)->nullable();
        $blueprint->timestamp('created_at')->useCurrent();
        $blueprint->index(['subject_type', 'subject_id']);

        $statements = $blueprint->toSql($this->connection, $this->grammar);
        $sql = implode('; ', $statements);

        $this->assertStringContainsString('create table `activity_logs`', $sql);
        $this->assertStringContainsString('`properties` json null', $sql);
        $this->assertStringContainsString('index `activity_logs_subject_type_subject_id_index`(`subject_type`, `subject_id`)', $sql);
    }

    public function test_brands_table_mysql_ddl(): void
    {
        $blueprint = new Blueprint($this->connection, 'brands');
        $blueprint->create();
        $blueprint->id();
        $blueprint->string('name', 100)->unique();
        $blueprint->string('slug', 191)->unique();
        $blueprint->string('logo', 255)->nullable();
        $blueprint->string('short_description', 500)->nullable();
        $blueprint->text('full_description')->nullable();
        $blueprint->string('website_url', 255)->nullable();
        $blueprint->string('country_of_origin', 100)->nullable();
        $blueprint->string('status', 30)->default('published');
        $blueprint->integer('sort_order')->default(0);
        $blueprint->softDeletes();
        $blueprint->timestamps();

        $statements = $blueprint->toSql($this->connection, $this->grammar);
        $sql = implode('; ', $statements);

        $this->assertStringContainsString('create table `brands`', $sql);
        $this->assertStringContainsString('unique `brands_name_unique`(`name`)', $sql);
        $this->assertStringContainsString('unique `brands_slug_unique`(`slug`)', $sql);
        $this->assertStringContainsString('`deleted_at` timestamp null', $sql);
    }

    public function test_devices_and_specs_table_mysql_ddl(): void
    {
        $blueprint = new Blueprint($this->connection, 'devices');
        $blueprint->create();
        $blueprint->id();
        $blueprint->foreignId('brand_id')->constrained('brands')->restrictOnDelete();
        $blueprint->string('name', 150);
        $blueprint->string('slug', 191)->unique();
        $blueprint->string('status', 30)->default('draft');
        $blueprint->string('market_status', 30)->default('available');
        $blueprint->json('image_gallery')->nullable();
        $blueprint->softDeletes();
        $blueprint->timestamps();

        $statements = $blueprint->toSql($this->connection, $this->grammar);
        $sql = implode('; ', $statements);

        $this->assertStringContainsString('create table `devices`', $sql);
        $this->assertStringContainsString('foreign key (`brand_id`) references `brands` (`id`) on delete restrict', $sql);
        $this->assertStringContainsString('`image_gallery` json null', $sql);

        // Test dynamic spec tables
        $specValBlueprint = new Blueprint($this->connection, 'device_specification_values');
        $specValBlueprint->create();
        $specValBlueprint->id();
        $specValBlueprint->foreignId('device_id')->constrained('devices')->cascadeOnDelete();
        $specValBlueprint->foreignId('field_id')->constrained('specification_fields')->cascadeOnDelete();
        $specValBlueprint->text('value')->nullable();
        $specValBlueprint->timestamps();
        $specValBlueprint->unique(['device_id', 'field_id']);

        $specStatements = $specValBlueprint->toSql($this->connection, $this->grammar);
        $specSql = implode('; ', $specStatements);

        $this->assertStringContainsString('create table `device_specification_values`', $specSql);
        $this->assertStringContainsString('foreign key (`device_id`) references `devices` (`id`) on delete cascade', $specSql);
        $this->assertStringContainsString('unique `device_specification_values_device_id_field_id_unique`(`device_id`, `field_id`)', $specSql);
    }

    public function test_firmware_and_download_mirrors_mysql_ddl(): void
    {
        $fwBlueprint = new Blueprint($this->connection, 'firmwares');
        $fwBlueprint->create();
        $fwBlueprint->id();
        $fwBlueprint->foreignId('brand_id')->constrained('brands')->restrictOnDelete();
        $fwBlueprint->foreignId('device_id')->nullable()->constrained('devices')->nullOnDelete();
        $fwBlueprint->string('title', 255);
        $fwBlueprint->string('slug', 191)->unique();
        $fwBlueprint->string('model_number', 100)->index();
        $fwBlueprint->string('csc', 50)->nullable()->index();
        $fwBlueprint->unsignedBigInteger('file_size_bytes')->nullable();
        $fwBlueprint->string('status', 30)->default('draft')->index();
        $fwBlueprint->unsignedBigInteger('download_requests_count')->default(0);
        $fwBlueprint->softDeletes();
        $fwBlueprint->timestamps();

        $fwStatements = $fwBlueprint->toSql($this->connection, $this->grammar);
        $fwSql = implode('; ', $fwStatements);

        $this->assertStringContainsString('create table `firmwares`', $fwSql);
        $this->assertStringContainsString('foreign key (`brand_id`) references `brands` (`id`) on delete restrict', $fwSql);
        $this->assertStringContainsString('foreign key (`device_id`) references `devices` (`id`) on delete set null', $fwSql);
        $this->assertStringContainsString('unique `firmwares_slug_unique`(`slug`)', $fwSql);
        $this->assertStringContainsString('index `firmwares_model_number_index`(`model_number`)', $fwSql);

        $mirrorBlueprint = new Blueprint($this->connection, 'download_mirrors');
        $mirrorBlueprint->create();
        $mirrorBlueprint->id();
        $mirrorBlueprint->morphs('mirrorable');
        $mirrorBlueprint->string('mirror_name', 100);
        $mirrorBlueprint->string('url', 1000)->nullable();
        $mirrorBlueprint->string('disk', 50)->default('local');
        $mirrorBlueprint->boolean('is_local')->default(false);
        $mirrorBlueprint->boolean('is_active')->default(true);
        $mirrorBlueprint->unsignedBigInteger('download_requests_count')->default(0);
        $mirrorBlueprint->timestamps();

        $mirrorStatements = $mirrorBlueprint->toSql($this->connection, $this->grammar);
        $mirrorSql = implode('; ', $mirrorStatements);

        $this->assertStringContainsString('create table `download_mirrors`', $mirrorSql);
        $this->assertStringContainsString('`mirrorable_type` varchar(191) not null', $mirrorSql);
        $this->assertStringContainsString('`mirrorable_id` bigint unsigned not null', $mirrorSql);
        $this->assertStringContainsString('index `download_mirrors_mirrorable_type_mirrorable_id_index`(`mirrorable_type`, `mirrorable_id`)', $mirrorSql);
    }

    public function test_flash_files_software_and_drivers_mysql_ddl(): void
    {
        // Flash files
        $flashBlueprint = new Blueprint($this->connection, 'flash_files');
        $flashBlueprint->create();
        $flashBlueprint->id();
        $flashBlueprint->foreignId('brand_id')->constrained('brands')->restrictOnDelete();
        $flashBlueprint->string('title', 255);
        $flashBlueprint->string('slug', 191)->unique();
        $flashBlueprint->string('model_number', 100);
        $flashBlueprint->string('file_type', 50)->index();
        $flashBlueprint->string('chipset', 100)->nullable();
        $flashBlueprint->string('status', 30)->default('draft')->index();
        $flashBlueprint->softDeletes();
        $flashBlueprint->timestamps();

        $flashStatements = $flashBlueprint->toSql($this->connection, $this->grammar);
        $flashSql = implode('; ', $flashStatements);

        $this->assertStringContainsString('create table `flash_files`', $flashSql);
        $this->assertStringContainsString('foreign key (`brand_id`) references `brands` (`id`) on delete restrict', $flashSql);

        // Software tools
        $softBlueprint = new Blueprint($this->connection, 'software_tools');
        $softBlueprint->create();
        $softBlueprint->id();
        $softBlueprint->string('name', 150);
        $softBlueprint->string('slug', 191)->unique();
        $softBlueprint->string('category', 50)->index();
        $softBlueprint->string('version', 50)->nullable();
        $softBlueprint->string('status', 30)->default('draft')->index();
        $softBlueprint->softDeletes();
        $softBlueprint->timestamps();

        $softStatements = $softBlueprint->toSql($this->connection, $this->grammar);
        $softSql = implode('; ', $softStatements);

        $this->assertStringContainsString('create table `software_tools`', $softSql);

        // Drivers
        $drvBlueprint = new Blueprint($this->connection, 'drivers');
        $drvBlueprint->create();
        $drvBlueprint->id();
        $drvBlueprint->foreignId('brand_id')->nullable()->constrained('brands')->nullOnDelete();
        $drvBlueprint->string('name', 150);
        $drvBlueprint->string('slug', 191)->unique();
        $drvBlueprint->string('driver_version', 50)->nullable();
        $drvBlueprint->string('status', 30)->default('draft')->index();
        $drvBlueprint->softDeletes();
        $drvBlueprint->timestamps();

        $drvStatements = $drvBlueprint->toSql($this->connection, $this->grammar);
        $drvSql = implode('; ', $drvStatements);

        $this->assertStringContainsString('create table `drivers`', $drvSql);
        $this->assertStringContainsString('foreign key (`brand_id`) references `brands` (`id`) on delete set null', $drvSql);
    }

    public function test_search_tables_mysql_ddl_and_indexes(): void
    {
        // search_documents
        $docBlueprint = new Blueprint($this->connection, 'search_documents');
        $docBlueprint->create();
        $docBlueprint->id();
        $docBlueprint->string('searchable_type', 120);
        $docBlueprint->unsignedBigInteger('searchable_id');
        $docBlueprint->string('content_type', 50);
        $docBlueprint->string('title', 255);
        $docBlueprint->string('slug', 255);
        $docBlueprint->text('excerpt')->nullable();
        $docBlueprint->longText('normalized_content');
        $docBlueprint->string('searchable_url', 500);
        $docBlueprint->string('model_code', 100)->nullable();
        $docBlueprint->string('brand_name', 100)->nullable();
        $docBlueprint->string('publication_status', 30)->default('published');
        $docBlueprint->timestamp('published_at')->nullable();
        $docBlueprint->timestamp('source_updated_at')->nullable();
        $docBlueprint->timestamp('indexed_at')->nullable();
        $docBlueprint->timestamps();

        $docBlueprint->unique(['searchable_type', 'searchable_id'], 'search_docs_unique_searchable');
        $docBlueprint->index(['publication_status', 'content_type'], 'search_docs_status_type_idx');
        $docBlueprint->index('model_code', 'search_docs_model_code_idx');

        $docStatements = $docBlueprint->toSql($this->connection, $this->grammar);
        $docSql = implode('; ', $docStatements);

        $this->assertStringContainsString('create table `search_documents`', $docSql);
        $this->assertStringContainsString('unique `search_docs_unique_searchable`(`searchable_type`, `searchable_id`)', $docSql);

        // search_document_terms
        $termBlueprint = new Blueprint($this->connection, 'search_document_terms');
        $termBlueprint->create();
        $termBlueprint->id();
        $termBlueprint->foreignId('search_document_id')->constrained('search_documents')->cascadeOnDelete();
        $termBlueprint->string('normalized_term', 100);
        $termBlueprint->string('field_name', 50)->default('content');
        $termBlueprint->integer('weight')->default(10);
        $termBlueprint->integer('occurrence_count')->default(1);
        $termBlueprint->timestamps();

        $termBlueprint->index(['normalized_term', 'search_document_id'], 'search_terms_term_doc_idx');

        $termStatements = $termBlueprint->toSql($this->connection, $this->grammar);
        $termSql = implode('; ', $termStatements);

        $this->assertStringContainsString('create table `search_document_terms`', $termSql);
        $this->assertStringContainsString('foreign key (`search_document_id`) references `search_documents` (`id`) on delete cascade', $termSql);
    }

    public function test_theme_and_homepage_tables_mysql_ddl(): void
    {
        // themes
        $themeBp = new Blueprint($this->connection, 'themes');
        $themeBp->create();
        $themeBp->id();
        $themeBp->string('name', 100);
        $themeBp->string('slug', 100)->unique();
        $themeBp->boolean('is_active')->default(false)->index();
        $themeBp->unsignedInteger('version')->default(1);
        $themeBp->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
        $themeBp->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
        $themeBp->timestamps();

        $themeSql = implode('; ', $themeBp->toSql($this->connection, $this->grammar));
        $this->assertStringContainsString('create table `themes`', $themeSql);
        $this->assertStringContainsString('unique `themes_slug_unique`(`slug`)', $themeSql);
        $this->assertStringContainsString('foreign key (`created_by`) references `users` (`id`) on delete set null', $themeSql);

        // theme_settings
        $settingBp = new Blueprint($this->connection, 'theme_settings');
        $settingBp->create();
        $settingBp->id();
        $settingBp->foreignId('theme_id')->constrained('themes')->cascadeOnDelete();
        $settingBp->boolean('is_published')->default(false)->index();
        $settingBp->json('branding')->nullable();
        $settingBp->json('global_colors')->nullable();
        $settingBp->timestamps();
        $settingBp->unique(['theme_id', 'is_published']);

        $settingSql = implode('; ', $settingBp->toSql($this->connection, $this->grammar));
        $this->assertStringContainsString('create table `theme_settings`', $settingSql);
        $this->assertStringContainsString('foreign key (`theme_id`) references `themes` (`id`) on delete cascade', $settingSql);
        $this->assertStringContainsString('unique `theme_settings_theme_id_is_published_unique`(`theme_id`, `is_published`)', $settingSql);

        // homepage_sections
        $secBp = new Blueprint($this->connection, 'homepage_sections');
        $secBp->create();
        $secBp->id();
        $secBp->string('section_key', 50)->unique();
        $secBp->string('title', 255);
        $secBp->boolean('is_enabled')->default(true)->index();
        $secBp->integer('sort_order')->default(0)->index();
        $secBp->timestamps();

        $secSql = implode('; ', $secBp->toSql($this->connection, $this->grammar));
        $this->assertStringContainsString('create table `homepage_sections`', $secSql);
        $this->assertStringContainsString('unique `homepage_sections_section_key_unique`(`section_key`)', $secSql);
    }

    public function test_advertisement_tables_mysql_ddl(): void
    {
        // advertisements
        $adBp = new Blueprint($this->connection, 'advertisements');
        $adBp->create();
        $adBp->id();
        $adBp->string('name', 150);
        $adBp->string('placement', 50)->index();
        $adBp->string('type', 30)->default('image');
        $adBp->string('destination_url', 500)->nullable();
        $adBp->text('custom_html')->nullable();
        $adBp->dateTime('start_date')->nullable();
        $adBp->dateTime('end_date')->nullable();
        $adBp->boolean('is_active')->default(true)->index();
        $adBp->unsignedBigInteger('impressions_count')->default(0);
        $adBp->unsignedBigInteger('clicks_count')->default(0);
        $adBp->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
        $adBp->timestamps();
        $adBp->index(['placement', 'is_active']);

        $adSql = implode('; ', $adBp->toSql($this->connection, $this->grammar));
        $this->assertStringContainsString('create table `advertisements`', $adSql);
        $this->assertStringContainsString('foreign key (`created_by`) references `users` (`id`) on delete set null', $adSql);
        $this->assertStringContainsString('index `advertisements_placement_is_active_index`(`placement`, `is_active`)', $adSql);

        // advertisement_logs
        $logBp = new Blueprint($this->connection, 'advertisement_logs');
        $logBp->create();
        $logBp->id();
        $logBp->foreignId('advertisement_id')->constrained('advertisements')->cascadeOnDelete();
        $logBp->string('event_type', 20);
        $logBp->string('placement', 50);
        $logBp->timestamp('created_at')->nullable()->index();
        $logBp->index(['advertisement_id', 'event_type']);

        $logSql = implode('; ', $logBp->toSql($this->connection, $this->grammar));
        $this->assertStringContainsString('create table `advertisement_logs`', $logSql);
        $this->assertStringContainsString('foreign key (`advertisement_id`) references `advertisements` (`id`) on delete cascade', $logSql);
    }
}
