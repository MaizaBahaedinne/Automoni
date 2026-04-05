<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;

class LangController extends BaseController
{
    private const SUPPORTED = ['en', 'fr', 'ar'];

    public function switch(string $locale): RedirectResponse
    {
        if (!in_array($locale, self::SUPPORTED, true)) {
            $locale = 'en';
        }

        session()->set('locale', $locale);

        $back = $this->request->getServer('HTTP_REFERER') ?? base_url('/');
        return redirect()->to($back);
    }
}
