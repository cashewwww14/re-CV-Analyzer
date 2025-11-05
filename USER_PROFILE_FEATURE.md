# User Profile Feature

## Fitur yang Ditambahkan

Fitur User Profile telah berhasil ditambahkan ke aplikasi CV Analyzer dengan kemampuan berikut:

### 1. View Profile (`/profile`)
- Menampilkan informasi lengkap user
- Avatar/foto profil
- Username, Email, Phone, Birth Date, Address, Bio
- Informasi akun (role, member since, last updated)

### 2. Edit Profile (`/profile/edit`)
- Edit semua informasi personal
- Upload/ganti avatar (max 2MB, format: JPG, PNG, GIF)
- Preview avatar sebelum upload
- Validasi untuk memastikan username dan email unik

### 3. Change Password (`/profile/password`)
- Ganti password dengan validasi keamanan
- Password harus minimal 8 karakter
- Harus mengandung huruf besar, huruf kecil, dan angka
- Password strength indicator (visual feedback)
- Tips keamanan password

## Database Changes

Migration baru ditambahkan: `2025_10_31_030042_add_profile_fields_to_users_table`

Field baru di tabel `users`:
- `phone` (string, nullable)
- `address` (text, nullable)
- `birth_date` (date, nullable)
- `avatar` (string, nullable)
- `bio` (text, nullable)

## Files Created/Modified

### New Files:
1. `app/Http/Controllers/UserProfileController.php` - Controller untuk profile
2. `resources/views/profile/show.blade.php` - View detail profile
3. `resources/views/profile/edit.blade.php` - Form edit profile
4. `resources/views/profile/change-password.blade.php` - Form ganti password
5. `database/migrations/2025_10_31_030042_add_profile_fields_to_users_table.php`

### Modified Files:
1. `routes/web.php` - Menambahkan routes profile
2. `app/Models/User.php` - Menambahkan fillable fields dan cast
3. `resources/views/cv-analyzer.blade.php` - Menambahkan link ke profile di sidebar
4. `resources/views/cv-history.blade.php` - Menambahkan link ke profile di sidebar
5. `resources/views/cv-detail.blade.php` - Menambahkan link ke profile di sidebar

## Routes

```
GET    /profile                - Menampilkan profile
GET    /profile/edit           - Form edit profile
PUT    /profile                - Update profile
GET    /profile/password       - Form ganti password
PUT    /profile/password       - Update password
```

## Cara Menggunakan

1. Login sebagai user (bukan admin)
2. Di sidebar, klik "My Profile"
3. Untuk edit profile, klik tombol "Edit Profile"
4. Untuk ganti password, klik tombol "Change Password"
5. Upload avatar dengan memilih file gambar
6. Semua perubahan akan tersimpan dan ditampilkan di halaman profile

## Security Features

- Route middleware: hanya user yang login dengan role 'user' yang bisa akses
- Validasi upload avatar (max 2MB, format image)
- Password validation dengan requirements ketat
- Current password verification sebelum update password
- Unique validation untuk username dan email
- CSRF protection untuk semua form

## Storage

Avatar disimpan di `storage/app/public/avatars/`
Symbolic link sudah dibuat: `public/storage -> storage/app/public`
