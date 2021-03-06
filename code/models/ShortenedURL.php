<?php

class CheckfrontShortenedURL extends DataObject {
    const KeyFieldType = 'Varchar(4)';
    const KeyLength = 4;                    // keep in sync with self.KeyFieldType
    const RetryCount = 20;

    private static $db = array(
        'Key' => self::KeyFieldType,
        'URL' => 'Text',
        'ExpiryDate' => 'SS_DateTime'
    );

    private static $prevent_updates = true;

    private static $valid_chars = 'ABCDEFGHIJKLMNOPQRSTVWXYZabcdefghijklmnopqrstvwxyz0123456789';

    public static function get_url_by_key($key) {
        if ($existing = CheckfrontShortenedURL::get()->filter('Key', $key)->first()) {
            return $existing->URL;
        }
    }

    /**
     * Build a key from config.valid_chars of self.KeyLength length testing for records
     * already existing with that key and failing after self.RetryCount attempts.
     *
     * @return string
     * @throws Exception
     */
    public static function get_unique_key() {
        $key = '';
        $chars = (string)static::config()->get('valid_chars');
        $length = strlen($chars);
        $loops = 0;

        do {
            for ($i = 0; $i < self::KeyLength; $i++) {
                $key .= substr($chars, rand(0, $length - 1), 1);
            }
            $exists = self::key_exists($key);

        } while ($exists && (++$loops < self::RetryCount));

        if ($loops == self::RetryCount) {
            throw new Exception("Tried 20 times to get a key and failed, you might need to increase your key length");
        }

        return $key;
    }

    public static function key_exists($key) {
        return CheckfrontShortenedURL::get()->filter('Key', $key)->count() != 0;
    }

    /**
     * If we are new and don't already have a key then assign a new unique key. If we aren't new then
     * prevents updating of URL for an existing Key.
     *
     * @throws Exception
     */
    public function onBeforeWrite() {
        if (!$this->isInDB()) {
            if (!$this->Key) {
                $this->Key = self::get_unique_key();
            } else {
                if (self::key_exists($this->Key)) {
                    throw new Exception("Key collision on '$this->Key'");
                }
            }
        } elseif ($this->config()->get('prevent_updates')) {
            $url = self::get_url_by_key($this->Key);
            if ($url != $this->URL) {
                throw new Exception("Can't update existing key with a new URL");
            }
        }
        parent::onBeforeWrite();
    }
}