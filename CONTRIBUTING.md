## Panduang Kontribusi Plugin LSDCommerce
Kamu dapat berkontribusi dalam pengembangan LSDCommerce dengan memfork/clone repository ini. silahkan install docker di komputer kamu terlebih dahulu.
lalu mulai ikuti instruksi dibawah ini.

### Memulai

1. Clone atau Fork Repository ini
2. Untuk pengguna OS Windows anda bisa buka `notepad` -> `run as administrator` lalu buka folder `C:\Windows\System32\drivers\etc` lalu buka file hosts dan tambahkan 
3. Untuk pengguna OS Linux anda bisa menambahkan pada `/etc/hosts`lalu buka file dan tambahkan
```text
127.0.0.1 lsdcommerce.local
```

### Menginstall dan Menjalankan Docker Image
```
docker-compose up -d
```

Jalankan perintah diatas, maka docker akan mendownload image yang dibutuhkan, tunggu hingga selesai. pastikan koneksi internet tersedia. 
Pastikan kamu tidak sedang menghidupkan apache server di komputer kamu. Matikan apache server ataupun MySql jika kamu menggunakan xampp / ampps dan kawan-kawannya

```
docker-compose logs wordpress
```

Kamu juga bisa melihat log docker dengan perintah diatas.

### Instalasi WordPress

```
docker-compose run --rm wp-cli install-wp
```
Jalankan perintah diatas maka docker akan menginstall wordpress melalui wp-cli. setelah prosesnya selesai kamu bisa mulai menginstall wordpresmu dengan membuka`http://lsdcommerce.local/` dan setelah selesai kamu bisa login ke dashboard wordpressmu dan mulai mendevelop plugin

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


### Menghentingkan Pengembangan
Kamu bisa menghentikan pengembangan dengan memberikan perintah berhenti pada docker 

```
docker-compose stop
````


### Memulai Pengembangan Lagi
Kamu bisa langsung menjalankan docker tanpa perlu menginstall ulang lagi, dengan cara

```
docker-compose start
````

### * Memulai Unit Testing *

```
docker-compose -f docker-compose.phpunit.yml run --rm wordpress_phpunit phpunit 
docker-compose -f docker-compose.phpunit.yml run --rm wordpress_phpunit phpunit --testdox
docker-compose -f docker-compose.phpunit.yml run --rm wordpress_phpunit phpunit --group ajax
```

### Setup Debug Log

```
docker ps
docker exec -it lsdcommerce_wordpress_1 apt-get update
docker exec -it lsdcommerce_wordpress_1 apt-get install nano
docker exec -it lsdcommerce_wordpress_1 bash
nano wp-config.php
````

sekarang kamu perlu mengedit wp-config.php

```
define('WP_DEBUG', true );
define('WP_DEBUG_LOG', 'wp-content/plugins/lsdcommerce/debug.log');
docker-compose restart
```

lalu setelah itu save dan restart container.