# Dynamic Specifications System — Mobile Resource Hub

## 1. Architectural Philosophy
Mobile technology evolves continuously. Hardcoding specification parameters into fixed database columns leads to schema bloat, repetitive database migrations, and inflexible codebases. 

Mobile Resource Hub solves this through a normalized, relational **Dynamic Specifications Schema** (`specification_groups`, `specification_fields`, `device_specification_values`).

Any administrator with `specifications.manage` permissions can:
1. Create new specification groups (e.g. *Satellite Connectivity*, *AI Accelerators*, *Foldable Hinge Mechanics*).
2. Add new technical fields with designated data types (`text`, `textarea`, `number`, `boolean`, `select`).
3. Set units (e.g. `mAh`, `nits`, `W`, `MP`, `Hz`, `mm`).
4. Reorder groups and fields via sort order.
5. Toggle visibility on public device pages without altering code or running migrations.

## 2. Relational Schema Structure

```
+------------------------+        +------------------------+        +------------------------------+
|  specification_groups  | 1    N |  specification_fields  | 1    N |  device_specification_values |
+------------------------+--------+------------------------+--------+------------------------------+
| id                     |        | id                     |        | id                           |
| name                   |        | group_id (FK)          |        | device_id (FK)               |
| slug (unique)          |        | label                  |        | field_id (FK)                |
| sort_order             |        | key (unique)           |        | value (text)                 |
| is_active              |        | unit                   |        | additional_info              |
+------------------------+        | field_type             |        | timestamps                   |
                                  | help_text              |        +------------------------------+
                                  | sort_order             |
                                  | show_on_public         |
                                  | is_active              |
                                  +------------------------+
```

## 3. Seeded Standard Specification Groups (15 Baseline Groups)
The system boots with 15 standard mobile specification groups and over 50 normalized fields:

| Group Name | Slug | Typical Fields |
|---|---|---|
| **1. Network** | `network` | Technology, 2G Bands, 3G Bands, 4G Bands, 5G Bands, Speed |
| **2. Launch** | `launch` | Announced, Market Status |
| **3. Body** | `body` | Dimensions, Weight, Build Material, SIM Configuration, Resistance |
| **4. Display** | `display` | Display Type, Screen Size, Resolution, Protection, Refresh Rate, Peak Brightness |
| **5. Platform** | `platform` | Operating System, Chipset, CPU Processor, GPU Graphics |
| **6. Memory** | `memory` | Card Slot, Internal Storage & RAM |
| **7. Main Camera** | `main-camera` | Setup, Modules & Lenses, Features, Video Capabilities |
| **8. Selfie Camera** | `selfie-camera` | Setup, Modules, Features, Video Capabilities |
| **9. Sound** | `sound` | Loudspeaker, 3.5mm Headphone Jack, Audio Quality |
| **10. Communications** | `communications` | WLAN / Wi-Fi, Bluetooth, Positioning / GPS, NFC, Radio, USB Interface |
| **11. Sensors** | `sensors` | Sensors List |
| **12. Battery** | `battery` | Battery Type, Capacity |
| **13. Charging** | `charging` | Wired Charging, Wireless Charging, Reverse Charging |
| **14. Miscellaneous** | `miscellaneous` | Color Options, Model Designations, SAR Specific Absorption |
| **15. Additional Features** | `additional-features` | Biometric Security, Special Capabilities & AI |

## 4. Efficient Group Retrieval: `getGroupedSpecifications()`
On the `Device` model, the method `getGroupedSpecifications()` efficiently groups and sorts all verified specification values for high-speed presentation:
- Joins active fields and active groups where `show_on_public = true`.
- Groups entries by group sort order.
- Sorts fields within each group by field sort order.
- Zero extra queries: leverages eager loading `with('specificationValues.field.group')`.
- Returns a normalized zero-indexed list of groups and their corresponding field-value tuples.
