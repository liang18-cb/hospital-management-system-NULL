# Maximillian Axel Sandhya Putra (2902567316) & Charles Budianto (2902561634)


## Fitur Utama
- **Authentication:** Sistem login/registrasi menggunakan Laravel Sanctum dengan proteksi Role Middleware (Admin, Dokter, Pasien).
- **Mailing System:** Notifikasi otomatis untuk konfirmasi booking, reminder H-1, dan update status appointment menggunakan Laravel Scheduler & Mailable.
- **Data Management:** CRUD penuh untuk pasien, dokter, dan rekam medis dengan normalisasi database hingga 3NF.
- **Polymorphic File Storage:** Upload dokumen medis dan foto dengan relasi polymorphic untuk fleksibilitas model.
- **RESTful API:** Arsitektur API yang konsisten menggunakan Laravel API Resources dan response envelope standar.
- **Testing:** Unit testing menggunakan PHPUnit untuk memastikan integritas logika bisnis.

- **SQL Complexity:** Menggunakan relasi `hasOne`, `hasMany`, `belongsTo`, `belongsToMany`.
- **Query Optimization:** Implementasi `INNER JOIN`, `LEFT JOIN`, dan kompleks join untuk pelaporan.

## Prerequisites
- PHP 8.2+
- Composer
- Laravel Herd

## Instalasi

1. **Clone repository:**
```bash
   git clone <https://github.com/liang18-cb/hospital-management-system-NULL.git>
   cd hospital-management-system