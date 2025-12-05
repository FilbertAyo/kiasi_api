<?php

namespace App\Http\Controllers;

use App\Models\StaticContent;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicContentController extends Controller
{
    /**
     * Get the language from request.
     */
    private function getLanguage(Request $request): string
    {
        $lang = $request->query('lang', app()->getLocale());
        
        // Validate language
        return in_array($lang, ['sw', 'en']) ? $lang : 'sw';
    }

    /**
     * Convert markdown to HTML.
     */
    private function markdownToHtml(string $markdown): string
    {
        // Try Laravel's built-in markdown helper (Laravel 10+)
        if (method_exists(\Illuminate\Support\Str::class, 'markdown')) {
            try {
                return \Illuminate\Support\Str::markdown($markdown);
            } catch (\Exception $e) {
                // Fall through to custom parser if markdown fails
            }
        }

        // Fallback: Simple markdown parser for basic formatting
        $lines = explode("\n", $markdown);
        $html = '';
        $inList = false;
        
        foreach ($lines as $line) {
            $line = rtrim($line);
            
            // Headers
            if (preg_match('/^### (.*)$/', $line, $matches)) {
                if ($inList) {
                    $html .= "</ul>\n";
                    $inList = false;
                }
                $html .= "<h3>{$matches[1]}</h3>\n";
            } elseif (preg_match('/^## (.*)$/', $line, $matches)) {
                if ($inList) {
                    $html .= "</ul>\n";
                    $inList = false;
                }
                $html .= "<h2>{$matches[1]}</h2>\n";
            }
            // Lists
            elseif (preg_match('/^- (.*)$/', $line, $matches)) {
                if (!$inList) {
                    $html .= "<ul>\n";
                    $inList = true;
                }
                $item = $matches[1];
                // Process inline formatting
                $item = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $item);
                $item = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $item);
                $html .= "<li>{$item}</li>\n";
            }
            // Empty line
            elseif (empty($line)) {
                if ($inList) {
                    $html .= "</ul>\n";
                    $inList = false;
                }
                $html .= "<p></p>\n";
            }
            // Regular paragraph
            else {
                if ($inList) {
                    $html .= "</ul>\n";
                    $inList = false;
                }
                // Process inline formatting
                $line = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $line);
                $line = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $line);
                $html .= "<p>{$line}</p>\n";
            }
        }
        
        // Close any open list
        if ($inList) {
            $html .= "</ul>\n";
        }
        
        // Clean up empty paragraphs
        $html = preg_replace('/<p><\/p>\n?/', '', $html);
        
        return trim($html);
    }

    /**
     * Display Privacy Policy page.
     * GET /privacy-policy
     */
    public function privacyPolicy(Request $request): View
    {
        $language = $this->getLanguage($request);
        $content = StaticContent::getContent('privacy', $language);

        // Fallback to English if content not found in requested language
        if (!$content && $language !== 'en') {
            $content = StaticContent::getContent('privacy', 'en');
        }

        // If still no content, show a default message
        if (!$content) {
            abort(404, 'Privacy Policy not found');
        }

        return view('public.privacy-policy', [
            'content' => $content,
            'language' => $language,
            'htmlContent' => $this->markdownToHtml($content->content),
        ]);
    }

    /**
     * Display Terms and Conditions page.
     * GET /terms-and-conditions
     */
    public function termsAndConditions(Request $request): View
    {
        $language = $this->getLanguage($request);
        $content = StaticContent::getContent('terms', $language);

        // Fallback to English if content not found in requested language
        if (!$content && $language !== 'en') {
            $content = StaticContent::getContent('terms', 'en');
        }

        // If still no content, show a default message
        if (!$content) {
            abort(404, 'Terms and Conditions not found');
        }

        return view('public.terms-and-conditions', [
            'content' => $content,
            'language' => $language,
            'htmlContent' => $this->markdownToHtml($content->content),
        ]);
    }
}

