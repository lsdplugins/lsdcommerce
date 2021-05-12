Anda dapat berkontribusi dalam pengembangan LSDCommerce dengan mengclone repository ini.

### Memulai

1. Clone atau Fork Repository ini
2. Untuk Pengguna OS windows anda bisa buka `notepad` -> `run as administrator` lalu buka folder `C:\Windows\System32\drivers\etc` lalu buka file hosts dan tambahkan 
```text
127.0.0.1 lsdcommerce.local
```
3. Untuk pengguna linux anda bisa menambahkan pada `/etc/hosts`

### Menginstall dan Menjalankan Docker Image

```
docker-compose up -d
```

Jalankan perintah diatas, maka docker akan mendownload image yang dibutuhkan, tunggu hingga selesai. pastikan koneksi internet tersedia.

```
docker-compose logs wordpress
```

Digunakan jika kamu ingin melihat logs dari instalasi wordpress

### Install WordPress

```
docker-compose run --rm wp-cli install-wp
```
Jalankan perintah diatas maka docker akan menginstall wordpress melalui wp-cli. setelah prosesnya selesai kamu bisa masuk ke `http://lsdcommerce.local/wp-admin/` dan mulai mendevelop plugin

### Menginstall PHP Unit

```
docker-compose -f docker-compose.yml -f docker-compose.phpunit.yml up -d
```
```
docker-compose -f docker-compose.phpunit.yml run --rm wordpress_phpunit bash
```
```
cd /app/bin/
dos2unix install-wp-tests.sh
./install-wp-tests.sh wordpress_test root '' mysql_phpunit latest true
```

Jalankan perintah diatas untuk menginstall PHP Unit dan mulai membuat Unit Testing untuk plugin.

### Akses WordPress Dibatasi

Kamu tidak akan bisa menambah plugin atau file karena akses yang dibatasi, untuk membuka akses kamu bisa lakukan

```
docker ps
docker exec -it lsdcommerce_wordpress_1 bash
chmod -R 777 .
````

Lalu simpan, dan sekarang kamu bisa menambah file, plugin, atau tema baru di instalasi wordpress kamu.