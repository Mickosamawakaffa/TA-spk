# 📑 Complete File Reference - API Integration

Panduan lengkap untuk menemukan file yang Anda butuhkan.

---

## 📁 File Structure

```
c:\laragon\www\TA\
├── spk_kontrakan/              ← Backend API (Laravel)
│   └── php artisan serve       ✓ Running on http://127.0.0.1:8000
│
└── spk_mobile/                 ← Flutter App
    ├── lib/
    │   ├── services/
    │   │   ├── auth_service.dart           ✅ Ready
    │   │   ├── kontrakan_service.dart      ✅ Ready
    │   │   ├── booking_service.dart        ✅ Ready
    │   │   ├── laundry_service.dart        ✅ Ready
    │   │   ├── review_service.dart         ✅ NEW
    │   │   ├── favorite_service.dart       ✅ NEW
    │   │   └── location_service.dart       ✅ Existing
    │   ├── config/
    │   │   └── app_config.dart             ← UPDATE THIS FIRST!
    │   ├── models/
    │   │   ├── booking.dart
    │   │   ├── kontrakan.dart
    │   │   ├── laundry.dart
    │   │   ├── user.dart
    │   │   └── ...
    │   ├── screens/
    │   │   ├── login.dart
    │   │   ├── register.dart
    │   │   └── ... (to be implemented)
    │   └── main.dart            ← ADD LOADER HERE
    ├── test/
    │   └── api_test_helper.dart  ✅ NEW - TESTING UTILITY
    │
    ├── GETTING_STARTED.md        ✅ NEW - START HERE!
    ├── README_API_INTEGRATION.md ✅ NEW - Overview
    ├── API_QUICKSTART.md         ✅ NEW - Quick Examples
    ├── API_INTEGRATION_GUIDE.md  ✅ NEW - Full Reference
    ├── API_STATUS.md             ✅ NEW - Feature Status
    ├── SCREEN_IMPLEMENTATION_CHECKLIST.md  ✅ NEW - Screen Guides
    ├── WHAT_WAS_CREATED.md       ✅ NEW - Summary
    └── pubspec.yaml              (no changes needed)
```

---

## 🎯 Which File to Read?

### Just Started?
👉 Read: [`GETTING_STARTED.md`](GETTING_STARTED.md)
- 3-step quick start
- Test your setup
- First example code

### Want Overview?
👉 Read: [`README_API_INTEGRATION.md`](README_API_INTEGRATION.md)
- What's included
- All services summary
- Learning path

### Need Code Examples?
👉 Read: [`API_QUICKSTART.md`](API_QUICKSTART.md)
- 15+ working examples
- Common use cases
- Widget integration

### Building a Screen?
👉 Read: [`SCREEN_IMPLEMENTATION_CHECKLIST.md`](SCREEN_IMPLEMENTATION_CHECKLIST.md)
- Step-by-step for 11 screens
- Code templates
- Test cases

### Need Full Reference?
👉 Read: [`API_INTEGRATION_GUIDE.md`](API_INTEGRATION_GUIDE.md)
- All endpoints documented
- Error handling patterns
- Troubleshooting

### Checking Status?
👉 Read: [`API_STATUS.md`](API_STATUS.md)
- What's implemented
- What's ready
- Implementation checklist

### Want to Know What's New?
👉 Read: [`WHAT_WAS_CREATED.md`](WHAT_WAS_CREATED.md)
- Summary of changes
- New services
- New documentation
- Statistics

---

## 🔧 Which File to Edit?

### To Change API URL
**File**: `lib/config/app_config.dart`
```dart
static const String baseUrl = 'http://192.168.XX.XX:8000/api';
                                                      ↑
                                        Update this IP!
```

### To Load Token on Startup
**File**: `lib/main.dart`
```dart
void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  
  final authService = AuthService();
  await authService.loadToken();  ← ADD THIS
  
  runApp(const MyApp());
}
```

### To Use Services in Your Screens
**File**: `lib/screens/your_screen.dart`
```dart
import 'package:spk_mobile/services/xxx_service.dart';  ← Use these imports

final service = XxxService();
final data = await service.methodName();
```

---

## 📋 Services Reference

### 1. AuthService
**File**: `lib/services/auth_service.dart`
**Documentation**: See API_QUICKSTART.md (Login section)
**Example**: SCREEN_IMPLEMENTATION_CHECKLIST.md (Login Screen)

### 2. KontrakanService
**File**: `lib/services/kontrakan_service.dart`
**Documentation**: API_INTEGRATION_GUIDE.md (Kontrakan Service)
**Example**: SCREEN_IMPLEMENTATION_CHECKLIST.md (Kontrakan List)

### 3. BookingService
**File**: `lib/services/booking_service.dart`
**Documentation**: API_INTEGRATION_GUIDE.md (Booking Service)
**Example**: API_QUICKSTART.md (Create Booking section)

### 4. LaundryService
**File**: `lib/services/laundry_service.dart`
**Documentation**: API_INTEGRATION_GUIDE.md (Laundry Service)
**Example**: API_QUICKSTART.md (Get Laundry section)

### 5. ReviewService ⭐ NEW
**File**: `lib/services/review_service.dart`
**Documentation**: API_INTEGRATION_GUIDE.md (Review Service)
**Example**: API_QUICKSTART.md (Add Review section)

### 6. FavoriteService ⭐ NEW
**File**: `lib/services/favorite_service.dart`
**Documentation**: API_INTEGRATION_GUIDE.md (Favorite Service)
**Example**: API_QUICKSTART.md (Toggle Favorite section)

---

## 🧪 Testing

### To Test API Connection
**File**: `test/api_test_helper.dart`

**Run All Tests**:
```dart
await APITestHelper.runAllTests(
  testEmail: 'test@example.com',
  testPassword: 'password123',
);
```

**Test Specific Endpoint**:
```dart
await APITestHelper.testGetKontrakan();
await APITestHelper.testLogin(email: '...', password: '...');
```

See `API_QUICKSTART.md` for more test examples.

---

## 📖 Documentation Files Path

| File | Location | Purpose |
|------|----------|---------|
| GETTING_STARTED.md | `/spk_mobile/` | Quick start (3 steps) |
| README_API_INTEGRATION.md | `/spk_mobile/` | Overview & summary |
| API_QUICKSTART.md | `/spk_mobile/` | Code examples & patterns |
| API_INTEGRATION_GUIDE.md | `/spk_mobile/` | Complete reference |
| API_STATUS.md | `/spk_mobile/` | Status & checklist |
| SCREEN_IMPLEMENTATION_CHECKLIST.md | `/spk_mobile/` | Screen-by-screen guide |
| WHAT_WAS_CREATED.md | `/spk_mobile/` | Summary of changes |

---

## 📚 Reading Order (Recommended)

### First Time Users
1. [`GETTING_STARTED.md`](GETTING_STARTED.md) - 10 min
2. [`README_API_INTEGRATION.md`](README_API_INTEGRATION.md) - 15 min
3. [`API_QUICKSTART.md`](API_QUICKSTART.md) - Browse examples

### Ready to Code
1. Pick a screen from [`SCREEN_IMPLEMENTATION_CHECKLIST.md`](SCREEN_IMPLEMENTATION_CHECKLIST.md)
2. Follow the code template
3. Refer to [`API_QUICKSTART.md`](API_QUICKSTART.md) for similar examples
4. Use [`API_INTEGRATION_GUIDE.md`](API_INTEGRATION_GUIDE.md) as reference

### Need Detailed Info
- [`API_INTEGRATION_GUIDE.md`](API_INTEGRATION_GUIDE.md) - All details
- [`API_STATUS.md`](API_STATUS.md) - Implementation status

---

## ✅ Implementation Checklist

### Before Coding
- [ ] Read [`GETTING_STARTED.md`](GETTING_STARTED.md)
- [ ] Update IP in `lib/config/app_config.dart`
- [ ] Add loader to `lib/main.dart`
- [ ] Test with [`APITestHelper`](test/api_test_helper.dart)

### Screen Implementation
- [ ] Pick screen from checklist
- [ ] Read guide in [`SCREEN_IMPLEMENTATION_CHECKLIST.md`](SCREEN_IMPLEMENTATION_CHECKLIST.md)
- [ ] See examples in [`API_QUICKSTART.md`](API_QUICKSTART.md)
- [ ] Reference [`API_INTEGRATION_GUIDE.md`](API_INTEGRATION_GUIDE.md) if needed
- [ ] Test your screen
- [ ] Move to next screen

### Before Push/Deployment
- [ ] Check [`API_STATUS.md`](API_STATUS.md) checklist
- [ ] Test all implemented features
- [ ] Update progress in status file

---

## 🆘 Troubleshooting

### "I don't know where to start"
→ Read [`GETTING_STARTED.md`](GETTING_STARTED.md)

### "How do I use a service?"
→ See examples in [`API_QUICKSTART.md`](API_QUICKSTART.md)

### "Which method should I use?"
→ Check [`API_INTEGRATION_GUIDE.md`](API_INTEGRATION_GUIDE.md)

### "How do I build screen X?"
→ Find it in [`SCREEN_IMPLEMENTATION_CHECKLIST.md`](SCREEN_IMPLEMENTATION_CHECKLIST.md)

### "API connection fails"
→ See troubleshooting in [`GETTING_STARTED.md`](GETTING_STARTED.md)

### "I want to know what's new"
→ Read [`WHAT_WAS_CREATED.md`](WHAT_WAS_CREATED.md)

---

## 🎯 Quick Navigation

### By Task

**Setup System**
1. [`GETTING_STARTED.md`](GETTING_STARTED.md) - Step 1-3
2. Test with `test/api_test_helper.dart`

**Learn API Services**
1. [`README_API_INTEGRATION.md`](README_API_INTEGRATION.md) - Review services
2. [`API_INTEGRATION_GUIDE.md`](API_INTEGRATION_GUIDE.md) - Details
3. [`API_QUICKSTART.md`](API_QUICKSTART.md) - Examples

**Build Screens**
1. [`SCREEN_IMPLEMENTATION_CHECKLIST.md`](SCREEN_IMPLEMENTATION_CHECKLIST.md) - Pick screen
2. [`API_QUICKSTART.md`](API_QUICKSTART.md) - Find similar example
3. Code your screen
4. Test with widget

**Test Everything**
1. `test/api_test_helper.dart` - API tests
2. Manual screen testing - User interactions
3. [`API_STATUS.md`](API_STATUS.md) - Verification checklist

### By Role

**Project Manager**
- [`README_API_INTEGRATION.md`](README_API_INTEGRATION.md) - Overview
- [`WHAT_WAS_CREATED.md`](WHAT_WAS_CREATED.md) - What's done
- [`API_STATUS.md`](API_STATUS.md) - Status & checklist

**Developer (Beginner)**
- [`GETTING_STARTED.md`](GETTING_STARTED.md) - Start here
- [`API_QUICKSTART.md`](API_QUICKSTART.md) - Examples
- [`SCREEN_IMPLEMENTATION_CHECKLIST.md`](SCREEN_IMPLEMENTATION_CHECKLIST.md) - Implementation guide

**Developer (Experienced)**
- [`API_INTEGRATION_GUIDE.md`](API_INTEGRATION_GUIDE.md) - Reference
- Source code in `lib/services/` - Implementation details

**QA/Tester**
- [`API_STATUS.md`](API_STATUS.md) - Checklist
- `test/api_test_helper.dart` - Automated tests
- [`SCREEN_IMPLEMENTATION_CHECKLIST.md`](SCREEN_IMPLEMENTATION_CHECKLIST.md) - Test cases

---

## 🔑 Key Files to Remember

| Priority | File | What It Is |
|----------|------|-----------|
| 🔴 Critical | `lib/config/app_config.dart` | API URL config (MUST UPDATE!) |
| 🔴 Critical | `lib/main.dart` | App entry (ADD LOADER!) |
| 🟡 Important | `SCREEN_IMPLEMENTATION_CHECKLIST.md` | Implementation guide |
| 🟡 Important | `API_QUICKSTART.md` | Code examples |
| 🟢 Reference | `API_INTEGRATION_GUIDE.md` | Full API reference |
| 🟢 Reference | `test/api_test_helper.dart` | Testing utility |

---

## 📞 File Navigation Guide

**Need to understand a service?**
→ `API_INTEGRATION_GUIDE.md` → Find service name

**Need to see code example?**
→ `API_QUICKSTART.md` → Search by feature

**Need to build a screen?**
→ `SCREEN_IMPLEMENTATION_CHECKLIST.md` → Find screen number

**Need to configure app?**
→ `lib/config/app_config.dart` → Update baseUrl

**Need to test API?**
→ `test/api_test_helper.dart` → Run test methods

**Need quick start?**
→ `GETTING_STARTED.md` → Follow 3 steps

---

## ✨ Summary

- **6 API Services** ready to use
- **6 Documentation Files** for different needs
- **1 Testing Utility** for validation
- **1 Config File** to update
- **All Ready** for development

---

**Total Files Created/Updated: 13**
- Services: 2 new + 4 existing (6 total)
- Documentation: 6 new
- Testing: 1 new
- Configuration: Already exists
- Implementation: Ready to start

---

**Start with**: [`GETTING_STARTED.md`](GETTING_STARTED.md)
**Then follow**: Implementation guide as needed
**Finally**: Reference documentation when stuck

---

**Happy Coding! 🚀**

*All API integration is complete and documented.*
*Navigate using this file when you need to find something.*
