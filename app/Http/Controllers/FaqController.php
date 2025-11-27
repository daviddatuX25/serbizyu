<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class FaqController extends Controller
{
    /**
     * Display the FAQ page
     */
    public function index(): View
    {
        $faqs = $this->parseFaqMarkdown();

        return view('faq', compact('faqs'));
    }

    /**
     * Parse FAQ.md and organize into categories
     */
    private function parseFaqMarkdown(): array
    {
        $content = File::get(base_path('FAQ.md'));
        $lines = explode("\n", $content);

        $categories = [];
        $currentCategory = null;
        $currentQuestion = null;
        $currentAnswer = '';

        foreach ($lines as $line) {
            // Check for category headers (##)
            if (preg_match('/^##\s+(.+)$/', $line, $matches)) {
                if ($currentCategory && $currentQuestion) {
                    $this->addQuestion($categories, $currentCategory, $currentQuestion, $currentAnswer);
                }

                $categoryText = trim($matches[1]);
                // Extract emoji and category name
                if (preg_match('/^([^\w]+)\s+(.+)$/', $categoryText, $categoryMatches)) {
                    $icon = $categoryMatches[1];
                    $name = $categoryMatches[2];
                } else {
                    $icon = '❓';
                    $name = $categoryText;
                }

                $currentCategory = [
                    'icon' => $icon,
                    'name' => $name,
                ];
                $currentQuestion = null;
                $currentAnswer = '';

                continue;
            }

            // Check for question headers (####)
            if (preg_match('/^####\s+(.+)$/', $line, $matches)) {
                if ($currentCategory && $currentQuestion) {
                    $this->addQuestion($categories, $currentCategory, $currentQuestion, $currentAnswer);
                }

                $currentQuestion = trim($matches[1]);
                $currentAnswer = '';

                continue;
            }

            // Skip non-answer lines
            if ($currentQuestion === null || empty($line)) {
                continue;
            }

            // Remove **A:** prefix if it exists
            $line = preg_replace('/^\*\*A:\*\*\s*/', '', $line);

            if (! empty($line)) {
                $currentAnswer .= ($currentAnswer ? ' ' : '').trim($line);
            }
        }

        // Add last question
        if ($currentCategory && $currentQuestion) {
            $this->addQuestion($categories, $currentCategory, $currentQuestion, $currentAnswer);
        }

        return $categories;
    }

    /**
     * Add a question to the categories array
     */
    private function addQuestion(&$categories, $category, $question, $answer): void
    {
        $categoryKey = $category['name'];

        if (! isset($categories[$categoryKey])) {
            $categories[$categoryKey] = [
                'icon' => $category['icon'],
                'category' => $category['name'],
                'questions' => [],
            ];
        }

        $categories[$categoryKey]['questions'][] = [
            'question' => str_replace(['**Q:', '**'], '', $question),
            'answer' => trim($answer),
        ];
    }
}
