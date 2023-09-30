<?php

dataset('paths', [
    'path one' => ['join', 'a', 'path', 'join/a/path'],
    'path two' => ['/this', 'is', '../weird/', '/this/is/../weird'],
]);

it('join paths', function (string ...$data) {
    expect(join_paths($data[0], $data[1], $data[2]))
        ->toBe($data[3]);
})->with('paths');
