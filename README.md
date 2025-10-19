# Bilet Satın Alma Platformu

## Özellikler

* Platform; Ziyaretçi, User (Yolcu), Firma Admini (Firma Yetkilisi) ve Admin olmak üzere farklı kullanıcı rollerini desteklemektedir. 
* Firma Adminleri sadece kendi firmalarına ait seferleri yönetebilir, bu seferler için oluşturma, düzenleme ve silme (CRUD) işlemleri yapabilir. 
* **Yönetim Panelleri:**
    * **Admin:** Yeni otobüs firmaları ve "Firma Admin" kullanıcıları oluşturabilir, mevcutları yönetebilir. Ayrıca tüm firmalarda geçerli indirim kuponları oluşturabilir.
    * **Firma Admini:** Sadece kendi firmasına ait seferleri yönetebilir ve firma özelinde indirim kuponları oluşturabilir.
* **Bilet Satış Akışı:**
    * Ana sayfada kalkış ve varış noktası seçilerek seferler listelenebilir. [
    * Gerçekçi 2+1 koltuk seçimi arayüzü.
    * Bilet alımı, kullanıcının hesabındaki sanal kredi üzerinden yapılır. 
* **Kupon Sistemi:** Yetkili rollerin oluşturduğu indirim kuponları kullanıcılar tarafından uygulanabilir. 
* **Kullanıcı İşlemleri:**
    * Kullanıcılar satın aldıkları biletleri iptal edebilir. Kalkış saatine 1 saatten az kalmışsa bilet iptal edilemez. Başarılı iptal durumunda bilet ücreti kullanıcının hesabına iade edilir. 
    * Satın alınan biletler PDF olarak indirilebilir. 
    * Güvenli şifre değiştirme.
* **Güvenlik:** SQL Injection, XSS ve CSRF saldırılarına karşı korumalı yapı.

## Kurulum ve Çalıştırma

Bu projeyi yerel makinenizde çalıştırmak için iki yöntem bulunmaktadır. En kolay ve tavsiye edilen yöntem Docker kullanmaktır.

### Yöntem 1: Docker ile

1.  Bu depoyu klonlayın: `git clone https://github.com/KULLANICI_ADINIZ/bilet-satin-alma.git`
2.  Proje dizinine gidin: `cd bilet-satin-alma`
3.  Docker imajını oluşturun: `docker-compose build`
4.  Konteyneri başlatın: `docker-compose up -d`
5.  Tarayıcınızdan `http://localhost:8000/install.php` adresine gidin ve kurulum talimatlarını izleyin.
6.  **ÖNEMLİ:** Kurulumu tamamladıktan sonra güvenlik için `public/install.php` dosyasını silin.
7.  Artık `http://localhost:8000` adresinden uygulamaya erişebilirsiniz.

### Yöntem 2: Yerel PHP Sunucusu ile

1.  Bu depoyu klonlayın.
2.  Proje dizininde bir terminal açın ve `public` klasörünü ana dizin olarak belirterek PHP'nin dahili sunucusunu başlatın: `php -S localhost:8000 -t public`
3.  Tarayıcınızdan `http://localhost:8000/install.php` adresine gidin ve kurulum talimatlarını izleyin. (Kurulum için `install.php` dosyasını geçici olarak `public` klasörüne taşımanız gerekebilir.)
4.  **ÖNEMLİ:** Kurulumu tamamladıktan sonra güvenlik için `install.php` dosyasını silin.
5.  Uygulamaya `http://localhost:8000` adresinden erişebilirsiniz.

## Varsayılan Giriş Bilgileri

Kurulum script'i aşağıdaki varsayılan kullanıcıları oluşturur:

* **Süper Admin:**
    * **Email:** `admin@bilet.com`
    * **Şifre:** `password123`
* **Firma Admini (Örnek):**
    * **Email:** `kamil@gmail.com`
    * **Şifre:** `Kamil.123456`
* **User:**
    * **Email:** `goksu@gmail.com`
    * **Şifre:** `goksu123456`
