<?php

dataset('paths', [
    ['join', 'a', 'path', 'join/a/path'],
    ['/this', 'is', '../weird/', '/this/is/../weird'],
]);

it('join paths', function (string ...$data) {
    expect(join_paths($data[0], $data[1], $data[2]))
        ->toBe($data[3]);
})
    ->with('paths');

it('return absolute path', function () {
    $configFolder = join_paths(__DIR__, '..', '..', 'workbench', 'config');

    expect(absolute('one.php', $configFolder))
        ->toBe(join_paths($configFolder, 'one.php'));
});
