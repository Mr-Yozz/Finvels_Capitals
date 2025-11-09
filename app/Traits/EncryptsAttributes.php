<?php

namespace App\Traits;

use Illuminate\Support\Facades\Crypt;

trait EncryptsAttributes
{

    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);
        if ($value && property_exists($this, 'encrypts') && in_array($key, $this->encrypts)) {
            try {
                return Crypt::decryptString($value);
            } catch (\Exception $e) {
                return $value;
            }
        }
        return $value;
    }

    public function setAttribute($key, $value)
    {
        if (property_exists($this, 'encrypts') && in_array($key, $this->encrypts) && $value !== null) {
            // encrypt only if plain string (avoid double encrypt)
            $value = Crypt::encryptString($value);
        }
        return parent::setAttribute($key, $value);
    }
}
