[![version](https://img.shields.io/badge/version-v1-orange.svg)]() [![license](https://img.shields.io/badge/license-GPLv3-blue.svg)](https://github.com/mertskaplan/anomaly-reader/blob/master/LICENSE) [![CodeFactor](https://www.codefactor.io/repository/github/mertskaplan/anomaly-reader/badge)](https://www.codefactor.io/repository/github/mertskaplan/anomaly-reader) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/mertskaplan/anomaly-reader/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/mertskaplan/anomaly-reader/?branch=master) [![Maintainability](https://api.codeclimate.com/v1/badges/8f181857fefd44c53d42/maintainability)](https://codeclimate.com/github/mertskaplan/anomaly-reader/maintainability)

# anomalyReader
Twitter'da akan tweetleri inceleyerek anomalileri bildirmeye yarayan bot uygulaması. Şimdilik 12 büyük ilde gerçekleşen patlamalar ile ilgili verileri inceliyor.

> **Twitter:** https://twitter.com/anomalyReader

### Kurulum
 1. `index.php` dosyasını metin editörü ile açın. `$word`, `$cities` ve `$radius` değişkenlerine anomalilerin tespit edileceği kelimeyi, şehirleri ve şehir koordinatlarının etrafına çizilecek olan alan için uzaklık değerini (*`km` veya `ml` cinsinden*) girin. Şehirlerin her biri için -sırayla- plaka kodu, şehir adı, şehrin sonuna eklenecen bulunma ve ayrılma hal ekleri için sesli harf ve "*latitude*" ile "*longitude*" formatlarında koordinat (Şehir merkezini esas alabilirsiniz.) bilgilerini girin.

> **Not:** Uygulamada, panik anında yazım kurallarına uymadan gönderilen tweetlerin de yakalanması için bulunma ve ayrılma hal eklerinin şehir adları ile birleştirilmiş halleri de incelenecek sorgulara dahil edilmiştir. Bu nedenle hal ekleri için sesli harfi doğru girmelisiniz. (*ör: Ankara için "a" harfi ile "Ankarada" kelimesinin veya İzmir için "e" harfi ile "İzmirden" kelimesinin üretilmesi için*)

 2. https://apps.twitter.com/ adresini ziyaret ederek bir Twitter uygulaması oluşturun (*Mobil doğrulama istenilebilir.*) ve uygulamaya "**Permissions**" sekmesinden "**Read, Write and Access direct messages**" iznini verin. Adından "**Keys and Access Tokens**" sekmesinden "**Create my access token**" butonu aracılığıyla gerekli erişim kodlarını oluşturun ve uygulamanın `index.php` dosyasındaki `$consumerKey`, `$consumerSecret`, `$accessToken` ve `$accessTokenSecret` değişkenlerine bu kodları yerleştirerek dosyayı kaydedin.

 3. Dosyaları sunucunuza gönderin ve `index.php` dosyasını işaret edecek şekilde bir **cronJob** oluşturun. (*Dakikada 1 kez çalıştırılabilir.*)

> **Not:** Kendi sunucunuzun zamanlanmış görevlerin yerine bu hizmeti sağlayan http://cron-job.org/ gibi servisleri de kullanabilirsiniz.

### Lisans
Uygulama, [GNU Genel Kamu Lisansı, sürüm 3](https://github.com/mertskaplan/anomaly-reader/blob/master/LICENSE) altında yayımlandı.

### İletişim
Web: [https://mertskaplan.com](http://mertskaplan.com)
Mail: mail@mertskaplan.com
Twitter: [@mertskaplan](https://twitter.com/mertskaplan)
