<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;

class FaqController extends Controller
{
    public function index()
    {
        $faqContent = File::get(base_path('faq.txt'));
        $sections = collect(preg_split('/(?=^[A-Z\s&]+$)/m', $faqContent, -1, PREG_SPLIT_NO_EMPTY));
        $faqs = $sections->mapWithKeys(function ($section) {
            $lines = array_filter(explode("\n", trim($section)));
            $title = array_shift($lines);
            $questions = [];
            $currentQuestion = null;
            $currentAnswer = '';

            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) {
                    if ($currentQuestion && ! empty($currentAnswer)) {
                        $questions[] = [
                            'question' => $currentQuestion,
                            'answer' => trim($currentAnswer),
                        ];
                    }
                    $currentQuestion = null;
                    $currentAnswer = '';

                    continue;
                }
                if (! $currentQuestion) {
                    $currentQuestion = $line;
                } else {
                    $currentAnswer .= $line."\n";
                }
            }

            if ($currentQuestion && ! empty($currentAnswer)) {
                $questions[] = [
                    'question' => $currentQuestion,
                    'answer' => trim($currentAnswer),
                ];
            }

            return [$title => $questions];
        });

        return view('faq', ['faqs' => $faqs]);
    }
}
