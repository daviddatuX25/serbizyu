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
        $categories = $this->parseFaqMarkdown();

        return view('faq', compact('categories'));
    }

    /**
     * Parse FAQ.md and extract real elements
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
            $line = rtrim($line);

            // Check for category headers (## )
            if (preg_match('/^##\s+(.+)$/', $line, $matches)) {
                if ($currentCategory && $currentQuestion) {
                    $this->addQuestion($categories, $currentCategory, $currentQuestion, $currentAnswer);
                }

                $categoryText = trim($matches[1]);
                // Extract emoji and category name
                preg_match('/^([^A-Za-z0-9]+)?\s*(.+)$/', $categoryText, $parts);
                $icon = trim($parts[1] ?? '');
                $name = trim($parts[2] ?? $categoryText);

                $currentCategory = [
                    'icon' => $icon ?: '❓',
                    'name' => $name,
                ];
                $currentQuestion = null;
                $currentAnswer = '';

                continue;
            }

            // Check for question headers (#### Q)
            if (preg_match('/^####\s+Q\d+:\s+(.+)$/', $line, $matches)) {
                if ($currentCategory && $currentQuestion) {
                    $this->addQuestion($categories, $currentCategory, $currentQuestion, $currentAnswer);
                }

                $currentQuestion = trim($matches[1]);
                $currentAnswer = '';

                continue;
            }

            // Skip lines that are just markdown or empty
            if (empty($line) || $line === '---' || str_starts_with($line, '#')) {
                if ($currentQuestion && ! empty(trim($currentAnswer))) {
                    $currentAnswer .= "\n";
                }

                continue;
            }

            // Accumulate answer content
            if ($currentQuestion !== null) {
                $trimmed = trim($line);
                if ($trimmed) {
                    // Remove **A:** prefix if it exists
                    $trimmed = preg_replace('/^\*\*A:\*\*\s*/', '', $trimmed);
                    // Remove bullet points but keep the content
                    $trimmed = preg_replace('/^[\*\-]\s+/', '', $trimmed);
                    // Remove markdown bold markers
                    $trimmed = str_replace(['**', '__'], '', $trimmed);

                    if ($currentAnswer) {
                        $currentAnswer .= "\n".$trimmed;
                    } else {
                        $currentAnswer = $trimmed;
                    }
                }
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
            'question' => $question,
            'answer' => trim($answer),
        ];
    }
}
