# TSU Staff Portal - ID Card System Guide

## Overview

The ID Card System allows administrators to generate professional staff ID cards with QR codes for all TSU staff members.

## Features

### 1. Staff Number Management
- **Format:** TSU/SP/### or TSU/JP/###
- **SP:** Senior Personnel
- **JP:** Junior Personnel
- **Required:** During registration and profile setup
- **Unique:** Each staff member has a unique ID

### 2. ID Card Design

**Front Side:**
- TSU Logo
- Staff Photo (or initials if no photo)
- Full Name (including middle name)
- Job Title/Designation
- Staff ID Number
- Faculty
- Department
- Email Address
- Issue Date

**Back Side:**
- QR Code (links to public profile)
- Profile URL
- Security Unit Information
- Return instructions

### 3. QR Code System
- **Auto-generated** for each profile
- **Links to:** Public profile page
- **Stored in:** `storage/qrcodes/`
- **Format:** PNG image (300x300px)
- **APIs Used:** QRServer (primary), QuickChart (backup)

## User Guide

### For Staff Members

#### Setting Up Your Profile
1. Register with your TSU email
2. Select staff ID prefix (TSU/SP/ or TSU/JP/)
3. Enter your staff ID number
4. Complete profile setup
5. Upload profile photo (recommended for ID card)

#### Updating Your Information
1. Login to your account
2. Go to "Edit Profile"
3. Update your information
4. Save changes
5. Admin will regenerate ID card if needed

### For Administrators

#### Generating ID Cards

**Single ID Card:**
1. Login as admin
2. Go to Admin Dashboard
3. Click "ID Cards" in sidebar
4. Find the staff member
5. Click "Generate ID Card"
6. Preview, Print, or Download

**Bulk Generation:**
1. Go to ID Cards page
2. Use filters to find users
3. Select multiple users (checkboxes)
4. Click "Generate ID Cards"
5. System generates all cards
6. Download or print as needed

#### Filtering Users
- **All Users:** Show everyone
- **With ID Cards:** Users who have QR codes
- **Without ID Cards:** Users missing QR codes
- **Search:** By name, email, or staff ID

#### Managing ID Cards

**Regenerate QR Code:**
- Click "Regenerate QR" button
- New QR code is created
- Old QR code is deleted
- Database is updated

**Update Profile:**
- Staff updates their profile
- Admin regenerates ID card
- New card reflects changes

## Technical Details

### File Structure
```
app/
├── Controllers/
│   └── IDCardController.php      # ID card logic
├── Helpers/
│   └── QRCodeHelper.php          # QR code generation
└── Views/
    └── admin/
        ├── id-card-generator.php # User selection
        └── id-card-preview.php   # Card preview/print

storage/
└── qrcodes/                      # QR code images
    └── qr_{user_id}_{timestamp}.png

routes/
└── web.php                       # ID card routes
```

### Database Schema
```sql
profiles table:
- staff_number VARCHAR(50) UNIQUE
- qr_code_path VARCHAR(255)
- profile_photo VARCHAR(255)
- first_name, middle_name, last_name
- title, designation
- faculty, department
```

### Routes
```
GET  /admin/id-cards                    # List users
GET  /admin/id-cards/preview/{id}       # Preview card
POST /admin/id-cards/generate/{id}      # Generate single
POST /admin/id-cards/bulk-generate      # Generate multiple
POST /admin/id-cards/regenerate-qr/{id} # Regenerate QR
GET  /qrcode/{filename}                 # Serve QR image
```

### API Endpoints

**QR Code Generation:**
- Primary: `https://api.qrserver.com/v1/create-qr-code/`
- Backup: `https://quickchart.io/qr`
- No API key required
- Free tier sufficient

## Printing Guidelines

### Recommended Specifications
- **Card Size:** 3.5" x 5.5" (standard ID card)
- **Paper:** 300gsm card stock
- **Finish:** Glossy or matte lamination
- **Color:** Full color (CMYK)
- **Resolution:** 300 DPI minimum

### Printing Process
1. Generate ID card
2. Click "Print" button
3. Select printer settings:
   - Paper size: Custom (3.5" x 5.5")
   - Orientation: Portrait
   - Quality: Best/High
   - Color: Color
4. Print front side
5. Flip card
6. Print back side
7. Laminate for durability

### Professional Printing
For bulk printing:
1. Download all cards as PDF
2. Send to professional printer
3. Specify card stock and lamination
4. Request hole punch for lanyard (optional)

## Troubleshooting

### QR Code Not Generating
**Symptoms:** No QR code on back of card

**Solutions:**
1. Check internet connection (API access needed)
2. Verify `storage/qrcodes/` folder exists
3. Check folder permissions (755)
4. Check error log for API errors
5. Try regenerating QR code

### Profile Photo Not Showing
**Symptoms:** Initials show instead of photo

**Solutions:**
1. Verify photo uploaded in profile
2. Check photo file exists in storage
3. Check file path in database
4. Clear browser cache
5. Re-upload photo

### Staff Number Issues
**Symptoms:** Can't save staff number

**Solutions:**
1. Check database migration ran
2. Verify unique constraint
3. Check for duplicate numbers
4. Use correct format (TSU/SP/### or TSU/JP/###)

### Download PDF Not Working
**Symptoms:** PDF download fails

**Solutions:**
1. Check browser console for errors
2. Verify JavaScript libraries load
3. Try Print > Save as PDF
4. Use different browser
5. Check popup blocker

## Best Practices

### For Staff
- ✅ Upload high-quality profile photo
- ✅ Keep profile information updated
- ✅ Use professional photo (passport style)
- ✅ Verify staff number is correct
- ✅ Complete all required fields

### For Administrators
- ✅ Generate ID cards after profile approval
- ✅ Verify information before printing
- ✅ Keep backup of generated cards
- ✅ Regenerate cards when info changes
- ✅ Test QR codes before printing
- ✅ Use bulk generation for efficiency

## Security Considerations

### Access Control
- Only admins can generate ID cards
- Staff can only edit their own profile
- QR codes are public (link to public profile)

### Data Privacy
- Profile photos stored securely
- Staff numbers are unique identifiers
- QR codes don't contain sensitive data
- Public profiles show only approved info

### Physical Security
- Laminate cards to prevent tampering
- Include security features if needed
- Report lost cards immediately
- Deactivate accounts of former staff

## Maintenance

### Regular Tasks
- **Weekly:** Check error log for issues
- **Monthly:** Verify QR codes work
- **Quarterly:** Audit staff numbers
- **Annually:** Regenerate all cards

### Storage Management
```bash
# Check QR code storage
du -sh storage/qrcodes/

# Clean old QR codes (if needed)
find storage/qrcodes/ -mtime +365 -delete

# Backup QR codes
tar -czf qrcodes_backup.tar.gz storage/qrcodes/
```

## Support

### Common Questions

**Q: Can staff generate their own ID cards?**
A: No, only administrators can generate ID cards.

**Q: How long does QR code generation take?**
A: Usually 1-2 seconds per card.

**Q: Can I customize the ID card design?**
A: Yes, edit `app/Views/admin/id-card-preview.php`

**Q: What if QR code API is down?**
A: System automatically tries backup API (QuickChart)

**Q: Can I print multiple cards at once?**
A: Yes, use bulk generation feature

### Getting Help
1. Check error log: `error.log`
2. Review this guide
3. Check deployment checklist
4. Contact technical support

---

**Last Updated:** February 7, 2026
**Version:** 1.0
**System:** TSU Staff Profile Portal
