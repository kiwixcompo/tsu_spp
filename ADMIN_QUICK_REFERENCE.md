# Admin Quick Reference - ID Card System

## Quick Access

**URL:** https://staff.tsuniversity.edu.ng/public/admin/id-cards

**Login:** admin@tsuniversity.edu.ng

---

## Common Tasks

### Generate Single ID Card
1. Admin Dashboard → ID Cards
2. Find staff member (search or scroll)
3. Click "Generate ID Card" button
4. Preview opens automatically
5. Click "Print" or "Download"

### Generate Multiple ID Cards
1. Admin Dashboard → ID Cards
2. Select users (checkboxes)
3. Click "Generate ID Cards" button
4. Wait for processing
5. Download or print each card

### Filter Users
- **All Users:** Shows everyone
- **With ID Cards:** Users who have QR codes
- **Without ID Cards:** Users needing cards
- **Search Box:** Find by name, email, or staff ID

### Regenerate QR Code
1. Go to ID card preview page
2. Click "Regenerate QR" button
3. New QR code is created
4. Old QR code is deleted

---

## Staff Number Format

**Format:** TSU/SP/### or TSU/JP/###

**Examples:**
- TSU/SP/001 (Senior Personnel)
- TSU/JP/123 (Junior Personnel)

**Rules:**
- Must be unique
- Required for all staff
- Set during registration
- Can be edited in profile

---

## ID Card Specifications

**Size:** 3.5" x 5.5" (standard ID card)

**Front Contains:**
- TSU Logo
- Profile Photo
- Full Name (with title)
- Staff ID Number
- Job Designation
- Faculty
- Department
- Email
- Issue Date

**Back Contains:**
- QR Code (links to profile)
- Profile URL
- Security Unit info
- Return instructions

---

## Printing Tips

### For Office Printer
1. Click "Print" button
2. Select printer
3. Paper size: Custom (3.5" x 5.5")
4. Quality: Best/High
5. Print front, flip, print back
6. Laminate for durability

### For Professional Printing
1. Click "Download" button
2. Save PDF file
3. Send to print shop
4. Specify: 300gsm card stock
5. Request lamination
6. Optional: hole punch for lanyard

---

## Troubleshooting

### QR Code Not Showing
- Check internet connection
- Verify storage/qrcodes/ folder exists
- Check folder permissions (755)
- Try regenerating QR code

### Photo Not Showing
- Verify staff uploaded photo
- Check file exists in storage
- Clear browser cache
- Ask staff to re-upload

### Can't Save Staff Number
- Check for duplicate numbers
- Verify format (TSU/SP/### or TSU/JP/###)
- Check database migration ran
- Contact technical support

### Download Not Working
- Check browser allows popups
- Try Print → Save as PDF
- Use different browser
- Check JavaScript is enabled

---

## Keyboard Shortcuts

- **Ctrl+P:** Print current page
- **Ctrl+F:** Search on page
- **Esc:** Close modals
- **Tab:** Navigate form fields

---

## Best Practices

✅ **DO:**
- Verify information before printing
- Test QR codes before bulk printing
- Keep backup of generated cards
- Regenerate cards when info changes
- Use bulk generation for efficiency

❌ **DON'T:**
- Print without verifying information
- Generate cards for incomplete profiles
- Forget to laminate physical cards
- Share admin credentials
- Delete QR code files manually

---

## Status Indicators

🟢 **Active:** User account is active
🔴 **Suspended:** User account suspended
⚪ **Pending:** Email not verified
✅ **Has ID Card:** QR code generated
❌ **No ID Card:** QR code missing

---

## Quick Checks

### Before Generating Cards
- [ ] Profile photo uploaded?
- [ ] All required fields filled?
- [ ] Staff number correct?
- [ ] Email verified?
- [ ] Account active?

### After Generating Cards
- [ ] Photo displays correctly?
- [ ] Name includes middle name?
- [ ] Staff number correct?
- [ ] QR code visible?
- [ ] All information accurate?

---

## Common Questions

**Q: How long does generation take?**
A: 2-5 seconds per card

**Q: Can staff generate their own cards?**
A: No, admin only

**Q: Can I edit the card design?**
A: Contact technical support

**Q: What if QR code doesn't work?**
A: Click "Regenerate QR" button

**Q: Can I print multiple cards at once?**
A: Yes, use bulk generation

**Q: How do I know who needs a card?**
A: Use "Without ID Cards" filter

---

## Emergency Contacts

**Technical Issues:**
- Check error.log file first
- Review troubleshooting section
- Contact IT support

**Database Issues:**
- Contact database administrator
- Don't modify database directly

**Server Issues:**
- Contact hosting support
- Check cPanel for errors

---

## File Locations

**QR Codes:** storage/qrcodes/
**Profile Photos:** storage/uploads/profiles/
**Error Log:** error.log
**Documentation:** See MD files in root

---

## Maintenance Schedule

**Daily:**
- Check for new registrations
- Generate cards for new staff

**Weekly:**
- Review error log
- Verify QR codes work

**Monthly:**
- Audit staff numbers
- Update cards for changed info

**Quarterly:**
- Backup QR codes
- Review system performance

**Annually:**
- Regenerate all cards
- Update card design if needed

---

## Security Reminders

🔒 **Always:**
- Logout when done
- Use strong password
- Don't share credentials
- Verify user identity
- Keep cards secure

🚫 **Never:**
- Share admin access
- Generate cards for unverified users
- Modify database directly
- Delete system files
- Ignore error logs

---

## Quick Commands

### Check Error Log
```bash
tail -f error.log
```

### Check Storage Space
```bash
du -sh storage/qrcodes/
```

### Count Generated Cards
```bash
ls storage/qrcodes/ | wc -l
```

---

## Version Info

**System:** TSU Staff Profile Portal
**Module:** ID Card System
**Version:** 1.0
**Last Updated:** February 7, 2026

---

## Need Help?

1. Check this quick reference
2. Review ID_CARD_SYSTEM_GUIDE.md
3. Check DEPLOYMENT_CHECKLIST.md
4. Review error.log
5. Contact technical support

---

**Print this page and keep it handy!**
