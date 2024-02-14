<?php

return [
    'accepted_file_types' => [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'image/svg+xml',
        'application/pdf',
        'application/msword', // .doc
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // .docx
        'application/vnd.ms-excel', // .xls
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx
        'application/vnd.ms-powerpoint', // .ppt
        'application/vnd.openxmlformats-officedocument.presentationml.presentation', // .pptx
        'application/zip',
        'application/x-rar-compressed', // .rar
        'application/x-tar',
        'application/x-gzip',
        'application/octet-stream', // generic binary file
        'text/plain', // plain text file
        'text/csv', // CSV file
        'audio/mpeg', // .mp3
        'audio/wav',
        'video/mp4', // .mp4
        'video/mpeg', // .mpeg
        'application/json',
        'application/xml',
        'application/vnd.ms-fontobject',
        'font/eot',
        'font/opentype',
        'font/otf',
        'font/ttf',
        'font/woff',
        'font/woff2',
        'application/vnd.visio', // .vsd
        'application/vnd.ms-excel.sheet.macroEnabled.12', // .xlsm
        'application/vnd.ms-powerpoint.presentation.macroEnabled.12', // .pptm
    ],
    'cloud_disks' => [
        's3',
        'cloudinary',
        'imgix',
    ],
    'curation_formats' => [
        'jpg',
        'jpeg',
        'webp',
        'png',
        'avif',
    ],
    'curation_presets' => [
        \Awcodes\Curator\Curations\ThumbnailPreset::class,
    ],
    'directory' => 'media',
    'disk' => env('FILAMENT_FILESYSTEM_DISK', 'public'),
    'glide' => [
        'server' => \Awcodes\Curator\Glide\DefaultServerFactory::class,
        'fallbacks' => [],
        'route_path' => 'curator',
    ],
    'image_crop_aspect_ratio' => null,
    'image_resize_mode' => null,
    'image_resize_target_height' => null,
    'image_resize_target_width' => null,
    'is_limited_to_directory' => false,
    'is_tenant_aware' => true,
    'tenant_ownership_relationship_name' => 'team',
    'max_size' => 5000,
    'model' => \App\Models\CustomMedia::class,
    'min_size' => 0,
    'path_generator' => null,
    'resources' => [
        'label' => 'Media',
        'plural_label' => 'Media',
        'navigation_group' => null,
        'navigation_icon' => 'heroicon-o-photo',
        'navigation_sort' => null,
        'navigation_count_badge' => false,
        'resource' => \Awcodes\Curator\Resources\MediaResource::class,
    ],
    'should_preserve_filenames' => false,
    'should_register_navigation' => true,
    'visibility' => 'public',
];
