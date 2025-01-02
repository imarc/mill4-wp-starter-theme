<?php

function is_hmr(): bool
{
    // make sure we're in development and a .hot file exists in the theme folder.
    return wp_get_environment_type() === 'development' && file_exists(get_theme_file_path('.hot'));
}
