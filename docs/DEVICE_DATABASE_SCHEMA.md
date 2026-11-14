# Mobile Device Database Schema Reference

## 1. DDL & Column Reference

### 1.1 `brands` Table
```sql
CREATE TABLE `brands` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(100) NOT NULL UNIQUE,
  `slug` varchar(191) NOT NULL UNIQUE,
  `logo` varchar(255) NULL,
  `short_description` varchar(500) NULL,
  `full_description` text NULL,
  `website_url` varchar(255) NULL,
  `country_of_origin` varchar(100) NULL,
  `status` varchar(30) NOT NULL DEFAULT 'published',
  `sort_order` int NOT NULL DEFAULT 0,
  `seo_title` varchar(255) NULL,
  `meta_description` varchar(500) NULL,
  `canonical_url` varchar(255) NULL,
  `og_image` varchar(255) NULL,
  `deleted_at` timestamp NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  INDEX `brands_status_index` (`status`),
  INDEX `brands_sort_order_index` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 1.2 `devices` Table
```sql
CREATE TABLE `devices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `brand_id` bigint unsigned NOT NULL,
  `name` varchar(150) NOT NULL,
  `marketing_name` varchar(150) NULL,
  `slug` varchar(191) NOT NULL UNIQUE,
  `product_description` text NULL,
  `short_description` varchar(500) NULL,
  `featured_image` varchar(255) NULL,
  `image_gallery` json NULL,
  `announced_date` date NULL,
  `release_date` date NULL,
  `status` varchar(30) NOT NULL DEFAULT 'draft',
  `market_status` varchar(30) NOT NULL DEFAULT 'available',
  `os` varchar(100) NULL,
  `os_version` varchar(100) NULL,
  `device_category` varchar(50) NOT NULL DEFAULT 'smartphone',
  `official_product_url` varchar(255) NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int NOT NULL DEFAULT 0,
  `seo_title` varchar(255) NULL,
  `meta_description` text NULL,
  `canonical_url` varchar(255) NULL,
  `og_image` varchar(255) NULL,
  `deleted_at` timestamp NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  CONSTRAINT `devices_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE RESTRICT,
  INDEX `devices_status_release_date_index` (`status`, `release_date`),
  INDEX `devices_market_status_index` (`market_status`),
  INDEX `devices_device_category_index` (`device_category`),
  INDEX `devices_is_featured_index` (`is_featured`),
  INDEX `devices_sort_order_index` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 1.3 `device_model_numbers` Table
```sql
CREATE TABLE `device_model_numbers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `device_id` bigint unsigned NOT NULL,
  `model_number` varchar(100) NOT NULL,
  `region` varchar(100) NULL,
  `market` varchar(100) NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `notes` varchar(255) NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  CONSTRAINT `device_model_numbers_device_id_foreign` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE CASCADE,
  UNIQUE KEY `device_model_numbers_device_id_model_number_unique` (`device_id`, `model_number`),
  INDEX `device_model_numbers_model_number_index` (`model_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 1.4 `device_variants` Table
```sql
CREATE TABLE `device_variants` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `device_id` bigint unsigned NOT NULL,
  `name` varchar(150) NOT NULL,
  `ram` varchar(50) NULL,
  `internal_storage` varchar(50) NULL,
  `color` varchar(100) NULL,
  `market_region` varchar(100) NULL,
  `model_reference` varchar(100) NULL,
  `notes` varchar(255) NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  CONSTRAINT `device_variants_device_id_foreign` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 1.5 `specification_groups` Table
```sql
CREATE TABLE `specification_groups` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(100) NOT NULL,
  `slug` varchar(191) NOT NULL UNIQUE,
  `sort_order` int NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  INDEX `specification_groups_sort_order_index` (`sort_order`),
  INDEX `specification_groups_is_active_index` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 1.6 `specification_fields` Table
```sql
CREATE TABLE `specification_fields` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `group_id` bigint unsigned NOT NULL,
  `label` varchar(150) NOT NULL,
  `key` varchar(191) NOT NULL UNIQUE,
  `unit` varchar(50) NULL,
  `field_type` varchar(40) NOT NULL DEFAULT 'text',
  `help_text` varchar(255) NULL,
  `options` json NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT 0,
  `show_on_public` tinyint(1) NOT NULL DEFAULT 1,
  `is_filterable` tinyint(1) NOT NULL DEFAULT 0,
  `sort_order` int NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  CONSTRAINT `specification_fields_group_id_foreign` FOREIGN KEY (`group_id`) REFERENCES `specification_groups` (`id`) ON DELETE CASCADE,
  INDEX `specification_fields_sort_order_index` (`sort_order`),
  INDEX `specification_fields_is_active_index` (`is_active`),
  INDEX `specification_fields_show_on_public_index` (`show_on_public`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### 1.7 `device_specification_values` Table
```sql
CREATE TABLE `device_specification_values` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `device_id` bigint unsigned NOT NULL,
  `field_id` bigint unsigned NOT NULL,
  `value` text NULL,
  `additional_info` varchar(255) NULL,
  `created_at` timestamp NULL,
  `updated_at` timestamp NULL,
  CONSTRAINT `device_spec_values_device_id_foreign` FOREIGN KEY (`device_id`) REFERENCES `devices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `device_spec_values_field_id_foreign` FOREIGN KEY (`field_id`) REFERENCES `specification_fields` (`id`) ON DELETE CASCADE,
  UNIQUE KEY `device_spec_unique` (`device_id`, `field_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```
