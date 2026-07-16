<?php

namespace App\Support;

/**
 * Fournisseur TOTP (RFC 6238) autonome — aucune dépendance Composer externe
 * n'est nécessaire (pas de pragmarx/google2fa). Compatible avec Google
 * Authenticator, Microsoft Authenticator, Authy, etc.
 */
class TwoFactorAuthenticationProvider
{
    /** Nombre de pas de 30s tolérés avant/après pour absorber le décalage d'horloge */
    protected const FENETRE = 1;

    protected const PAS_SECONDES = 30;

    protected const CHIFFRES = 6;

    /**
     * Génère une clé secrète aléatoire encodée en Base32 (compatible apps d'authentification).
     */
    public function genererCleSecrete(int $longueur = 20): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';

        for ($i = 0; $i < $longueur; $i++) {
            $secret .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        }

        return $secret;
    }

    /**
     * Construit l'URI otpauth:// à encoder dans le QR code.
     */
    public function urlOtpAuth(string $emetteur, string $identifiant, string $secret): string
    {
        return sprintf(
            'otpauth://totp/%s:%s?secret=%s&issuer=%s&algorithm=SHA1&digits=%d&period=%d',
            rawurlencode($emetteur),
            rawurlencode($identifiant),
            $secret,
            rawurlencode($emetteur),
            self::CHIFFRES,
            self::PAS_SECONDES
        );
    }

    /**
     * Vérifie un code à 6 chiffres saisi par l'utilisateur, avec une tolérance
     * de dérive d'horloge de +/- 1 pas (30s).
     */
    public function verifier(string $secret, ?string $code): bool
    {
        if (! $code || ! preg_match('/^\d{6}$/', $code)) {
            return false;
        }

        $pasActuel = (int) floor(time() / self::PAS_SECONDES);

        for ($decalage = -self::FENETRE; $decalage <= self::FENETRE; $decalage++) {
            if (hash_equals($this->codeAPas($secret, $pasActuel + $decalage), $code)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Calcule le code TOTP à 6 chiffres pour un pas temporel donné.
     */
    protected function codeAPas(string $secret, int $pas): string
    {
        $cleBinaire = $this->decoderBase32($secret);
        $compteur = pack('N*', 0, $pas); // 8 octets big-endian

        $hachage = hash_hmac('sha1', $compteur, $cleBinaire, true);

        $decalage = ord($hachage[19]) & 0xf;

        $tronque = ((ord($hachage[$decalage]) & 0x7f) << 24)
            | ((ord($hachage[$decalage + 1]) & 0xff) << 16)
            | ((ord($hachage[$decalage + 2]) & 0xff) << 8)
            | (ord($hachage[$decalage + 3]) & 0xff);

        $code = $tronque % (10 ** self::CHIFFRES);

        return str_pad((string) $code, self::CHIFFRES, '0', STR_PAD_LEFT);
    }

    /**
     * Décode une chaîne Base32 (RFC 4648) en octets bruts.
     */
    protected function decoderBase32(string $base32): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $base32 = strtoupper(str_replace('=', '', $base32));

        $bits = '';
        foreach (str_split($base32) as $caractere) {
            $position = strpos($alphabet, $caractere);
            if ($position === false) {
                continue;
            }
            $bits .= str_pad(decbin($position), 5, '0', STR_PAD_LEFT);
        }

        $octets = '';
        foreach (str_split($bits, 8) as $morceau) {
            if (strlen($morceau) === 8) {
                $octets .= chr(bindec($morceau));
            }
        }

        return $octets;
    }

    /**
     * Génère un lot de codes de récupération à usage unique (format XXXX-XXXX).
     *
     * @return array<int, string>
     */
    public function genererCodesRecuperation(int $nombre = 8): array
    {
        return collect(range(1, $nombre))
            ->map(fn () => strtoupper(substr(bin2hex(random_bytes(5)), 0, 4).'-'.substr(bin2hex(random_bytes(5)), 0, 4)))
            ->all();
    }
}
