<?php

namespace App\Services;

class BadWordService
{
    public function addBadWord($word)
    {
        $badWords = $this->getBadWords();
        $badWords[] = $word;
        $this->updateBadWords($badWords);
    }

    public function removeBadWord($word)
    {
        $badWords = $this->getBadWords();
        $wordIndex = array_search($word, $badWords);

        if ($wordIndex !== false) {
            unset($badWords[$wordIndex]);
            $this->updateBadWords($badWords);
            return true;
        }

        return false;
    }

    public function getBadWords()
    {
        return config('bad_words');
    }

    private function updateBadWords($badWords)
    {
        sort($badWords);
        config(['bad_words' => array_unique($badWords)]);
        $this->saveBadWordsToFile($badWords);
    }

    private function saveBadWordsToFile($badWords)
    {
        $fileContent = "<?php\n\nreturn " . var_export($badWords, true) . ";";
        file_put_contents(config_path('bad_words.php'), $fileContent);
    }
}
