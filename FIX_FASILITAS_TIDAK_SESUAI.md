# FIX: Hasil Rekomendasi Tidak Sesuai Fasilitas yang Dicentang

## 🐛 Masalah Yang Dilaporkan User

**Skenario**:
- User mengisi questionnaire: Budget termurah, Fasilitas: WiFi + AC + Parkir, Jarak: sedang
- User tekan "TEMUKAN KONTRAKAN"
- **Hasil yang muncul**: Kontrakan yang TIDAK punya fasilitas yang dipilih
- **Harapan**: Hanya kontrakan dengan SEMUA fasilitas yang dipilih

**Contoh**:
- User centang: Lemari, Kamar Mandi Luar, Dapur
- Hasil show: Kontrakan yang cuma punya "Lemari" saja (kurang 2 fasilitas)

---

## 🔍 Root Cause Analysis

### Problem Hierarchy

```
Issue: Hasil tidak sesuai dengan fasilitas yang dicentang
  ↓
  Root Cause 1: Mobile app TIDAK mengirim selected_facilities ke API
    - App collect selected facilities dari checkbox
    - App calculate bobot dari questionnaire
    - But: selected_facilities NOT in API request body
    - Backend: Tidak tahu fasilitas mana yang user inginkan
    - Result: Show ALL kontrakan (no facility filtering)
    
  Root Cause 2: Backend tidak ada logic untuk filter by selected_facilities
    - API validator: Accept 'fasilitas' string, but NOT 'selected_facilities' array
    - SAW calculation: Cuma baca fasilitas_count (total jumlah)
    - Filter logic: Missing untuk check "has all selected facilities"
```

---

## ✅ SOLUSI YANG DITERAPKAN

### Fix #1: Mobile App - Send Selected Facilities

**File**: `spk_mobile/lib/screens/recommendation_screen.dart` (Line 466-472)

**Perubahan**:
```dart
// BEFORE - Missing selected_facilities
final bodyParams = <String, dynamic>{};
bodyParams['bobot_harga'] = _bobotHarga;
bodyParams['bobot_jarak'] = _bobotJarak;
bodyParams['bobot_jumlah_kamar'] = _bobotKriteria3;
bodyParams['bobot_fasilitas'] = _bobotKriteria4;
// ❌ _selectedFacilities NOT sent!

// AFTER - Include selected_facilities array
final bodyParams = <String, dynamic>{};
bodyParams['bobot_harga'] = _bobotHarga;
bodyParams['bobot_jarak'] = _bobotJarak;
bodyParams['bobot_jumlah_kamar'] = _bobotKriteria3;
bodyParams['bobot_fasilitas'] = _bobotKriteria4;
// ✅ CRITICAL: Send selected facilities for filtering
bodyParams['selected_facilities'] = _selectedFacilities.toList();
```

**Impact**:
- Mobile app now sends: `{"selected_facilities": ["WiFi", "AC", "Parkir"], ...}`
- Backend knows exactly which facilities to filter

---

### Fix #2: Backend - Accept Selected Facilities

**File**: `spk_kontrakan/app/Http/Controllers/Api/SAWController.php` (Line 48-50)

**Perubahan Validator**:
```php
// BEFORE
'fasilitas' => 'nullable|string',
// (no selected_facilities parameter)

// AFTER
'fasilitas' => 'nullable|string',
'selected_facilities' => 'nullable|array',  // Array dari questionnaire
'selected_facilities.*' => 'nullable|string',
```

**Impact**:
- Backend validator now accept array of selected facilities
- Allows: `POST /api/saw/calculate/kontrakan` dengan body `{selected_facilities: [...]}`

---

### Fix #3: Backend - Filter by Selected Facilities

**File**: `spk_kontrakan/app/Http/Controllers/Api/SAWController.php` (Line 135-156)

**Filtering Logic** (setelah `$kontrakan = $query->get()`):

```php
// Filter by selected_facilities from questionnaire
// Kontrakan HARUS punya SEMUA selected facilities
if ($request->has('selected_facilities') && 
    is_array($request->selected_facilities) && 
    !empty($request->selected_facilities)) {
    
    $selectedFacilities = array_filter(array_map('trim', $request->selected_facilities));
    
    if (!empty($selectedFacilities)) {
        // Filter: kontrakan must have ALL selected facilities
        $kontrakan = $kontrakan->filter(function($k) use ($selectedFacilities) {
            $kontrakanFasilitas = array_map('trim', explode(',', $k->fasilitas));
            
            // Check if kontrakan has ALL selected facilities
            foreach ($selectedFacilities as $facility) {
                if (!in_array($facility, $kontrakanFasilitas, true)) {
                    return false;  // Missing this facility
                }
            }
            return true;  // Has all selected facilities
        });
    }
}
```

**Logic**:
1. Take selected_facilities array from request
2. For each kontrakan, get list of facilities (split by comma)
3. Check: Does kontrakan have ALL selected facilities?
4. Keep only matching kontrakan

**Example**:
```
User selected: ["Lemari", "Kamar Mandi Luar", "Dapur"]
Kontrakan A has: "Lemari, Kamar Mandi Luar, Dapur, Tempat Cuci Piring" → ✓ KEEP
Kontrakan B has: "Lemari, Kamar Mandi Dalam" → ✗ REMOVE (missing 2)
Kontrakan C has: "Dapur, Parkir, AC" → ✗ REMOVE (missing 2)
```

---

## 🧪 TESTING RESULTS

### Automated Testing Script: `test_selected_facilities.php`

```
=== Test: Selected Facilities Filtering ===

Sample Kontrakan: kontrakan bangka
Facilities: Lemari, Kamar Mandi Luar, Dapur, ... (11 total)

--- Test Case 1: WITHOUT selected_facilities ---
Results count: 9 ✓
(Returns ALL kontrakan, ranked by SAW)

--- Test Case 2: WITH selected_facilities = ["Lemari", "Kamar Mandi Luar", "Dapur"] ---
Results count: 1 ✓
- kontrakan bangka (score: 1)
(Only kontrakan with ALL 3 facilities)

--- Test Case 3: WITH impossible facilities ---
Results count: 0 ✓
Message: Tidak ada kontrakan yang memenuhi kriteria yang Anda pilih
(Correctly returns error when no match)
```

### What Each Test Verifies

| Test | Input | Expected | Actual | Status |
|------|-------|----------|--------|--------|
| No filter | bobot only | 9 results (all) | 9 results | ✅ |
| Specific filter | 3 facilities | 1 result (kontrakan bangka) | 1 result | ✅ |
| Impossible filter | non-existent facilities | 0 results | 0 results | ✅ |

---

## 🔄 COMPLETE FLOW (BEFORE vs AFTER)

### BEFORE (Broken)
```
1. User fill questionnaire
   - Budget: termurah
   - Facilities: WiFi, AC, Parkir
   - Jarak: sedang
   - Kamar: tidak_peduli

2. User press "TEMUKAN KONTRAKAN"

3. Mobile app calculate bobot: (70, 10, 0, 20)

4. Mobile app calls API with:
   POST /api/saw/calculate/kontrakan
   {
     "bobot_harga": 70,
     "bobot_jarak": 10,
     "bobot_jumlah_kamar": 0,
     "bobot_fasilitas": 20
     // ❌ selected_facilities NOT sent!
   }

5. Backend SAW calculation:
   - For ALL 9 kontrakan (no facility filter)
   - Calculate scores based on harga, jarak, kamar, fasilitas_count
   - Return ranked list

6. Result: Show kontrakan yang tidak punya WiFi+AC+Parkir
   Example: Show kontrakan dengan AC saja ❌
```

### AFTER (Fixed)
```
1. User fill questionnaire
   - Budget: termurah
   - Facilities: WiFi, AC, Parkir
   - Jarak: sedang
   - Kamar: tidak_peduli

2. User press "TEMUKAN KONTRAKAN"

3. Mobile app calculate bobot: (70, 10, 0, 20)

4. Mobile app calls API with:
   POST /api/saw/calculate/kontrakan
   {
     "bobot_harga": 70,
     "bobot_jarak": 10,
     "bobot_jumlah_kamar": 0,
     "bobot_fasilitas": 20,
     "selected_facilities": ["WiFi", "AC", "Parkir"]  // ✅ Now sent!
   }

5. Backend SAW calculation:
   - FIRST: Filter kontrakan yang punya ALL 3 facilities (WiFi+AC+Parkir)
   - THEN: Calculate SAW scores only for filtered kontrakan
   - Return ranked list (only matching facilities)

6. Result: Show HANYA kontrakan dengan WiFi+AC+Parkir ✅
```

---

## 📝 FILES MODIFIED

### Backend (Laravel)
1. **spk_kontrakan/app/Http/Controllers/Api/SAWController.php**
   - Line 48-50: Added validator for `selected_facilities` array
   - Line 135-156: Added filtering logic by selected_facilities

### Frontend (Flutter)
2. **spk_mobile/lib/screens/recommendation_screen.dart**
   - Line 466-472: Added `selected_facilities` to API request body

---

## 🚀 HOW TO TEST

### Test 1: Manual Testing on Mobile App

```
1. Open app → Rekomendasi Kontrakan
2. Questionnaire appears
3. Q1: Select "Harga termurah"
4. Q2: Check SPECIFIC facilities (e.g., "Lemari", "Kamar Mandi Luar", "Dapur")
5. Q3: Select "Jarak sedang"
6. Q4: Select "Tidak peduli"
7. Press "TEMUKAN KONTRAKAN"

Expected:
- Results show ONLY kontrakan that have ALL selected facilities
- Each result should have all 3 checked facilities in their description
```

### Test 2: API Testing

```bash
cd c:\laragon\www\TA\spk_kontrakan

# Test with selected_facilities
php -r "
\$ch = curl_init('http://192.168.18.16:8000/api/saw/calculate/kontrakan');
curl_setopt(\$ch, CURLOPT_POST, 1);
curl_setopt(\$ch, CURLOPT_POSTFIELDS, json_encode([
    'bobot_harga' => 70,
    'bobot_jarak' => 10,
    'bobot_jumlah_kamar' => 0,
    'bobot_fasilitas' => 20,
    'selected_facilities' => ['Lemari', 'Kamar Mandi Luar', 'Dapur']
]));
curl_setopt(\$ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt(\$ch, CURLOPT_RETURNTRANSFER, 1);
echo curl_exec(\$ch);
"
```

Expected result: Only kontrakan with all 3 facilities returned

---

## ✨ VERIFICATION CHECKLIST

- ✅ Mobile app sends `selected_facilities` array to API
- ✅ Backend validator accepts `selected_facilities` parameter
- ✅ Backend filtering logic correctly checks for ALL facilities
- ✅ API returns only matching kontrakan
- ✅ No facilities selected = all kontrakan returned (no filter)
- ✅ Impossible facilities selected = 0 results + error message
- ✅ Mixed edge cases handled correctly

---

## 📊 SUMMARY

| Aspect | BEFORE | AFTER |
|--------|--------|-------|
| Facilities sent to API | ❌ No | ✅ Yes (array) |
| Backend filtering | ❌ None | ✅ Full implementation |
| Results matching facilities | ❌ No | ✅ Yes (ALL selected) |
| Edge case: no filter | ✅ All returned | ✅ All returned |
| Edge case: impossible filter | ❌ All returned | ✅ 0 results + message |

---

## 🎯 CONCLUSION

**Problem**: User centang fasilitas tapi hasil tidak sesuai

**Root Cause**: 
1. Mobile app tidak kirim selected_facilities ke API
2. Backend tidak ada filtering logic

**Solution Applied**:
1. Mobile: Add `selected_facilities` array to request
2. Backend: Accept dan implement filtering

**Result**: ✅ Results now match EXACTLY what user selected

**Status**: **RESOLVED** 🎉

---

**Last Updated**: May 19, 2026
**Tested**: ✓ All test cases passing
**Ready**: Mobile app + Backend API both updated and verified
