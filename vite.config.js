import laravel, { refreshPaths } from "laravel-vite-plugin";
import { defineConfig } from "vite";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/filament/admin/theme.css",
                "resources/js/filament/admin/scroll-fix.js",
            ],
            refresh: [
                ...refreshPaths,
                "app/Filament/**",
                "app/Forms/Components/**",
                "app/Livewire/**",
                "app/Infolists/Components/**",
                "app/Providers/Filament/**",
                "app/Tables/Columns/**",
            ],
        }),
    ],
});
