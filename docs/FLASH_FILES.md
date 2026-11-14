# Flash Files & Repair Firmware — Mobile Resource Hub

## 1. Overview
The **Flash Files Management** module (`/admin/flash-files`) manages technical unbrick firmware, chipset-specific partition maps, emergency download packages, and authentication agents used by technicians to restore bricked smartphones.

## 2. Supported Partition Architectures (`file_type`)

| File Type | Architecture / SoC Family | Primary Flashing Tool | Key File Payloads |
| :--- | :--- | :--- | :--- |
| `scatter_firmware` | MediaTek (MTK) Helio & Dimensity | SP Flash Tool | `MTxxxx_Android_scatter.txt`, `preloader.bin`, `boot.img` |
| `pac_file` | Spreadtrum / Unisoc | ResearchDownload / SPD Upgrade | Single packaged `.pac` archive |
| `qfil_package` | Qualcomm Snapdragon EDL (9008) | QFIL / QPST | `rawprogram0.xml`, `patch0.xml`, `prog_firehose_*.mbn` |
| `stock_rom` | Universal Fastboot / Factory | Fastboot CLI / Vendor Suite | `flash_all.bat`, `images/*.img` |
| `da_file` | MediaTek Custom Secure Boot | SP Flash Tool | `DA_SWSEC_*.bin`, `MTK_AllInOne_DA.bin` |
| `recovery_image` | Custom / Stock Recovery | Fastboot / Heimdall | `recovery.img` |
| `boot_image` | Kernel & Ramdisk | Fastboot | `boot.img`, `init_boot.img` |

## 3. Database & Relational Schema
- **`brand_id`**: Foreign key to `brands`.
- **`device_id`**: Optional foreign key to `devices`.
- **`title`**: Descriptive name including hardware code (e.g. `Xiaomi Redmi Note 13 Pro 5G Fastboot ROM (garnet)`).
- **`model_number`**: Exact hardware model or motherboard code.
- **`chipset`**: Explicit System-on-Chip identifier (e.g., `Snapdragon 7s Gen 2`, `MT6768`, `SC9863A`).
- **`flashing_tool`**: Software recommended for flashing this package.
- **`compatibility_notes`**: Critical technician notes on locked bootloaders, hardware revisions, and display panel variations.
- **`installation_instructions`**: Procedural steps for emergency mode flashing.
- **`download_requests_count`**: Real-time counter of user download attempts.
- **`is_verified`**: Quality assurance badge indicating genuine, tested binaries.

## 4. Multi-Mirror Architecture
Flash files support multiple mirrors (`download_mirrors` polymorphic relation):
- High-speed direct CDN mirrors.
- Cloud archive mirrors (Google Drive, Mega, MediaFire).
- In-house private disk streamed packages.
