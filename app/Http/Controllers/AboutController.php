<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class AboutController extends Controller
{
    /**
     * Display the About page
     */
    public function index(): View
    {
        $sections = $this->parseAboutContent();

        return view('about', compact('sections'));
    }

    /**
     * Parse about.txt and organize into sections
     */
    private function parseAboutContent(): array
    {
        $content = File::get(base_path('app/about.txt'));
        $lines = explode("\n", $content);

        $sections = [];
        $currentSection = null;
        $currentContent = '';

        foreach ($lines as $line) {
            $line = rtrim($line);

            // Check if this is a section header (non-empty line at start of content)
            if (! empty($line) && ! str_starts_with($line, ' ') && $currentSection === null && empty($currentContent)) {
                $currentSection = $line;

                continue;
            }

            // If we have a section and encounter a new header
            if ($currentSection && ! empty($line) && ! str_starts_with($line, ' ') && trim($currentContent) !== '') {
                $sections[] = [
                    'title' => $currentSection,
                    'content' => trim($currentContent),
                ];
                $currentSection = $line;
                $currentContent = '';

                continue;
            }

            // Accumulate content
            if ($currentSection) {
                if (! empty($line)) {
                    $currentContent .= ($currentContent ? "\n" : '').$line;
                } elseif ($currentContent) {
                    $currentContent .= "\n\n";
                }
            }
        }

        // Add the last section
        if ($currentSection && trim($currentContent) !== '') {
            $sections[] = [
                'title' => $currentSection,
                'content' => trim($currentContent),
            ];
        }

        return $sections;
    }
}
