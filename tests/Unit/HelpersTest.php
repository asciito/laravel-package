<?php

dataset('paths', [
    'path one' => [
        'values' => ['join', 'a', 'path'],
        'toBe' => 'join/a/path',
    ],
    'path two' => [
        'values' => ['/this', 'is', '../weird/'],
        'toBe' => '/this/is/../weird',
    ],
]);

it('join paths', function (array $paths, string $result) {
    expect(join_paths(...$paths))->toBe($result);
})->with('paths');
