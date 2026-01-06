<?php
namespace MarkNovel\Frontend;

class TemplateLoader {
    public function register() {
        add_filter('template_include', [$this, 'load_template']);
    }

    public function load_template($template) {
        // 1. Nếu đang xem chi tiết một TRUYỆN
        if (is_singular('novel')) {
            $new_template = MARK_NOVEL_PATH . 'templates/single-novel.php';
            if (file_exists($new_template)) {
                return $new_template;
            }
        }

        // 2. Nếu đang xem chi tiết một CHƯƠNG
        if (is_singular('chapter')) {
            $new_template = MARK_NOVEL_PATH . 'templates/single-chapter.php';
            if (file_exists($new_template)) {
                return $new_template;
            }
        }

        return $template; // Nếu không phải truyện/chương thì kệ nó, để Theme xử lý
    }
}