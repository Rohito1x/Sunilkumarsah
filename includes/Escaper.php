<?php
declare(strict_types=1);

/**
 * Vision Point Educational Service - Context-Aware Output Escaping & XSS Protection
 * Provides strict, context-sensitive sanitization for HTML body, attributes, JavaScript, and URLs.
 */
final class Escaper
{
    /**
     * Context 1: Escapes data rendered in standard HTML body.
     */
    public static function html(?string $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8');
    }

    /**
     * Context 2: Escapes data inside HTML attribute values.
     */
    public static function attr(?string $value): string
    {
        return self::html($value);
    }

    /**
     * Context 3: Encodes PHP arrays/objects safely for inline <script> tags.
     * Prevents </script> breakouts and string injection vulnerabilities.
     */
    public static function jsJson(mixed $data): string
    {
        $flags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES;
        $json = json_encode($data, $flags);
        return $json !== false ? $json : '{}';
    }

    /**
     * Context 4: Sanitizes and neutralizes dangerous URL schemes.
     * Blocks javascript:, data:, and vbscript: execution vectors in href and src attributes.
     */
    public static function url(?string $url): string
    {
        if ($url === null || $url === '') {
            return '';
        }

        $trimmed = trim($url);
        // Disallow dangerous script schemes
        if (preg_match('/^\s*(javascript|data|vbscript):/i', $trimmed)) {
            return '#blocked-dangerous-scheme';
        }

        return self::attr($trimmed);
    }

    /**
     * Context 5: Sanitizes rich-text user HTML input while preserving safe formatting tags.
     * Supports HTMLPurifier if installed, or provides a robust DOMDocument fallback.
     */
    public static function sanitizeHtml(string $dirtyHtml): string
    {
        if (trim($dirtyHtml) === '') {
            return '';
        }

        // 1. Use HTMLPurifier if available
        if (class_exists('HTMLPurifier')) {
            static $purifier = null;
            if ($purifier === null) {
                $config = HTMLPurifier_Config::createDefault();
                $config->set('Core.Encoding', 'UTF-8');
                $config->set('HTML.Allowed', 'p,b,strong,i,em,u,a[href|title|target],ul,ol,li,br,h2,h3,h4,blockquote,img[src|alt|width|height]');
                $config->set('URI.AllowedSchemes', ['http' => true, 'https' => true, 'mailto' => true]);
                $config->set('HTML.TargetBlank', true);
                $config->set('Cache.SerializerPath', sys_get_temp_dir());
                $purifier = new HTMLPurifier($config);
            }
            return $purifier->purify($dirtyHtml);
        }

        // 2. Fallback: Native DOMDocument-based tag whitelist sanitizer
        $allowedTags = ['p', 'b', 'strong', 'i', 'em', 'u', 'a', 'ul', 'ol', 'li', 'br', 'h2', 'h3', 'h4', 'blockquote', 'img'];
        $allowedAttrs = [
            'a' => ['href', 'title', 'target'],
            'img' => ['src', 'alt', 'width', 'height']
        ];

        libxml_use_internal_errors(true);
        $dom = new DOMDocument('1.0', 'UTF-8');
        // Wrap with UTF-8 meta to avoid character encoding issues
        $dom->loadHTML('<?xml encoding="utf-8" ?><div>' . $dirtyHtml . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);
        $nodes = $xpath->query('//*');

        if ($nodes) {
            for ($i = $nodes->length - 1; $i >= 0; $i--) {
                $node = $nodes->item($i);
                if ($node === null || $node->nodeName === 'div') {
                    continue;
                }

                $tag = strtolower($node->nodeName);
                if (!in_array($tag, $allowedTags, true)) {
                    // Replace disallowed element with its safe text children
                    $fragment = $dom->createDocumentFragment();
                    while ($node->childNodes->length > 0) {
                        $fragment->appendChild($node->childNodes->item(0));
                    }
                    $node->parentNode->replaceChild($fragment, $node);
                    continue;
                }

                // Strip attributes that are not explicitly permitted
                if ($node->hasAttributes()) {
                    $attrList = [];
                    foreach ($node->attributes as $attr) {
                        $attrList[] = $attr->nodeName;
                    }
                    foreach ($attrList as $attrName) {
                        $attrLower = strtolower($attrName);
                        $permitted = $allowedAttrs[$tag] ?? [];
                        if (!in_array($attrLower, $permitted, true)) {
                            $node->removeAttribute($attrName);
                        } else {
                            // Check attribute values for script injection
                            $val = $node->getAttribute($attrName);
                            if (preg_match('/^\s*(javascript|data|vbscript):/i', $val)) {
                                $node->removeAttribute($attrName);
                            }
                        }
                    }
                }
            }
        }

        $cleanHtml = $dom->saveHTML($dom->getElementsByTagName('div')->item(0));
        // Remove the outer wrapping <div>...</div>
        return preg_replace('/^<div>(.*)<\/div>$/s', '$1', (string)$cleanHtml);
    }
}

// Global helper functions
if (!function_exists('e')) {
    function e(?string $str): string {
        return Escaper::html($str);
    }
}

if (!function_exists('e_attr')) {
    function e_attr(?string $str): string {
        return Escaper::attr($str);
    }
}

if (!function_exists('e_json')) {
    function e_json(mixed $data): string {
        return Escaper::jsJson($data);
    }
}

if (!function_exists('e_url')) {
    function e_url(?string $str): string {
        return Escaper::url($str);
    }
}

// Backward compatibility alias with existing codebase function h()
if (!function_exists('h')) {
    function h($s): string {
        return Escaper::html((string)$s);
    }
}
