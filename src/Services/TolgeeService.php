<?php

namespace Snippet\LaravelTolgee\Services;

use GuzzleHttp\Client; // 🚀 Guzzle 추가
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Snippet\LaravelTolgee\Integration\Tolgee;

class TolgeeService
{
    private array $config;
    private Filesystem $files;
    private Client $client;

    public function __construct(Filesystem $files, private readonly Tolgee $tolgee)
    {
        $this->config = config('tolgee');
        $this->files = $files;
    }

    public function syncTranslations(array $targetFiles = []): bool
    {
        $prepareWriteArray = [];

        foreach ($this->tolgee->getAllTranslations() as $translationItem) {
            $keyName = (string) $translationItem['keyName'];
            $filePath = $translationItem['keyNamespace'];

            if (!empty($targetFiles) && !in_array(basename($filePath), $targetFiles)) {
                continue;
            }

            foreach ($translationItem['translations'] as $locale => $translation) {
                $localPathName = Str::replace('/en', '/' . $locale, $filePath);
                $prepareWriteArray[$localPathName][$keyName] = $translation['text'];
            }
        }

        foreach ($prepareWriteArray as $localPathName => $translations) {
            $this->files->ensureDirectoryExists(dirname($localPathName));

            $fileContent = Str::contains($localPathName, '.json')
                ? json_encode($translations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
                : "<?php\n\nreturn " . var_export(Arr::undot($translations), true) . ";\n";

            $this->files->put($localPathName, $fileContent);
        }

        return true;
    }

    public function importKeys(array $targetFiles = [])
    {
        $prepare = [];
        $import = [];
        $locales = explode(',', $this->config['locale']);

        if (empty($targetFiles)) {
            foreach ($this->files->directories($this->config['lang_path']) as $langPath) {
                foreach ($this->files->allfiles($langPath) as $file) {
                    $targetFiles[] = $file->getBasename();
                }
            }

            foreach ($this->files->files($this->config['lang_path']) as $jsonFile) {
                if (str_ends_with($jsonFile, '.json')) {
                    $targetFiles[] = basename($jsonFile);
                }
            }
        }

        foreach ($this->files->directories($this->config['lang_path']) as $langPath) {
            $locale = basename($langPath);

            if (!in_array($locale, $locales)) {
                continue;
            }

            foreach ($this->files->allfiles($langPath) as $file) {
                if (in_array($file->getBasename(), $targetFiles)) {
                    $prepare[$locale][$file->getPathname()] = Arr::dot(include $file);
                }
            }
        }

        if (!$this->config['lang_subfolder']) {
            foreach ($this->files->files($this->config['lang_path']) as $jsonFile) {
                if (str_ends_with($jsonFile, '.json')) {
                    $locale = basename($jsonFile, '.json');
                    if (in_array($locale, $locales)) {
                        if (empty($targetFiles) || in_array(basename($jsonFile), $targetFiles)) {
                            $prepare[$locale][$jsonFile->getPathname()] = Arr::dot(Lang::getLoader()->load($locale, '*', '*'));
                        }
                    }
                }
            }
        }

        foreach ($prepare as $locale => $files) {
            foreach ($files as $namespace => $keys) {
                foreach ($keys as $key => $value) {
                    if (!is_array($value)) {
                        $import[$key] ??= ['name' => $key, 'namespace' => $namespace, 'translations' => []];
                        $import[$key]['translations'][$locale] = $value;
                    }
                }
            }
        }
        return $this->tolgee->importKeysRequest(array_values($import));
    }

    public function deleteKeys()
    {
        $ids = [];
        $init = $this->tolgee->getKeysRequest(parse: true);

        for ($page = 0; $page < $init['page']['totalPages']; $page++) {
            $data = $this->tolgee->getKeysRequest($page, true);
            $target = data_get($data, '_embedded.keys');
            $pluck = Arr::pluck($target, 'id');
            $ids = array_merge($ids, $pluck);
        }

        return $this->tolgee->deleteKeysRequest($ids);
    }
}
