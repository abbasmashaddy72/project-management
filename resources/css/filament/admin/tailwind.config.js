import preset from "../../../../vendor/filament/filament/tailwind.config.preset";

export default {
    presets: [preset],
    content: [
        "./node_modules/flowbite/**/*.js",
        "./app/Filament/**/*.php",
        "./app/Livewire/**/*.php",
        "./resources/views/**/*.blade.php",
        "./vendor/awcodes/filament-curator/resources/**/*.blade.php",
        "./vendor/awcodes/overlook/resources/**/*.blade.php",
        "./vendor/awcodes/filament-quick-create/resources/**/*.blade.php",
        "./vendor/awcodes/filament-versions/resources/**/*.blade.php",
        "./vendor/filament/**/*.blade.php",
    ],
    plugins: [require("flowbite/plugin")],
};
