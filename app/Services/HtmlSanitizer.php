<?php

namespace App\Services;

/**
 * Cleans the HTML an admin writes in the CMS editor (FAQs, Terms, Privacy) before it is stored and shown to visitors:
 * only simple formatting tags are kept, every attribute except a safe link href is dropped, and script/style/iframe/forms
 * (and javascript: links) never get through — even if someone pastes them in.
 */
class HtmlSanitizer
{
    private const ALLOWED_TAGS = [
        'p', 'br', 'strong', 'b', 'em', 'i', 'u', 's', 'ul', 'ol', 'li', 'h1', 'h2', 'h3', 'h4', 'blockquote', 'a', 'span', 'div',
        'table', 'thead', 'tbody', 'tr', 'th', 'td', 'hr',
    ];

    private const REMOVE_WITH_CONTENT = ['script', 'style', 'iframe', 'object', 'embed', 'form', 'input', 'button', 'textarea', 'select', 'link', 'meta', 'svg', 'math'];

    public static function clean(?string $html): string
    {
        $html = trim((string) $html);
        if ($html === '') {
            return '';
        }

        $doc = new \DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="UTF-8"><div id="cms-root">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $root = $doc->getElementById('cms-root');
        if (!$root) {
            return e(strip_tags($html));
        }
        self::walk($root);

        $out = '';
        foreach ($root->childNodes as $child) {
            $out .= $doc->saveHTML($child);
        }

        return trim($out);
    }

    private static function walk(\DOMNode $node): void
    {
        $children = [];
        foreach ($node->childNodes as $child) {
            $children[] = $child;
        }

        foreach ($children as $child) {
            if ($child instanceof \DOMComment) {
                $node->removeChild($child);
                continue;
            }
            if (!$child instanceof \DOMElement) {
                continue;
            }

            $tag = strtolower($child->tagName);
            if (in_array($tag, self::REMOVE_WITH_CONTENT, true)) {
                $node->removeChild($child);      // dangerous element: removed together with its content
                continue;
            }

            self::walk($child);

            if (!in_array($tag, self::ALLOWED_TAGS, true)) {
                // unknown tag: keep its (already cleaned) content, drop the tag itself
                while ($child->firstChild) {
                    $node->insertBefore($child->firstChild, $child);
                }
                $node->removeChild($child);
                continue;
            }

            foreach (iterator_to_array($child->attributes) as $attr) {
                $name = strtolower($attr->name);
                if ($tag === 'a' && $name === 'href') {
                    if (!preg_match('#^(https?://|mailto:|tel:|/|\#)#i', trim($attr->value))) {
                        $child->removeAttribute('href');
                    }
                } elseif ($tag === 'a' && $name === 'target') {
                    $child->setAttribute('rel', 'noopener');
                } elseif (!in_array($name, ['colspan', 'rowspan'], true)) {
                    $child->removeAttribute($attr->name);
                }
            }
        }
    }
}
