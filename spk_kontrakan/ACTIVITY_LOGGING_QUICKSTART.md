# Activity Logging - Quick Start Guide

## 🎯 What is Activity Logging?

Activity logging is a system that **automatically records every action** users perform in your application. This includes:
- Creating new records
- Editing existing records
- Deleting records
- Exporting data

Every action is saved with:
- **Who** performed it (user)
- **What** was done (action type)
- **When** it happened (timestamp)
- **Where** it happened (which model/table)
- **How much** changed (old vs new values)

---

## ✅ What Gets Logged?

### Kontrakan Operations
| Operation | Logged As |
|-----------|-----------|
| Create new kontrakan | ✅ Yes - "Membuat kontrakan baru: {nama}" |
| Update kontrakan | ✅ Yes - "Memperbarui kontrakan: {nama}" |
| Delete kontrakan | ✅ Yes - "Menghapus kontrakan: {nama}" |
| Bulk delete kontrakan | ✅ Yes - Logged for each item |

### Laundry Operations
| Operation | Logged As |
|-----------|-----------|
| Create new laundry | ✅ Yes - "Membuat laundry baru: {nama}" |
| Update laundry | ✅ Yes - "Memperbarui laundry: {nama}" |
| Delete laundry | ✅ Yes - "Menghapus laundry: {nama}" |
| Bulk delete laundry | ✅ Yes - Logged for each item |

### Export Operations
| Operation | Logged As |
|-----------|-----------|
| Export kontrakan to Excel | ✅ Yes - "Export data Kontrakan ke Excel (X items)" |
| Export kontrakan to PDF | ✅ Yes - "Export data Kontrakan ke PDF (X items)" |
| Export laundry to Excel | ✅ Yes - "Export data Laundry ke Excel (X items)" |
| Export laundry to PDF | ✅ Yes - "Export data Laundry ke PDF (X items)" |
| Export SAW results to Excel | ✅ Yes - "Export hasil SAW ke Excel (type)" |
| Export SAW results to PDF | ✅ Yes - "Export hasil SAW ke PDF (type)" |

### User Management
| Operation | Logged As |
|-----------|-----------|
| Create user | ✅ Yes - "Membuat user baru" |
| Update user | ✅ Yes - "Memperbarui user" |
| Delete user | ✅ Yes - "Menghapus user" |
| Restore user | ✅ Yes - "Mengembalikan user" |

---

## 🔍 How to View Activity Logs

### Step 1: Access Admin Panel
1. Login with your admin account
2. Click the dropdown menu in top-right corner
3. Select **"Admin"** → **"Activity Logs"**

### Step 2: View All Logs
- You'll see a table with all activities
- Columns show:
  - **Timestamp**: When the action happened
  - **User**: Who did it
  - **Action**: Type of action (create, update, delete, export)
  - **Description**: What was done
  - **Model**: What was affected (Kontrakan, Laundry, User, etc.)
  - **IP Address**: Where they were accessing from

### Step 3: Filter Logs
Use the filter section to find specific logs:

**By User**:
```
Select user from dropdown → Click Filter
```

**By Action Type**:
```
Select action: Create, Update, Delete, Export, Login
```

**By Model Type**:
```
Select: Kontrakan, Laundry, User, SAW
```

**By Date Range**:
```
Select start date and end date → Click Filter
```

### Step 4: Export Logs
```
Click "Export to CSV" button
→ Opens in Excel or Google Sheets
→ Contains all filtered logs
```

---

## 💾 What Data is Stored?

### For Create Operations
```
{
  user: "Admin Name",
  action: "create",
  description: "Membuat kontrakan baru: Rumah Nyaman",
  model_type: "Kontrakan",
  model_id: 123,
  timestamp: "2025-01-15 10:30:45",
  new_values: {
    nama: "Rumah Nyaman",
    alamat: "Jl. Raya No. 10",
    harga: 1500000,
    ...
  }
}
```

### For Update Operations
```
{
  user: "Admin Name",
  action: "update",
  description: "Memperbarui kontrakan: Rumah Nyaman",
  model_type: "Kontrakan",
  model_id: 123,
  timestamp: "2025-01-15 11:45:20",
  old_values: {
    harga: 1500000,
    jumlah_kamar: 3
  },
  new_values: {
    harga: 1600000,
    jumlah_kamar: 4
  }
}
```

### For Delete Operations
```
{
  user: "Admin Name",
  action: "delete",
  description: "Menghapus kontrakan: Rumah Nyaman",
  model_type: "Kontrakan",
  model_id: 123,
  timestamp: "2025-01-15 12:00:00",
  old_values: {
    // Complete kontrakan data that was deleted
  }
}
```

### For Export Operations
```
{
  user: "Admin Name",
  action: "export",
  description: "Export data Kontrakan ke Excel (15 items)",
  model_type: "Kontrakan",
  model_id: null, // No specific model for bulk exports
  timestamp: "2025-01-15 13:15:30"
}
```

---

## 🎯 Common Use Cases

### Use Case 1: Track Who Deleted a Record
```
1. Go to Admin → Activity Logs
2. Select Model Type: "Kontrakan"
3. Select Action: "delete"
4. Click Filter
5. See who deleted which kontrakan and when
```

### Use Case 2: View What Changed in an Update
```
1. Go to Admin → Activity Logs
2. Select User: "Manager Name"
3. Select Action: "update"
4. Click on the log entry
5. See "Old Values" and "New Values" side by side
6. Understand exactly what was changed
```

### Use Case 3: Monitor Export Activity
```
1. Go to Admin → Activity Logs
2. Select Action: "export"
3. Click Filter
4. See all exports with count and timestamp
5. Identify which data was exported and by whom
```

### Use Case 4: Generate Audit Report
```
1. Go to Admin → Activity Logs
2. Set Date Range: "Jan 1 - Jan 31"
3. Click Filter
4. Click "Export to CSV"
5. Use in Excel for reporting
```

---

## 🔐 Security Benefits

### 1. Accountability
- Every action is attributed to a user
- Users know their actions are tracked
- Encourages responsible data management

### 2. Audit Trail
- Track who did what, when, and where
- Required for compliance and regulations
- Useful for investigating issues

### 3. Data Recovery
- If data is accidentally deleted, you know:
  - When it was deleted
  - Who deleted it
  - What was in it (stored in old_values)
- Can manually restore if needed

### 4. Security Monitoring
- IP addresses logged for each action
- User agent information recorded
- Detect suspicious access patterns

### 5. Forensic Analysis
- Investigate data breaches or unauthorized access
- Timeline of events for compliance investigations
- Evidence for security audits

---

## ⚙️ Technical Details

### Database Table
```sql
CREATE TABLE activity_logs (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT,
  action VARCHAR(255),
  description TEXT,
  model_type VARCHAR(255),
  model_id BIGINT NULL,
  old_values JSON NULL,
  new_values JSON NULL,
  ip_address VARCHAR(45),
  user_agent TEXT,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  
  INDEX idx_user_id (user_id),
  INDEX idx_created_at (created_at),
  INDEX idx_action (action),
  INDEX idx_model_type (model_type)
);
```

### How It Works
```
User Action
    ↓
Controller Method (e.g., store(), update(), destroy())
    ↓
ActivityLog::log() called
    ↓
Data stored in activity_logs table
    ↓
Available in Admin Panel for viewing
```

### Code Example
```php
// When user creates kontrakan
$kontrakan = Kontrakan::create([...]);

// This is called automatically
ActivityLog::log(
    'create',                              // Action
    "Membuat kontrakan baru: {$kontrakan->nama}", // Description
    'Kontrakan',                          // Model type
    $kontrakan->id                        // Model ID
);

// Log entry created in database
// User can see it in Admin → Activity Logs
```

---

## 📊 Example Reports

### Monthly Activity Report
```
Date Range: January 2025
Total Actions: 156

By Action Type:
- Create: 42
- Update: 78
- Delete: 12
- Export: 24

By Model Type:
- Kontrakan: 98
- Laundry: 45
- User: 13

By User:
- Admin A: 95 actions
- Admin B: 61 actions
```

### Delete Activity Report
```
Month: January 2025
Total Deletions: 12

By Model:
- Kontrakan: 8 items
- Laundry: 4 items

Detail:
- Jan 5: Admin A deleted "Rumah Nyaman"
- Jan 12: Admin B deleted "Laundry Express"
- ...
```

### Export Activity Report
```
Month: January 2025
Total Exports: 24

By Format:
- Excel: 14
- PDF: 10

By Data Type:
- Kontrakan: 15
- Laundry: 9

Peak Hour: 10:00 AM (5 exports)
```

---

## ❓ FAQ

**Q: Can I delete activity logs?**
A: Only super_admin can. Go to Admin → Activity Logs → Click "Clear Logs" button. Use with caution - this is permanent!

**Q: How long are logs kept?**
A: Indefinitely. They're never auto-deleted. This is important for compliance and auditing.

**Q: Can I see what another user did?**
A: Yes, if you're super_admin. Filter by user to see their activities.

**Q: What if I need logs from 2 years ago?**
A: All logs are permanent unless manually cleared. You can filter by date range to find them.

**Q: Does logging slow down the system?**
A: No, logging is non-blocking and doesn't impact performance.

**Q: Can users see their own activity logs?**
A: Only super_admin can view logs. Regular users cannot.

**Q: What if there's a password change?**
A: Password changes are logged, but the actual password is never logged (only that it was changed).

---

## 🚀 Best Practices

### For Administrators
1. **Regular Review**: Check activity logs weekly
2. **Monitor Deletions**: Alert on suspicious bulk deletions
3. **Track Exports**: Monitor who exports data and when
4. **User Compliance**: Ensure users are following policies
5. **Backup Logs**: Regularly export and archive logs

### For Security
1. **Audit Trail**: Keep logs for at least 1 year
2. **Access Control**: Only show logs to admin users
3. **IP Monitoring**: Track unusual IP addresses
4. **Anomaly Detection**: Set up alerts for suspicious patterns

### For Compliance
1. **Documentation**: Keep printed reports of activity
2. **Data Retention**: Document your log retention policy
3. **Audit Ready**: Always be able to generate reports
4. **User Agreement**: Inform users that activity is logged

---

## 🎓 Learning Path

1. **Beginner**: Learn to view and filter activity logs
2. **Intermediate**: Understand log data structure and CSV export
3. **Advanced**: Use logs for security analysis and reporting
4. **Expert**: Integrate logs with external monitoring systems

---

## 📞 Support

For questions about activity logging:
1. Check the admin panel help (?) icons
2. Review ACTIVITY_LOGGING_INTEGRATION.md
3. Check application logs in `/storage/logs/laravel.log`
4. Contact system administrator

---

**Last Updated**: 2025
**Version**: 2.5
**Status**: ✅ Active and Recording

---

*Start using activity logs today to enhance security, compliance, and accountability in your system!*
