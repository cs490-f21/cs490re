<?php
require_once (__DIR__ . '/pconfig.php');

/**
 * Locate or import (`require`) a template element by its name.
 * 
 * To import the template into current file, please set `$load = true`.
 */
function use_template(string|array $template_names, bool $load = false, bool $require_once = true) : string {
    $located = '';
    foreach ((array) $template_names as $template_name) {
        if (! $template_name) {
            continue;
        }
        if (file_exists(PARTIAL_DIR . '/' . $template_name)) {
            $located = PARTIAL_DIR . '/' . $template_name;
            break;
        } elseif (file_exists(PUBHTML_DIR . '/' . $template_name)) {
            $located = PUBHTML_DIR . '/' . $template_name;
            break;
        }
    }

    if ($load && '' !== $located) {
        load_template($located, $require_once);
    }

    return $located;
}

/**
 * Import a template file into current file.
 */
function load_template(string $template_file, bool $require_once = true) : void {
    if ($require_once) {
        require_once ($template_file);
    } else {
        require ($template_file);
    }
}
