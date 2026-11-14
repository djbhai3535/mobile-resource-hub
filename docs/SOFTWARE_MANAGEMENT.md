# Flashing Software & Mobile Utilities — Mobile Resource Hub

## 1. Overview
The **Software Tools Management** module (`/admin/software`) maintains a certified repository of flashing utilities, root managers, ADB toolkits, and device service suites.

## 2. Categorization System (`category`)
1. **`flashing_tools`**: Firmware installers (e.g., Samsung Odin, SP Flash Tool, Mi Flash Tool, QFIL, Heimdall).
2. **`android_utilities`**: Command-line SDK platform tools, ADB shell helpers, Fastboot runners.
3. **`mobile_repair_tools`**: FRP unlock assistants, IMEI diagnostic tools, NVRAM rebuild utilities.
4. **`backup_tools`**: Data migration tools, partition backup suites (e.g., Samsung Smart Switch).
5. **`recovery_utilities`**: Custom recovery installers (e.g., TWRP, OrangeFox, PBRP).
6. **`device_management`**: Complete desktop companion suites.

## 3. Metadata & Technical Specifications
- **`version`**: Semantic or vendor build version (e.g., `v3.14.4`, `v6.2216`).
- **`developer`**: Organization or author (e.g., `Samsung Electronics`, `Google LLC`, `MediaTek Inc.`).
- **`supported_os`**: OS compatibility list (e.g., `Windows 11, 10, 8.1, 7`, `macOS`, `Linux`).
- **`supported_architectures`**: Binary target (`32-bit (x86)`, `64-bit (x64)`, `Universal arm64`).
- **`dependencies`**: System prerequisites (e.g., `Microsoft Visual C++ 2015-2022`, `.NET Framework 4.8`).
- **`file_format`**: Archive type (`.zip`, `.exe`, `.msi`, `.7z`, `.tar.gz`).
- **`license_type`**: `Freeware`, `Open Source (Apache/GPL/MIT)`, `OEM Proprietary`.
- **`is_verified`**: Quality and security verification flag.

## 4. Executable Isolation & Security
All software executable files uploaded to the server:
- Are strictly stored in private disk directories (`storage/app/private_downloads/software/`).
- Web server configurations forbid direct HTTP execution or access inside private storage.
- Download delivery uses chunked streaming via `FileStorageService::streamDownload` with `Content-Disposition: attachment`.
- SHA-256 cryptographic hashes are recorded for post-download integrity verification.
