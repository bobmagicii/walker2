<?php

list($AppRoot, $BootRoot) = (function(): array {
	if(Phar::Running(FALSE) !== '')
	return [ dirname(Phar::Running(FALSE)), Phar::Running(TRUE) ];

	return array_fill(0, 2, dirname(__FILE__, 2));
})();

require(join(
	DIRECTORY_SEPARATOR,
	[ $BootRoot, 'vendor', 'autoload.php' ]
));

exit(Walker\TerminalApp::Realboot([
	'AppRoot'  => Nether\Common\Filesystem\Util::Repath($AppRoot),
	'BootRoot' => $BootRoot
]));
