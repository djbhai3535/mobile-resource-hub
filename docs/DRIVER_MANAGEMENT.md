# USB Driver Management — Mobile Resource Hub

## 1. Overview
The **USB Driver Management** module (`/admin/drivers`) provides certified connectivity drivers essential for PC-to-smartphone communication during bootloader, flashing, and recovery operations.

## 2. Driver Ecosystems

### 2.1 Manufacturer OEM Drivers
- **Samsung Android USB Driver:** Dedicated package for Galaxy smartphones and tablets, providing ADB, MTP, and Odin Download Mode COM ports.
- **Xiaomi USB Drivers:** Fastboot and Qualcomm EDL drivers for Redmi, POCO, and Xiaomi devices.
- **Google Universal ADB Driver:** Standard Android Debug Bridge interfaces for Pixel and AOSP hardware.

### 2.2 Chipset & Emergency Recovery Drivers
- **Qualcomm HS-USB QDLoader 9008 Driver:** Crucial driver for unbricking Qualcomm smartphones in Emergency Download (EDL) mode.
- **MediaTek MTK Preloader USB VCOM Driver:** Connects MediaTek SoC BootROM to SP Flash Tool before device boots into Android.
- **Spreadtrum / Unisoc SCI USB2Serial:** High-speed diagnostic driver for SC9863A, T606, T612, and T616 chipsets.

## 3. Metadata Structure
- **`brand_id`**: Optional foreign key to `brands`. If null, driver represents universal chipset package.
- **`driver_version`**: Exact package version string.
- **`windows_versions`**: Certified Windows operating systems (e.g. `Windows 11, 10, 8.1, 7`).
- **`architecture`**: Architecture compatibility (`32-bit (x86)`, `64-bit (x64)`).
- **`supported_devices`**: Comprehensive list of supported phone families and SoC generations.
- **`file_type`**: `EXE Installer`, `INF Driver Archive`, `MSI Package`, `ZIP Portable`.
- **`installation_instructions`**: Step-by-step guidance including Windows Driver Signature Enforcement advice when required.
- **`is_verified`**: WHQL digital signature verification status.
