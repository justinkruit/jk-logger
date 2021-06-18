<?php

namespace WpTracy;

use Tracy\Debugger;

/**
 * Custom panel based on global $wp_user variable
 *
 * @author Martin Hlaváč
 */
class WpUserPanel extends WpPanelBase
{
    public function getTab()
    {
        if (is_user_logged_in()) {
            return parent::getSimpleTab(__("User"), null, null, '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!-- Font Awesome Pro 5.15.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) --><path d="M313.6 304c-28.7 0-42.5 16-89.6 16-47.1 0-60.8-16-89.6-16C60.2 304 0 364.2 0 438.4V464c0 26.5 21.5 48 48 48h352c26.5 0 48-21.5 48-48v-25.6c0-74.2-60.2-134.4-134.4-134.4zM400 464H48v-25.6c0-47.6 38.8-86.4 86.4-86.4 14.6 0 38.3 16 89.6 16 51.7 0 74.9-16 89.6-16 47.6 0 86.4 38.8 86.4 86.4V464zM224 288c79.5 0 144-64.5 144-144S303.5 0 224 0 80 64.5 80 144s64.5 144 144 144zm0-240c52.9 0 96 43.1 96 96s-43.1 96-96 96-96-43.1-96-96 43.1-96 96-96z"/></svg>');
        }
        return null;
    }

    public function getPanel()
    {
        $output = null;
        if (is_user_logged_in()) {
            $currentUser = wp_get_current_user();
            $output = parent::getTablePanel([
                __("ID") => $currentUser->ID,
                __("Login") => $currentUser->user_login,
                __("E-mail") => $currentUser->user_email,
                __("Display Name") => $currentUser->display_name,
                __("First Name") => $currentUser->first_name,
                __("Last Name") => $currentUser->last_name,
                __("Roles") => Debugger::dump($currentUser->roles, true),
                __("Allcaps") => Debugger::dump($currentUser->allcaps, true),
            ]);
        }
        return $output;
    }
}
