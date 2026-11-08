/**
 * Pembaca token CSRF.
 *
 * CodeIgniter 3 membandingkan nilai POST dengan nilai COOKIE, bukan dengan
 * meta tag di HTML. Meta tag hanya salinan saat halaman dimuat, jadi begitu
 * cookie berganti (token kedaluwarsa, atau tab dibiarkan terbuka lebih dari
 * csrf_expire) meta tag menjadi basi dan setiap POST dibalas 403 tanpa
 * umpan balik ke user.
 *
 * Cookie selalu paling baru, jadi bacalah dari situ lebih dulu. Meta tag
 * hanya dipakai sebagai cadangan kalau cookie tidak terbaca.
 *
 * Nama cookie CSRF asli adalah cookie_prefix + csrf_cookie_name
 * (lihat system/core/Security.php), jadi jangan di-hardcode di sini.
 */
function getCsrfToken() {
    var hashMeta = document.querySelector('meta[name="csrf-token-hash"]');
    var cookieMeta = document.querySelector('meta[name="csrf-cookie-name"]');
    var cookieName = (cookieMeta && cookieMeta.content) || 'csrf';
    var prefix = cookieName + '=';
    var parts = (document.cookie || '').split('; ');

    for (var i = 0; i < parts.length; i++) {
        if (parts[i].indexOf(prefix) === 0) {
            try {
                return decodeURIComponent(parts[i].slice(prefix.length));
            } catch (e) {
                return parts[i].slice(prefix.length);
            }
        }
    }

    return hashMeta ? hashMeta.content : '';
}

function getCsrfTokenName() {
    var nameMeta = document.querySelector('meta[name="csrf-token-name"]');
    return nameMeta ? nameMeta.content : 'csrf_test_name';
}

/** Pasangan siap POST untuk application/x-www-form-urlencoded. */
function getCsrfField() {
    return getCsrfTokenName() + '=' + encodeURIComponent(getCsrfToken());
}
