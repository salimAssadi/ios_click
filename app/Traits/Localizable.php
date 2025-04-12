<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait Localizable
{
    /**
     * Map full language names to language codes.
     *
     * @var array
     */
    protected $languageMap = [
        'arabic' => 'ar',
        'english' => 'en',
    ];

    /**
     * Retrieve the localized attribute based on the user's preferred language.
     *
     * @param string $attribute
     * @return string
     */
    protected function getLocalizedAttribute($attribute)
    {
        $user = Auth::user(); // Get the currently authenticated user
        $locale = $this->getLocaleFromUser($user); // Get the locale from the user's language
        return $this->attributes["$attribute" . "_$locale"] ?? $this->attributes["$attribute" . "_en"]; // Fallback to '_en'
    }

    /**
     * Map the user's language to a locale code.
     *
     * @param \App\Models\User|null $user
     * @return string
     */
    protected function getLocaleFromUser($user)
    {
        if ($user && isset($this->languageMap[$user->lang])) {
            return $this->languageMap[$user->lang]; // Map the language name to a locale code
        }
        return 'en'; // Default to English if no valid mapping is found
    }
}