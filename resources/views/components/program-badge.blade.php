@props(['program'])

@php
$colors = [
    'academic'    => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
    'cooperative' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
    'summer'      => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
    'visitor'     => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
];
$color = $colors[$program->code ?? ''] ?? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {$color}"]) }}>
    {{ $program->name ?? 'غير محدد' }}
</span>
