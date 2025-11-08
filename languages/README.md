# A-Tables and Charts - Translation Guide

Welcome to the A-Tables and Charts translation project! We appreciate your help in making this plugin accessible to users around the world.

---

## 📋 Quick Start

### For Translators

1. **Download the template file:** `a-tables-charts.pot`
2. **Use a translation tool** (recommended):
   - [Poedit](https://poedit.net/) - Desktop app (Free & Pro versions)
   - [Loco Translate](https://wordpress.org/plugins/loco-translate/) - WordPress plugin
   - [GlotPress](https://translate.wordpress.org/) - WordPress.org community
3. **Create your translation:**
   - Open the `.pot` file in your translation tool
   - Translate strings to your language
   - Save as `a-tables-charts-{locale}.po` (e.g., `a-tables-charts-es_ES.po` for Spanish)
4. **Compile to .mo:** Your translation tool will automatically create the `.mo` file
5. **Install:** Place both `.po` and `.mo` files in the `/languages` folder

---

## 🌍 Language Codes (Locales)

Common locale codes for WordPress:

| Language | Locale Code | Example Filename |
|----------|-------------|------------------|
| Arabic | ar | a-tables-charts-ar.po |
| Chinese (Simplified) | zh_CN | a-tables-charts-zh_CN.po |
| Chinese (Traditional) | zh_TW | a-tables-charts-zh_TW.po |
| Dutch | nl_NL | a-tables-charts-nl_NL.po |
| French | fr_FR | a-tables-charts-fr_FR.po |
| German | de_DE | a-tables-charts-de_DE.po |
| Italian | it_IT | a-tables-charts-it_IT.po |
| Japanese | ja | a-tables-charts-ja.po |
| Korean | ko_KR | a-tables-charts-ko_KR.po |
| Portuguese (Brazil) | pt_BR | a-tables-charts-pt_BR.po |
| Portuguese (Portugal) | pt_PT | a-tables-charts-pt_PT.po |
| Russian | ru_RU | a-tables-charts-ru_RU.po |
| Spanish | es_ES | a-tables-charts-es_ES.po |
| Spanish (Mexico) | es_MX | a-tables-charts-es_MX.po |
| Turkish | tr_TR | a-tables-charts-tr_TR.po |

**Full list:** https://make.wordpress.org/polyglots/teams/

---

## 🛠️ Translation Tools

### Option 1: Poedit (Recommended for Desktop)

**Download:** https://poedit.net/

**Steps:**
1. Download and install Poedit
2. Open Poedit and select **"Create New Translation"**
3. Select the `a-tables-charts.pot` file
4. Choose your target language
5. Start translating
6. Save your work (automatically creates .po and .mo files)

**Pros:**
- ✅ User-friendly interface
- ✅ Automatic .mo compilation
- ✅ Translation memory
- ✅ Suggestions from similar strings
- ✅ Works offline

### Option 2: Loco Translate (WordPress Plugin)

**Install:** WordPress Admin → Plugins → Add New → Search "Loco Translate"

**Steps:**
1. Install and activate Loco Translate
2. Go to **Loco Translate → Plugins → A-Tables and Charts**
3. Click **"New Language"**
4. Select your language
5. Choose location (plugin's languages folder)
6. Start translating in WordPress admin
7. Click **"Save"** to compile

**Pros:**
- ✅ Translate directly in WordPress
- ✅ No external tools needed
- ✅ Real-time preview
- ✅ Automatic backups

### Option 3: Manual Translation (Advanced)

**Requirements:**
- Text editor
- `msgfmt` command-line tool (part of gettext)

**Steps:**
```bash
# 1. Copy template
cp a-tables-charts.pot a-tables-charts-es_ES.po

# 2. Edit with text editor
# Update header and translate strings

# 3. Compile to .mo
msgfmt a-tables-charts-es_ES.po -o a-tables-charts-es_ES.mo
```

---

## 📝 Translation Guidelines

### 1. Context Matters

Some strings have special meanings:

```gettext
# Technical term - usually not translated
msgid "MySQL"
msgstr "MySQL"

# UI element - translate
msgid "Table"
msgstr "Tabla"  # Spanish

# Button text - keep it short
msgid "Save"
msgstr "Guardar"
```

### 2. Placeholders

Preserve placeholders in your translation:

```gettext
# %s = string, %d = number
msgid "Table created successfully!"
msgstr "¡Tabla creada con éxito!"

msgid "%d rows × %d columns"
msgstr "%d filas × %d columnas"

msgid "%s is required."
msgstr "%s es obligatorio."
```

**Important:** Don't translate the `%s`, `%d`, or other format specifiers!

### 3. HTML Tags

Keep HTML tags intact:

```gettext
msgid "&larr; Return to Plugins"
msgstr "&larr; Volver a Plugins"
```

### 4. Consistency

Use consistent terminology:
- "Table" → Always translate the same way
- "Chart" → Always translate the same way
- "Export" → Always translate the same way

### 5. Formality

Match WordPress's tone in your language:
- Use formal or informal address consistently
- Follow WordPress translation standards for your language

### 6. String Length

Some translations are for buttons or small UI elements:
- Try to keep similar length to English
- Very long translations may break the layout

### 7. RTL Languages

For right-to-left languages (Arabic, Hebrew):
- WordPress handles text direction automatically
- Just translate the text normally
- CSS will adjust layout

---

## 📚 Translation Resources

### WordPress Official Resources

- **Polyglots Handbook:** https://make.wordpress.org/polyglots/handbook/
- **Translation Style Guide:** https://make.wordpress.org/polyglots/handbook/translating/glossary/
- **WordPress Glossary:** https://translate.wordpress.org/

### Community

- **WordPress Polyglots:** https://make.wordpress.org/polyglots/
- **Slack Channel:** #polyglots on [WordPress Slack](https://make.wordpress.org/chat/)

---

## 🧪 Testing Your Translation

### 1. Install Translation Files

Place your `.po` and `.mo` files in:
```
/wp-content/plugins/a-tables-charts/languages/
```

### 2. Set WordPress Language

**WordPress Admin:**
- Go to **Settings → General**
- Set **Site Language** to your language
- Save changes

### 3. Verify Translations

- Navigate through the plugin
- Check all pages and features
- Look for untranslated strings
- Verify placeholders work correctly
- Check for layout issues

### 4. Common Issues

**Translation not showing:**
- ✅ Check file names match locale exactly
- ✅ Verify .mo file exists (compiled from .po)
- ✅ Clear WordPress cache
- ✅ Verify WordPress language setting

**Some strings not translated:**
- ✅ Check for newer plugin version
- ✅ Regenerate .pot file to get new strings
- ✅ Update your translation

**Layout broken:**
- ✅ Translation may be too long
- ✅ Try shorter alternative
- ✅ Report to plugin authors

---

## 📊 Translation Statistics

The A-Tables and Charts plugin contains approximately:

- **400+ translatable strings**
- **25 modules** with translations
- **6 main feature areas:**
  1. Table Management (80+ strings)
  2. Chart Management (60+ strings)
  3. Import/Export (70+ strings)
  4. Display Settings (50+ strings)
  5. Advanced Features (90+ strings)
  6. General UI (50+ strings)

---

## 🤝 Contributing Your Translation

### Option 1: WordPress.org (Recommended)

Once the plugin is on WordPress.org:

1. Visit: https://translate.wordpress.org/projects/wp-plugins/a-tables-charts
2. Select your language
3. Start translating online
4. Translations are automatically included in plugin updates

### Option 2: Submit to Plugin Author

1. Complete your translation
2. Test thoroughly
3. Email both `.po` and `.mo` files to: [support@a-tables-charts.com](mailto:support@a-tables-charts.com)
4. Include:
   - Your name (for credits)
   - Language/locale code
   - WordPress version tested
   - Any notes or issues

### Option 3: GitHub Pull Request

If the plugin is on GitHub:

1. Fork the repository
2. Add your translation files to `/languages/`
3. Create a pull request
4. Include testing notes

---

## 🏆 Translation Credits

We give credit to all translators! Your name will appear in:

- Plugin's **Credits** section
- `readme.txt` file
- WordPress.org plugin page (if applicable)
- Release notes

---

## 🆘 Need Help?

### Translation Issues

- **Question about context?** → Email support with the string ID
- **Technical issue?** → Check WordPress forums
- **Unsure about terminology?** → Check WordPress glossary for your language

### Contact

- **Email:** support@a-tables-charts.com
- **Website:** https://a-tables-charts.com
- **Documentation:** https://a-tables-charts.com/docs

---

## 📄 File Structure

```
/languages/
├── a-tables-charts.pot          # Template file (use this to start)
├── README.md                    # This file
├── a-tables-charts-{locale}.po  # Your translation source
└── a-tables-charts-{locale}.mo  # Compiled translation (auto-generated)
```

---

## 🔄 Updates

When the plugin updates:

1. **New strings added** → Check for updated `.pot` file
2. **Update your translation** → Open your `.po` file in Poedit
3. **Catalog → Update from POT file** → Merge new strings
4. **Translate new strings** → Fill in missing translations
5. **Save** → New `.mo` file is generated
6. **Test** → Verify everything works

---

## ✅ Translation Checklist

Before submitting your translation:

- [ ] All strings translated
- [ ] Placeholders preserved (%s, %d, etc.)
- [ ] HTML tags intact
- [ ] Consistent terminology throughout
- [ ] Tested in WordPress
- [ ] No layout issues
- [ ] Error messages clear
- [ ] Success messages positive
- [ ] Button text concise
- [ ] Help text helpful
- [ ] .mo file compiled
- [ ] File names correct

---

## 🌟 Thank You!

Thank you for helping make A-Tables and Charts accessible to users worldwide! Your contribution makes a real difference.

**Happy Translating!** 🌍✨
