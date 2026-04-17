@php
    $baseVendor = base_path('vendor');
    $pattern = $baseVendor . '/**/guidelines/.ai/guidelines/*.md';
    $files = glob($pattern, GLOB_BRACE);

    // Group by package
    $grouped = [];
    foreach ($files as $file) {
        preg_match('/vendor\/([^\/]+\/[^\/]+)\/guidelines/', $file, $matches);
        $package = $matches[1] ?? 'unknown';
        $grouped[$package][] = $file;
    }
    ksort($grouped);
@endphp

@forelse ($grouped as $package => $files)
    # {{ $package }} Guidelines

    @foreach ($files as $file)
        {!! file_get_contents($file) !!}

    @endforeach

@endforelse