# 🔍 TOAST NOTIFICATIONS - DEBUG GUIDE

## Step-by-Step Debugging

### Step 1: Check if Files Are Loaded

1. **Open any A-Tables page** (Dashboard, All Tables, etc.)
2. **Press F12** → Click "Network" tab
3. **Refresh the page** (Ctrl+R)
4. **Search for:** `notifications.js`
5. **Check if it loads** (should show status 200)

### Step 2: Check Console

1. **Press F12** → Click "Console" tab
2. **Look for:** `✓ A-Tables Toast System Loaded`
3. **If you see it:** JS loaded successfully ✅
4. **If you DON'T see it:** File not loading ❌

### Step 3: Test in Console

**Type exactly this:**
```javascript
window.ATablesToast
```

**Expected Result:**
```javascript
{show: ƒ, success: ƒ, error: ƒ, warning: ƒ, info: ƒ, ...}
```

**If you see "undefined":** File didn't load ❌

### Step 4: Force Test

**Type this:**
```javascript
ATablesToast.success('Testing!');
```

**Expected:** Green toast appears bottom-right ✅

---

## Common Issues & Fixes

### Issue 1: "ATablesToast is not defined"

**Cause:** JavaScript file not loading

**Fix:**
1. Hard refresh: `Ctrl + Shift + R`
2. Clear cache
3. Check file exists: `C:\Users\Tommy\Local Sites\my-wordpress-site\app\public\wp-content\plugins\a-tables-charts\assets\js\notifications.js`

### Issue 2: Console shows "✓ Loaded" but ATablesToast is undefined

**Cause:** Timing issue (rare)

**Fix:**
```javascript
// Wait a bit and try again
setTimeout(() => ATablesToast.success('Test'), 1000);
```

### Issue 3: File loads but no toast appears

**Cause:** CSS not loading

**Fix:**
1. Check Network tab for `notifications.css`
2. Hard refresh
3. Check browser console for CSS errors

### Issue 4: Toast appears but disappears immediately

**Cause:** Multiple toast systems conflicting

**Fix:**
```javascript
// Clear all toasts first
ATablesToast.clear();
// Then test
ATablesToast.success('Test');
```

---

## Manual Test (Foolproof)

### Copy this ENTIRE block into console:

```javascript
console.log('=== TOAST DEBUG START ===');
console.log('1. Checking if ATablesToast exists:', typeof window.ATablesToast);
console.log('2. Checking if ATablesNotifications exists:', typeof window.ATablesNotifications);

if (typeof ATablesToast !== 'undefined') {
    console.log('3. ATablesToast methods:', Object.keys(ATablesToast));
    console.log('4. Attempting to show toast...');
    try {
        ATablesToast.success('🎉 Toast system is working!');
        console.log('5. SUCCESS! Toast shown!');
    } catch (e) {
        console.error('5. ERROR showing toast:', e);
    }
} else {
    console.error('3. FAILED: ATablesToast not found!');
    console.log('Checking global objects:', Object.keys(window).filter(k => k.includes('Toast') || k.includes('Notification')));
}
console.log('=== TOAST DEBUG END ===');
```

**What to share with me:**
- Screenshot of the console output
- Network tab showing notifications.js status
- Any error messages

---

## Quick Check URLs

Try these URLs directly in browser:

**JavaScript file:**
```
http://my-wordpress-site.local/wp-content/plugins/a-tables-charts/assets/js/notifications.js
```

**CSS file:**
```
http://my-wordpress-site.local/wp-content/plugins/a-tables-charts/assets/css/notifications.css
```

**Both should load** without 404 errors.

---

## If NOTHING Works...

### Last Resort: Add directly to page

1. Go to WordPress Admin → A-Tables & Charts
2. **Press F12** → Console
3. **Paste this:**

```javascript
// Load toast system manually
(function() {
    if (document.getElementById('atables-toast-test-loaded')) return;
    const marker = document.createElement('div');
    marker.id = 'atables-toast-test-loaded';
    document.body.appendChild(marker);
    
    // Create container
    const container = document.createElement('div');
    container.id = 'atables-toast-container';
    container.style.cssText = 'position:fixed;bottom:20px;right:20px;z-index:999999;display:flex;flex-direction:column-reverse;gap:12px;';
    document.body.appendChild(container);
    
    // Create toast
    const toast = document.createElement('div');
    toast.style.cssText = 'display:flex;align-items:center;gap:12px;padding:16px 20px;background:#fff;border-radius:8px;box-shadow:0 4px 20px rgba(0,0,0,0.15);border-left:4px solid #00a32a;min-width:300px;';
    toast.innerHTML = '<div style="font-size:24px;">✓</div><div style="flex:1;font-size:14px;color:#1d2327;font-weight:500;">Manual toast test working!</div>';
    container.appendChild(toast);
    
    setTimeout(() => toast.remove(), 4000);
    console.log('✓ Manual toast shown!');
})();
```

**If this shows a toast:** CSS/JS loading issue  
**If this doesn't show:** Browser/page issue

---

## What I Need From You:

Please run the debug script above and tell me:

1. **What does console say?**
   - Does it find ATablesToast?
   - Are there any errors?

2. **Network tab:**
   - Does notifications.js load? (status 200?)
   - Does notifications.css load? (status 200?)

3. **Manual test:**
   - Does the manual paste work?

This will tell me exactly what's wrong! 🔍
